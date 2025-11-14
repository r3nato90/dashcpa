<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

// Apenas Gerentes (todos os níveis) podem ver esta página
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

// --- Inicialização de Variáveis e Filtros ---
$usuarios_list = []; 
$admins_list = []; 
$relatorios = [];
$filtros_aplicados = [
    'date_start' => date('Y-m-d'), 'date_end' => date('Y-m-d'),
    'user_ids' => [], 'admin_id' => '' 
];
// --- Carregar Filtros (salvos ou POST) ---
if (isset($_GET['report_id'])) {
    // Carrega filtros de saved_reports
    $report_id = (int)$_GET['report_id'];
    $query_saved = "SELECT * FROM saved_reports WHERE id_report_salvo = ?";
    if ($role == 'sub_adm' || $role == 'admin') {
        $query_saved .= " AND id_salvo_por = " . $id_logado; 
    }
    $stmt_saved = $pdo->prepare($query_saved);
    $stmt_saved->execute([$report_id]);
    $saved_report = $stmt_saved->fetch();
    if ($saved_report) {
        $filtros_aplicados = json_decode($saved_report['filtros'], true);
    }
}
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'filtrar') {
    // Carrega filtros do formulário
    $filtros_aplicados['date_start'] = $_POST['date_start'];
    $filtros_aplicados['date_end'] = $_POST['date_end'];
    $filtros_aplicados['user_ids'] = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];
    if ($role == 'super_adm' && isset($_POST['admin_id'])) {
        $filtros_aplicados['admin_id'] = $_POST['admin_id'];
    }
}
// --- Listar Usuários e Gerentes para os Filtros ---
if ($role == 'super_adm') {
    $stmt_admins = $pdo->query("SELECT id_sub_adm, nome, username FROM sub_administradores ORDER BY nome");
    $admins_list = $stmt_admins->fetchAll();
    $query_users = "SELECT id_usuario, nome FROM usuarios";
    $params_users = [];
    if (!empty($filtros_aplicados['admin_id'])) {
        $query_users .= " WHERE id_sub_adm = ?";
        $params_users[] = $filtros_aplicados['admin_id'];
    }
    $query_users .= " ORDER BY nome";
    $stmt_users = $pdo->prepare($query_users);
    $stmt_users->execute($params_users);
    $usuarios_list = $stmt_users->fetchAll();
} else {
    $stmt_users = $pdo->prepare("SELECT id_usuario, nome FROM usuarios WHERE id_sub_adm = ? ORDER BY nome");
    $stmt_users->execute([$id_logado]);
    $usuarios_list = $stmt_users->fetchAll();
}

// --- Construção da Query do Relatório ---
$query = "SELECT r.*, u.nome AS nome_usuario FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario WHERE (r.data BETWEEN ? AND ?)";
// Correção: A data final deve incluir o dia todo (até 23:59:59)
$date_end_query = $filtros_aplicados['date_end'] . ' 23:59:59';
$params = [$filtros_aplicados['date_start'], $date_end_query];


if ($role == 'admin' || $role == 'sub_adm') {
    $query .= " AND u.id_sub_adm = ?"; $params[] = $id_logado;
}
if ($role == 'super_adm' && !empty($filtros_aplicados['admin_id'])) {
    $query .= " AND u.id_sub_adm = ?"; $params[] = $filtros_aplicados['admin_id'];
}
if (!empty($filtros_aplicados['user_ids'])) {
    // Garantir que user_ids seja um array para http_build_query e a query IN
    if (!is_array($filtros_aplicados['user_ids'])) {
         $filtros_aplicados['user_ids'] = [$filtros_aplicados['user_ids']];
    }
    $in_placeholders = implode(',', array_fill(0, count($filtros_aplicados['user_ids']), '?'));
    $query .= " AND r.id_usuario IN ($in_placeholders)"; $params = array_merge($params, $filtros_aplicados['user_ids']);
}
$query .= " ORDER BY r.data DESC";
$stmt_report = $pdo->prepare($query);
$stmt_report->execute($params);
$relatorios = $stmt_report->fetchAll();

// --- Cálculo dos Totais (ADICIONADO comissao_admin) ---
$totais = ['deposito' => 0, 'saque' => 0, 'bau' => 0, 'lucro' => 0, 'comissao_user' => 0, 'comissao_sub' => 0, 'comissao_admin' => 0];
foreach ($relatorios as $r) {
    $totais['deposito'] += $r['valor_deposito']; $totais['saque'] += $r['valor_saque']; $totais['bau'] += $r['valor_bau'];
    $totais['lucro'] += $r['lucro_diario']; $totais['comissao_user'] += $r['comissao_usuario']; $totais['comissao_sub'] += $r['comissao_sub_adm'];
    $totais['comissao_admin'] += $r['comissao_admin'];
}

