<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');
include('config/logger.php'); // Incluído

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin' || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$id_admin_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') $message = "<div class='alert alert-success'>Relatório enviado com sucesso!</div>";
    elseif ($_GET['status'] == 'error_invalid_input') $message = "<div class='alert alert-danger'>Erro: Verifique os dados ou selecione um usuário.</div>";
}

$stmt_admin_user = $pdo->prepare("SELECT username FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ?"); 
$stmt_admin_user->execute([$id_admin_logado, $org_id]);
$admin_user = $stmt_admin_user->fetch();
$admin_username = $admin_user ? $admin_user['username'] : '';

// **** CORREÇÃO: Busca usuários (N3) vinculados aos Sub-Admins (N2) DO Admin (N1) E usuários (N3) vinculados DIRETAMENTE ao Admin (N1) ****
$stmt_linked_users = $pdo->prepare("
    SELECT id_usuario, nome 
    FROM usuarios 
    WHERE org_id = ? 
    AND (
        id_sub_adm = ? 
        OR id_sub_adm IN (SELECT id_sub_adm FROM sub_administradores WHERE parent_admin_id = ?)
    )
    ORDER BY nome
");
$stmt_linked_users->execute([$org_id, $id_admin_logado, $id_admin_logado]);
$linked_users = $stmt_linked_users->fetchAll();
$hoje = date('Y-m-d');


// --- 1. QUERIES PARA KPIs (FILTRADAS PARA A HIERARQUIA DESTE ADMIN) ---
$params_kpi = [$id_admin_logado, $id_admin_logado, $org_id];

// Sub-Query para encontrar todos os IDs de Gerentes (N2) e o próprio Admin (N1)
$sub_query_admins = "SELECT id_sub_adm FROM sub_administradores WHERE parent_admin_id = ? OR id_sub_adm = ?";

// Lucro total gerado pelos seus operadores
$stmt_total_lucro = $pdo->prepare("
    SELECT SUM(r.lucro_diario) 
    FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario 
    WHERE u.id_sub_adm IN ($sub_query_admins) AND r.org_id = ?
");
$stmt_total_lucro->execute($params_kpi);
$total_lucro_admin = $stmt_total_lucro->fetchColumn() ?? 0;

// Sua comissão (N1)
$stmt_total_comissao_admin = $pdo->prepare("
    SELECT SUM(r.comissao_admin) 
    FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario 
    WHERE u.id_sub_adm IN ($sub_query_admins) AND r.org_id = ?
");
$stmt_total_comissao_admin->execute($params_kpi);
$total_comissao_admin = $stmt_total_comissao_admin->fetchColumn() ?? 0;

// Comissão dos seus Gerentes (N2)
$stmt_total_comissao_gerentes = $pdo->prepare("
    SELECT SUM(r.comissao_sub_adm) 
    FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario 
    WHERE u.id_sub_adm IN ($sub_query_admins) AND r.org_id = ?
");
$stmt_total_comissao_gerentes->execute($params_kpi);
$total_comissao_gerentes_admin = $stmt_total_comissao_gerentes->fetchColumn() ?? 0;

// Comissão dos seus operadores (N3)
$stmt_total_comissao_users = $pdo->prepare("
    SELECT SUM(r.comissao_usuario) 
    FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario 
    WHERE u.id_sub_adm IN ($sub_query_admins) AND r.org_id = ?
");
$stmt_total_comissao_users->execute($params_kpi);
$total_comissao_usuarios_admin = $stmt_total_comissao_users->fetchColumn() ?? 0;


// --- 2. GRÁFICO DE LINHA (FILTRADO PARA HIERARQUIA) ---
$stmt_line_chart = $pdo->prepare("
    SELECT DATE(r.data) as dia, SUM(r.lucro_diario) as lucro_total
    FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.data >= CURDATE() - INTERVAL 7 DAY 
    AND u.id_sub_adm IN ($sub_query_admins) 
    AND r.org_id = ?
    GROUP BY dia ORDER BY dia ASC
");
$stmt_line_chart->execute($params_kpi);
$line_chart_data = $stmt_line_chart->fetchAll(PDO::FETCH_ASSOC);

$chart_labels = []; $chart_lucro = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('d/m', strtotime($date));
    $chart_lucro[$date] = 0;
}
foreach ($line_chart_data as $row) {
    $chart_lucro[$row['dia']] = $row['lucro_total'];
}


include('templates/header-new.php'); 
?>

<div class="container-fluid">
    
    <div class="alert alert-info shadow-sm">
        <strong>Seu link de registro de usuário (N3):</strong><br>
        <input type="text" class="form-control form-control-sm mt-1" value="<?php echo $site_url; ?>/register_user.php?ref=<?php echo $admin_username; ?>" readonly>
        <small class="form-text">Use este link para cadastrar usuários (N3) diretos. Para cadastrar Sub-Admins (N2), use o "Criar Conta".</small>
    </div>

    <?php echo $message; ?>

    <div class="row">
        <div class="col-lg-5 col-md-12">
            <h3>Enviar Relatório (Admin)</h3>
            
            <div class="card shadow-sm mb-4"><div class="card-body">
                <form action="process_transaction.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario_id" class="form-label">Selecione o Usuário Vinculado (Seu Time)</label>
                        <select class="form-control" name="usuario_id" required>
                            <option value="">Escolha um usuário...</option>
                            <?php foreach ($linked_users as $user) echo "<option value='{$user['id_usuario']}'>" . htmlspecialchars($user['nome']) . "</option>"; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="data_relatorio" class="form-label">Data do Relatório</label>
                        <input type="date" class="form-control" name="data_relatorio" value="<?php echo $hoje; ?>" required>
                    </div>
                    <div class="mb-3"><label for="deposito" class="form-label">DEPÓSITO</label><input type="number" step="0.01" class="form-control" name="deposito" required></div>
                    <div class="mb-3"><label for="saque" class="form-label">SAQUE</label><input type="number" step="0.01" class="form-control" name="saque" required></div>
                    <div class="mb-3"><label for="bau" class="form-label">BAÚ (Saldo Final)</label><input type="number" step="0.01" class="form-control" name="bau" required></div>
                    <button type="submit" class="btn btn-success w-100" <?php echo (empty($linked_users)) ? 'disabled' : ''; ?>>
                        <?php echo (empty($linked_users)) ? 'Vincule um usuário primeiro' : 'Enviar Relatório'; ?>
                    </button>
                </form>
            </div></div>
        </div>
        
        <div class="col-lg-7 col-md-12">

            <div class="row mb-4">
                <div class="col-sm-6 mb-3">
                    <div class="card kpi-card shadow-sm h-100">
                        <div class="card-body">
                            <div class="kpi-icon bg-success-soft"><i class="fas fa-dollar-sign"></i></div>
                            <div>
                                <h6 class="text-muted mb-1">Lucro Total (Seu Time N2+N3)</h6>
                                <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_lucro_admin, 2, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="card kpi-card shadow-sm h-100">
                        <div class="card-body">
                            <div class="kpi-icon bg-warning-soft"><i class="fas fa-percentage"></i></div>
                            <div>
                                <h6 class="text-muted mb-1">Sua Comissão (N1)</h6>
                                <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_comissao_admin, 2, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="card kpi-card shadow-sm h-100">
                        <div class="card-body">
                            <div class="kpi-icon bg-info-soft"><i class="fas fa-user-shield"></i></div>
                            <div>
                                <h6 class="text-muted mb-1">Comissão Gerentes (N2)</h6>
                                <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_comissao_gerentes_admin, 2, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                     <div class="card kpi-card shadow-sm h-100">
                        <div class="card-body">
                            <div class="kpi-icon bg-danger-soft text-danger"><i class="fas fa-users"></i></div>
                            <div>
                                <h6 class="text-muted mb-1">Comissão Operadores (N3)</h6>
                                <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_comissao_usuarios_admin, 2, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm h-100 mb-4">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribuição (Sua Hierarquia)</h5></div>
                <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 250px;">
                    <canvas id="comissaoDoughnutChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
            
            <h3>Relatórios dos Seus Usuários</h3>
            <form action="dashboard_admin.php" method="POST" class="mb-3 p-3 border rounded bg-light shadow-sm">
                <input type="hidden" name="action" value="filtrar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4"><label for="date_start" class="form-label small mb-1">De:</label><input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>"></div>
                    <div class="col-md-4"><label for="date_end" class="form-label small mb-1">Até:</label><input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>"></div>
                    <div class="col-md-4"><label for="user_id_filter" class="form-label small mb-1">Filtrar Usuário (N3):</label>
                         <select name="user_id_filter" id="user_id_filter" class="form-control form-control-sm">
                            <option value="">Todos os Usuários</option>
                            <?php foreach ($linked_users as $user): ?>
                                <option value="<?php echo $user['id_usuario']; ?>" <?php echo ($user['id_usuario'] == $selected_user_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mt-2"><button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button></div>
                </div>
            </form>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuário (N3)</th><th>Data</th>
                                    <th>Lucro Total</th>
                                    <th>Com. User (N3)</th>
                                    <th>Com. Gerente (N2)</th>
                                    <th>Sua Com. (N1)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // **** CORREÇÃO (Hierarquia N1->N2->N3) ****
                                    $query_reports = "
                                        SELECT r.*, u.nome AS nome_usuario 
                                        FROM relatorios r 
                                        JOIN usuarios u ON r.id_usuario = u.id_usuario
                                        WHERE r.org_id = ? 
                                        AND (r.data BETWEEN ? AND ?)
                                        AND (
                                            u.id_sub_adm = ? 
                                            OR u.id_sub_adm IN (SELECT id_sub_adm FROM sub_administradores WHERE parent_admin_id = ?)
                                        )
                                    ";
                                    $params_reports = [$org_id, $date_start, $date_end_query, $id_admin_logado, $id_admin_logado];

                                    if (!empty($selected_user_id)) {
                                        $query_reports .= " AND r.id_usuario = ?";
                                        $params_reports[] = $selected_user_id; 
                                    }
                                    $query_reports .= " ORDER BY r.data DESC";
                                    $stmt_reports = $pdo->prepare($query_reports);
                                    $stmt_reports->execute($params_reports); 
                                    $relatorios = $stmt_reports->fetchAll();

                                    $total_lucro_geral = 0; $total_comissao_usuario = 0; $total_comissao_gerente = 0; $total_comissao_admin = 0;
                                    if (count($relatorios) == 0) {
                                        echo "<tr><td colspan='6' class='text-center'>Nenhum relatório encontrado.</td></tr>";
                                    } else {
                                        foreach ($relatorios as $r) {
                                            $total_lucro_geral += $r['lucro_diario'];
                                            $total_comissao_usuario += $r['comissao_usuario'];
                                            $total_comissao_gerente += $r['comissao_sub_adm'];
                                            $total_comissao_admin += $r['comissao_admin'];
                                            echo "<tr>
                                                    <td>" . htmlspecialchars($r['nome_usuario']) . "</td>
                                                    <td>" . date('d/m/Y H:i', strtotime($r['data'])) . "</td>
                                                    <td>R$ " . number_format($r['lucro_diario'], 2, ',', '.') . "</td>
                                                    <td>R$ " . number_format($r['comissao_usuario'], 2, ',', '.') . "</td>
                                                    <td>R$ " . number_format($r['comissao_sub_adm'], 2, ',', '.') . "</td> 
                                                    <td>R$ " . number_format($r['comissao_admin'], 2, ',', '.') . "</td> 
                                                  </tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr>
                                    <td colspan="2" class="text-end"><strong>TOTAIS:</strong></td>
                                    <td class="text-success fw-bold">R$ <?php echo number_format($total_lucro_geral, 2, ',', '.'); ?></td>
                                    <td class="text-success fw-bold">R$ <?php echo number_format($total_comissao_usuario, 2, ',', '.'); ?></td>
                                    <td class="text-success fw-bold">R$ <?php echo number_format($total_comissao_gerente, 2, ',', '.'); ?></td>
                                    <td class="text-success fw-bold">R$ <?php echo number_format($total_comissao_admin, 2, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- 1. Gráfico Donut (Pizza) ---
    const ctxDoughnut = document.getElementById('comissaoDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Sua Comissão (N1)', 
                    'Comissão Gerentes (N2)',
                    'Comissão Operadores (N3)'
                ],
                datasets: [{
                    data: [
                        <?php echo $total_comissao_admin; ?>,
                        <?php echo $total_comissao_gerentes_admin; ?>,
                        <?php echo $total_comissao_usuarios_admin; ?>
                    ],
                    backgroundColor: [
                        'rgba(117, 79, 254, 0.8)', // Roxo
                        'rgba(255, 193, 7, 0.8)', // Amarelo
                        'rgba(25, 135, 84, 0.8)'  // Verde
                    ]
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12 } } } }
        });
    }

    // --- 2. Gráfico de Linha (Lucro 7 dias) ---
    const ctxLine = document.getElementById('lucroLineChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_values($chart_labels)); ?>,
                datasets: [{
                    label: 'Lucro (R$)',
                    data: <?php echo json_encode(array_values($chart_lucro)); ?>,
                    borderColor: 'rgba(117, 79, 254, 1)', // Roxo
                    backgroundColor: 'rgba(117, 79, 254, 0.1)',
                    fill: true, tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>

<?php 
// **** USA O NOVO FOOTER (ROXO) ****
include('templates/footer-new.php'); 
?>