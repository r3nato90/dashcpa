<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Relatórios Detalhados";
$breadcrumb_active = "Relatórios";

// Verificação de segurança: Apenas Gerentes (Super e Sub) podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];
$message = "";

// Processamento de formulários (Filtros e Mensagens)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'saved') {
        $message = "<div class='alert alert-success mt-3'>Relatório salvo com sucesso!</div>";
    } elseif ($_GET['status'] == 'deleted') {
         $message = "<div class='alert alert-success mt-3'>Relatório salvo excluído com sucesso!</div>";
    }
}


// --- 1. Obter Filtros ---
$date_start = $_GET['date_start'] ?? date('Y-m-d');
$date_end = $_GET['date_end'] ?? date('Y-m-d');
$selected_user_ids = isset($_GET['user_ids']) && is_array($_GET['user_ids']) ? $_GET['user_ids'] : (isset($_GET['user_ids']) && !is_array($_GET['user_ids']) ? [$_GET['user_ids']] : []);
$selected_admin_id = $_GET['admin_id'] ?? '';
$loaded_report_name = $_GET['report_name'] ?? '';

// Carregar filtros de um relatório salvo
if (isset($_GET['load_report_id']) && is_numeric($_GET['load_report_id'])) {
    $report_id = $_GET['load_report_id'];
    $stmt_load = $pdo->prepare("SELECT * FROM relatorios_salvos WHERE id = ? AND id_admin = ?");
    $stmt_load->execute([$report_id, $id_logado]);
    $saved_report = $stmt_load->fetch(PDO::FETCH_ASSOC);

    if ($saved_report) {
        $date_start = $saved_report['date_start'];
        $date_end = $saved_report['date_end'];
        // Assume que user_ids e admin_id são armazenados como JSON no banco
        $selected_user_ids = json_decode($saved_report['user_ids'] ?? '[]', true);
        $selected_admin_id = $saved_report['admin_id'];
        $loaded_report_name = $saved_report['nome'];
        $message = "<div class='alert alert-info mt-3'>Filtros do relatório '{$loaded_report_name}' carregados.</div>";
    } else {
        $message = "<div class='alert alert-warning mt-3'>Relatório salvo não encontrado.</div>";
    }
}

// --- 2. Preparar Dados para Formulários ---

// A. Lista de Admins/Sub-Admins (para Super Admin)
$managers_options = [];
if ($role == 'super_adm') {
    // Super Admin lista todos Admin e Sub-Admin
    $stmt_managers = $pdo->query("SELECT id, nome, role FROM sub_administradores WHERE role IN ('admin', 'sub_adm') ORDER BY nome");
    $managers_options = $stmt_managers->fetchAll(PDO::FETCH_ASSOC);
}

// B. Lista de Operadores (Para todos os gerentes)
$users_options = [];
$users_query = "SELECT u.id, u.nome, s.nome AS nome_manager, s.role AS role_manager 
                FROM usuarios u
                LEFT JOIN sub_administradores s ON u.manager_id = s.id
                WHERE u.role = 'usuario'";
$user_params = [];
$user_where = [];

if ($role == 'admin' || $role == 'sub_adm') {
    // Admin/Sub-Admin veem apenas os seus usuários
    $manager_ids = [$id_logado]; 
    if ($role == 'admin') {
        // Inclui Sub-Admins para a filtragem de operadores (para Admin)
        $stmt_subadmins_ids = $pdo->prepare("SELECT id FROM sub_administradores WHERE manager_id = ? AND role = 'sub_adm'");
        $stmt_subadmins_ids->execute([$id_logado]);
        while ($row = $stmt_subadmins_ids->fetch(PDO::FETCH_ASSOC)) {
            $manager_ids[] = $row['id'];
        }
    }
    $manager_placeholders = implode(',', array_fill(0, count($manager_ids), '?'));
    $user_where[] = "u.manager_id IN ($manager_placeholders)";
    $user_params = array_merge($user_params, $manager_ids);
    
} elseif ($role == 'super_adm' && !empty($selected_admin_id)) {
    // Super Admin filtra por Admin/Sub-Admin
    $user_where[] = "u.manager_id = ?";
    $user_params[] = $selected_admin_id;
}