// Cálculo de Métricas Adicionais para os Cards
$total_saque_bau = $totais['saque'] + $totais['bau'];
// Evita divisão por zero se a data de início e fim for a mesma
$dias_diff = (new DateTime($filtros_aplicados['date_start']))->diff(new DateTime($filtros_aplicados['date_end']))->days;
$dias_filtrados = $dias_diff + 1;
$media_diaria = ($dias_filtrados > 0 && $totais['lucro'] != 0) ? $totais['lucro'] / $dias_filtrados : $totais['lucro'];
$registros = count($relatorios);

// Geração do link de exportação com os filtros atuais
$export_query_params = [
    'date_start' => $filtros_aplicados['date_start'],
    'date_end' => $filtros_aplicados['date_end'],
    'admin_id' => $filtros_aplicados['admin_id']
];
// Adiciona user_ids ao array para http_build_query (suporta múltiplos)
if (!empty($filtros_aplicados['user_ids'])) {
    $export_query_params['user_ids'] = $filtros_aplicados['user_ids'];
}
$export_query = http_build_query($export_query_params);

include('templates/header.php'); // Inclui o header com sidebar
?>

<!-- Cabeçalho (da Imagem) -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Relatórios e Análises</h1>
        <p class="text-muted-foreground">Visualize o desempenho das suas operações.</p>
    </div>
</div>

<!-- Cards de Métricas (Layout Horizontal) -->
<h3 class="text-2xl font-semibold leading-none tracking-tight mb-4">Métricas do Período Selecionado</h3>
<!-- As classes 'grid', 'md:grid-cols-2' e 'lg:grid-cols-4' garantem que as caixas fiquem na horizontal em telas maiores -->
<div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
    
    <!-- Total Investido -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-blue-500">
        <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h3 class="tracking-tight text-sm font-medium">Total Investido (Depósito)</h3>
            <i class="fas fa-dollar-sign h-4 w-4 text-blue-600"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold text-blue-600">R$ <?php echo number_format($totais['deposito'], 2, ',', '.'); ?></div>
        </div>
    </div>
    
    <!-- Total Saque + Baú -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-purple-500">
        <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h3 class="tracking-tight text-sm font-medium">Total Saque + Baú</h3>
            <i class="fas fa-wallet h-4 w-4 text-purple-600"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold text-purple-600">R$ <?php echo number_format($total_saque_bau, 2, ',', '.'); ?></div>
        </div>
    </div>
    
    <!-- Lucro Total (Período) -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-green-500">
        <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h3 class="tracking-tight text-sm font-medium">Lucro Total (Período)</h3>
            <i class="fas fa-chart-line h-4 w-4 text-green-600"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold text-green-600">R$ <?php echo number_format($totais['lucro'], 2, ',', '.'); ?></div>
        </div>
    </div>

    <!-- Média Diária (Período) -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-orange-500">
        <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
            <h3 class="tracking-tight text-sm font-medium">Média Diária (Período)</h3>
            <i class="fas fa-calendar-day h-4 w-4 text-orange-600"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold text-orange-600">R$ <?php echo number_format($media_diaria, 2, ',', '.'); ?></div>
        </div>
    </div>
</div>

<!-- Botões de Filtro Rápido (com OnClick) -->
<div class="mb-4 d-flex gap-2">
    <button type="button" class="btn btn-secondary" onclick="setDateFilter('30days')">30 Dias</button>
    <button type="button" class="btn btn-outline-secondary" onclick="setDateFilter('month')">Mês Atual</button>
    <button type="button" class="btn btn-outline-secondary" onclick="setDateFilter('year')">Ano</button>
    
    <!-- ms-auto joga os botões de exportação para a direita -->

</div>