if (!empty($user_where)) {
    $users_query .= " AND " . implode(" AND ", $user_where);
}

$users_query .= " ORDER BY u.nome";
$stmt_users = $pdo->prepare($users_query);
$stmt_users->execute($user_params);
$users_options = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// --- 3. Query Principal do Relatório ---
$query = "SELECT r.*, u.nome AS nome_usuario, u.percentual_comissao AS user_rate 
          FROM relatorios r 
          JOIN usuarios u ON r.id_usuario = u.id
          WHERE 1=1"; // Começa com 1=1 para facilitar a adição de cláusulas

$params = [];

// Filtro de Data
if (!empty($date_start) && !empty($date_end)) {
    $date_end_query = $date_end . ' 23:59:59';
    $query .= " AND (r.data BETWEEN ? AND ?)";
    $params[] = $date_start;
    $params[] = $date_end_query;
}

// Filtro por Gerência (para Admin/Sub-Admin)
$allowed_user_ids_for_logado = [];
if ($role == 'admin' || $role == 'sub_adm') {
    // Se Admin/Sub-Admin, garante que só vê os seus (usa a lista de users_options calculada acima)
    $allowed_user_ids_for_logado = array_column($users_options, 'id');

    if (empty($allowed_user_ids_for_logado)) {
        // Se não há usuários gerenciados, o relatório estará vazio.
        $relatorios = [];
        $totais = ['deposito' => 0, 'saque' => 0, 'bau' => 0, 'lucro' => 0, 'comissao_user' => 0, 'comissao_sub' => 0, 'comissao_admin' => 0];
        $display_table = false;
        goto display_page; // Pula a execução da query principal
    }
    
    // Filtra o relatório pelos IDs de usuários permitidos
    $in_placeholders = implode(',', array_fill(0, count($allowed_user_ids_for_logado), '?'));
    $query .= " AND r.id_usuario IN ($in_placeholders)";
    $params = array_merge($params, $allowed_user_ids_for_logado);

} 
// Filtro por Admin específico (apenas Super Admin usa)
elseif ($role == 'super_adm' && !empty($selected_admin_id)) {
    // Super Admin filtra por manager_id (o Admin ou Sub-Admin selecionado)
    $query .= " AND u.manager_id = ?";
    $params[] = $selected_admin_id;
}


// Filtro por Usuários Específicos
if (!empty($selected_user_ids)) {
    // Garante que o usuário logado (Admin/Sub-Admin) só pode filtrar entre os seus
    if ($role != 'super_adm') {
        $selected_user_ids = array_intersect($selected_user_ids, $allowed_user_ids_for_logado);
    }
    
    if (!empty($selected_user_ids)) {
        $in_placeholders = implode(',', array_fill(0, count($selected_user_ids), '?'));
        $query .= " AND r.id_usuario IN ($in_placeholders)";
        $params = array_merge($params, $selected_user_ids);
    } else {
        // Se a interseção for vazia (Admin tentou filtrar usuário que não é dele)
        $relatorios = [];
        $totais = ['deposito' => 0, 'saque' => 0, 'bau' => 0, 'lucro' => 0, 'comissao_user' => 0, 'comissao_sub' => 0, 'comissao_admin' => 0];
        $display_table = false;
        goto display_page;
    }
}


$query .= " ORDER BY r.data DESC";
$stmt_report = $pdo->prepare($query);
$stmt_report->execute($params);
$relatorios = $stmt_report->fetchAll(PDO::FETCH_ASSOC);

// --- 4. Calcular Totais ---
$totais = ['deposito' => 0, 'saque' => 0, 'bau' => 0, 'lucro' => 0, 'comissao_user' => 0, 'comissao_sub' => 0, 'comissao_admin' => 0];
foreach ($relatorios as $r) {
    $totais['deposito'] += $r['valor_deposito'];
    $totais['saque'] += $r['valor_saque'];
    $totais['bau'] += $r['valor_bau'];
    $totais['lucro'] += $r['lucro_diario'];
    $totais['comissao_user'] += $r['comissao_usuario'];
    $totais['comissao_sub'] += $r['comissao_sub_adm'];
    $totais['comissao_admin'] += $r['comissao_admin'];
}
$display_table = true;

display_page: // Label para o goto


// --- URL para Exportação ---
$export_url_params = http_build_query([
    'date_start' => $date_start,
    'date_end' => $date_end,
    'user_ids' => $selected_user_ids,
    'admin_id' => $selected_admin_id
]);

include('header.php'); 
?>

<h2 class="mb-4">Relatórios Detalhados de Operações</h2>

<?php echo $message; // Exibe feedback de status ?>

<!-- Card de Filtros -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Filtros de Relatório <?php echo !empty($loaded_report_name) ? " - Relatório Carregado: **" . htmlspecialchars($loaded_report_name) . "**" : ""; ?></h5>
    </div>
    <div class="card-body">
        <form id="filterForm" method="GET" action="reports.php">
            <input type="hidden" name="load_report_id" value="">
            <div class="row g-3">
                <!-- Filtro de Data Início -->
                <div class="col-md-3">
                    <label for="date_start" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="date_start" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>" required>
                </div>

                <!-- Filtro de Data Fim -->
                <div class="col-md-3">
                    <label for="date_end" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="date_end" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>" required>
                </div>
                
                <!-- Filtro por Admin (Apenas Super Admin) -->
                <?php if ($role == 'super_adm'): ?>
                <div class="col-md-3">
                    <label for="admin_id" class="form-label">Filtrar por Gerente</label>
                    <select class="form-select" id="admin_id" name="admin_id">
                        <option value="">Todos os Gerentes</option>
                        <?php foreach ($managers_options as $manager): ?>
                            <option value="<?php echo $manager['id']; ?>" 
                                    <?php echo ($selected_admin_id == $manager['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($manager['nome'] . ' (' . $manager['role'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Filtro por Operador (Todos os Gerentes) -->
                <div class="col-md-3">
                    <label for="user_ids" class="form-label">Filtrar por Operador</label>
                    <select class="form-select" id="user_ids" name="user_ids[]" multiple size="1">
                        <option value="">Todos os Operadores</option>
                        <?php foreach ($users_options as $user): ?>
                            <option value="<?php echo $user['id']; ?>" 
                                    <?php echo in_array($user['id'], $selected_user_ids) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($user['nome']) . " (Gerente: " . htmlspecialchars($user['nome_manager']) . ")"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Mantenha Ctrl/Cmd pressionado para selecionar múltiplos.</div>
                </div>

                <!-- Botão de Filtrar -->
                <div class="col-12 mt-4 d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i> Aplicar Filtros
                    </button>
                    
                    <!-- Botão Salvar Relatório (Modal Trigger) -->
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#saveReportModal">
                        <i class="fas fa-save me-2"></i> Salvar Filtro
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Salvar Relatório -->
<div class="modal fade" id="saveReportModal" tabindex="-1" aria-labelledby="saveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="save_report.php">
                <input type="hidden" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>">
                <input type="hidden" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>">
                <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($selected_admin_id); ?>">
                
                <?php // Repassa os IDs de usuário selecionados
                foreach ($selected_user_ids as $id) {
                    echo '<input type="hidden" name="user_ids[]" value="' . htmlspecialchars($id) . '">';
                }
                ?>
                
                <div class="modal-header">
                    <h5 class="modal-title" id="saveReportModalLabel">Salvar Filtro de Relatório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report_name" class="form-label">Nome do Relatório</label>
                        <input type="text" class="form-control" id="report_name" name="report_name" placeholder="Ex: Relatório Semanal Setembro" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Cards de Totais (Resumo) -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-secondary text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-white">Depósito Total</h5>
                <h3 class="mb-0">R$ <?php echo number_format($totais['deposito'], 2, ',', '.'); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-white">Saque Total</h5>
                <h3 class="mb-0">R$ <?php echo number_format($totais['saque'], 2, ',', '.'); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-white">Baú Total</h5>
                <h3 class="mb-0">R$ <?php echo number_format($totais['bau'], 2, ',', '.'); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-white">Lucro Bruto Total</h5>
                <h3 class="mb-0">R$ <?php echo number_format($totais['lucro'], 2, ',', '.'); ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Opções de Exportação -->
<div class="mb-4 text-end">
    <div class="btn-group" role="group" aria-label="Opções de Exportação">
        <a href="export_excel.php?<?php echo $export_url_params; ?>" class="btn btn-outline-success">
            <i class="fas fa-file-excel me-1"></i> Exportar para Excel
        </a>
        <a href="export_pdf.php?<?php echo $export_url_params; ?>" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf me-1"></i> Exportar para PDF
        </a>
        <a href="export_html.php?<?php echo $export_url_params; ?>" class="btn btn-outline-info">
            <i class="fas fa-file-code me-1"></i> Exportar para HTML
        </a>
    </div>
</div>

<!-- Tabela de Detalhes -->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Detalhes das Transações (<?php echo count($relatorios); ?> Registros)</h5>
    </div>
    <div class="card-body table-responsive">
        <?php if ($display_table && !empty($relatorios)): ?>
        <table class="table table-striped table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Data/Hora</th>
                    <th>Operador</th>
                    <th class="text-end">Depósito</th>
                    <th class="text-end">Saque</th>
                    <th class="text-end">Baú</th>
                    <th class="text-end text-success">Lucro Bruto</th>
                    <th class="text-end">Com. Operador (<?php echo number_format($relatorios[0]['user_rate'], 0, ',', '.'); ?>%)</th>
                    <th class="text-end">Com. Gerente (10%)</th>
                    <th class="text-end">Com. Admin (50%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($relatorios as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['id_relatorio']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($r['data'])); ?></td>
                    <td><?php echo htmlspecialchars($r['nome_usuario']); ?></td>
                    <td class="text-end">R$ <?php echo number_format($r['valor_deposito'], 2, ',', '.'); ?></td>
                    <td class="text-end">R$ <?php echo number_format($r['valor_saque'], 2, ',', '.'); ?></td>
                    <td class="text-end">R$ <?php echo number_format($r['valor_bau'], 2, ',', '.'); ?></td>
                    <td class="text-end text-success">R$ <?php echo number_format($r['lucro_diario'], 2, ',', '.'); ?></td>
                    <td class="text-end">R$ <?php echo number_format($r['comissao_usuario'], 2, ',', '.'); ?></td>
                    <td class="text-end">R$ <?php echo number_format($r['comissao_sub_adm'], 2, ',', '.'); ?></td>
                    <td class="text-end">R$ <?php echo number_format($r['comissao_admin'], 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
             <tfoot>
                <tr class="table-info">
                    <th colspan="3" class="text-end">SOMA TOTAL:</th>
                    <th class="text-end">R$ <?php echo number_format($totais['deposito'], 2, ',', '.'); ?></th>
                    <th class="text-end">R$ <?php echo number_format($totais['saque'], 2, ',', '.'); ?></th>
                    <th class="text-end">R$ <?php echo number_format($totais['bau'], 2, ',', '.'); ?></th>
                    <th class="text-end text-success">R$ <?php echo number_format($totais['lucro'], 2, ',', '.'); ?></th>
                    <th class="text-end text-success">R$ <?php echo number_format($totais['comissao_user'], 2, ',', '.'); ?></th>
                    <th class="text-end text-success">R$ <?php echo number_format($totais['comissao_sub'], 2, ',', '.'); ?></th>
                    <th class="text-end text-success">R$ <?php echo number_format($totais['comissao_admin'], 2, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">Nenhum relatório encontrado com os filtros selecionados.</div>
        <?php endif; ?>
    </div>
</div>

<?php include('footer.php'); ?>