<!-- Filtros Detalhados (com IDs) -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3 class="text-lg font-semibold tracking-tight">Filtro Detalhado</h3>
    </div>
    <div class="card-body">
        <form id="filterForm" action="reports.php" method="POST">
            <input type="hidden" name="action" value="filtrar">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="date_start" class="form-label">Data Início</label>
                    <input type="date" id="date_start" class="form-control" name="date_start" value="<?php echo htmlspecialchars($filtros_aplicados['date_start']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="date_end" class="form-label">Data Fim</label>
                    <input type="date" id="date_end" class="form-control" name="date_end" value="<?php echo htmlspecialchars($filtros_aplicados['date_end']); ?>" required>
                </div>
                <?php if ($role == 'super_adm'): ?>
                <div class="col-md-3">
                    <label for="admin_id" class="form-label">Filtrar por Gerente</label>
                    <select name="admin_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($admins_list as $admin): ?>
                        <option value="<?php echo $admin['id_sub_adm']; ?>" <?php echo ($filtros_aplicados['admin_id'] == $admin['id_sub_adm']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($admin['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <label for="user_ids" class="form-label"><?php echo ($role == 'super_adm') ? 'Filtrar por Operador' : 'Seus Operadores'; ?></label>
                    <select name="user_ids[]" id="user_ids" class="form-control" multiple style="height: 100px;">
                        <?php foreach ($usuarios_list as $usuario): ?>
                        <option value="<?php echo $usuario['id_usuario']; ?>" <?php echo (is_array($filtros_aplicados['user_ids']) && in_array($usuario['id_usuario'], $filtros_aplicados['user_ids'])) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($usuario['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Aplicar Filtro</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Resultados -->
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="text-lg font-semibold tracking-tight">Resultados Detalhados (<?php echo $registros; ?> registros)</h3>
        <?php 
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'report_updated') { echo "<span class='alert alert-success py-1 ms-3'>Relatório atualizado!</span>"; }
            if ($_GET['status'] == 'report_deleted') { echo "<span class='alert alert-success py-1 ms-3'>Relatório apagado!</span>"; }
        }
        ?>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-hover table-sm">
            <thead class="table-dark">
                <tr>
                    <th>Data</th>
                    <th>Operador</th>
                    <th>Depósito</th>
                    <th>Saque</th>
                    <th>Baú</th>
                    <th>Lucro Bruto</th>
                    <th>Com. Operador</th>
                    <th>Com. Gerente</th>
                    <th>Com. Admin</th>
                    <th>Ações</th> 
                </tr>
            </thead>
            <tbody>
                <?php if (empty($relatorios)): ?>
                    <tr><td colspan="10" class="text-center">Nenhum relatório encontrado para este período.</td></tr>
                <?php endif; ?>
                <?php foreach ($relatorios as $r): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($r['data'])); ?></td>
                    <td><?php echo htmlspecialchars($r['nome_usuario']); ?></td>
                    <td>R$ <?php echo number_format($r['valor_deposito'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($r['valor_saque'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($r['valor_bau'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($r['lucro_diario'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($r['comissao_usuario'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($r['comissao_sub_adm'], 2, ',', '.'); ?></td>
                    <td>R$ <?php echo number_format($r['comissao_admin'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="edit_report_entry.php?id=<?php echo $r['id_relatorio']; ?>" class="btn btn-warning btn-sm" title="Editar">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a href="delete_report_entry.php?id=<?php echo $r['id_relatorio']; ?>" class="btn btn-danger btn-sm" title="Apagar" onclick="return confirm('Tem certeza que deseja apagar esta linha de relatório?');">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-dark">
                <tr>
                    <th colspan="2" class="text-end">SOMA TOTAL:</th>
                    <th>R$ <?php echo number_format($totais['deposito'], 2, ',', '.'); ?></th>
                    <th>R$ <?php echo number_format($totais['saque'], 2, ',', '.'); ?></th>
                    <th>R$ <?php echo number_format($totais['bau'], 2, ',', '.'); ?></th>
                    <th>R$ <?php echo number_format($totais['lucro'], 2, ',', '.'); ?></th>
                    <th>R$ <?php echo number_format($totais['comissao_user'], 2, ',', '.'); ?></th>
                    <th>R$ <?php echo number_format($totais['comissao_sub'], 2, ',', '.'); ?></th>
                    <th>R$ <?php echo number_format($totais['comissao_admin'], 2, ',', '.'); ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Salvar Relatório -->
 <div class="card shadow-sm mb-4 mt-4">
    <div class="card-header">
         <h3 class="text-lg font-semibold tracking-tight">Salvar Filtros Atuais</h3>
    </div>
    <div class="card-body">
        <form action="save_report.php" method="POST">
            <input type="hidden" name="filtros_json" value="<?php echo htmlspecialchars(json_encode($filtros_aplicados)); ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="nome_relatorio" class="form-label">Nome para Salvar</label>
                    <input type="text" class="form-control" name="nome_relatorio" placeholder="Ex: Relatório Outubro Semana 1" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Salvar Relatório</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include('templates/footer.php'); ?>