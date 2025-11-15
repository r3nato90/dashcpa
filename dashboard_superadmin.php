<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php'); 

// Verificação Multi-Tenant
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm' || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id']; 

$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') $message = "<div class='alert alert-success mt-3'>Relatório enviado com sucesso!</div>";
    elseif ($_GET['status'] == 'error_no_user') $message = "<div class='alert alert-danger mt-3'>Erro: Nenhum usuário foi selecionado.</div>";
}

// --- 1. QUERIES PARA KPIs (FILTRADAS POR ORG_ID) ---
$stmt_total_users = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE org_id = ?");
$stmt_total_users->execute([$org_id]);
$total_users = $stmt_total_users->fetchColumn();

$stmt_total_managers = $pdo->prepare("SELECT COUNT(*) FROM sub_administradores WHERE role IN ('admin', 'sub_adm') AND org_id = ?");
$stmt_total_managers->execute([$org_id]);
$total_managers = $stmt_total_managers->fetchColumn();

$stmt_total_lucro = $pdo->prepare("SELECT SUM(lucro_diario) FROM relatorios WHERE org_id = ?");
$stmt_total_lucro->execute([$org_id]);
$total_lucro = $stmt_total_lucro->fetchColumn() ?? 0;

$stmt_total_comissao_gerentes = $pdo->prepare("SELECT SUM(comissao_sub_adm) FROM relatorios WHERE org_id = ?");
$stmt_total_comissao_gerentes->execute([$org_id]);
$total_comissao_gerentes = $stmt_total_comissao_gerentes->fetchColumn() ?? 0;

// --- 2. QUERIES PARA GRÁFICOS (FILTRADAS POR ORG_ID) ---
$stmt_total_comissao_users = $pdo->prepare("SELECT SUM(comissao_usuario) FROM relatorios WHERE org_id = ?");
$stmt_total_comissao_users->execute([$org_id]);
$total_comissao_usuarios = $stmt_total_comissao_users->fetchColumn() ?? 0;
$lucro_liquido_sistema = $total_lucro - $total_comissao_gerentes - $total_comissao_usuarios;

$stmt_line_chart = $pdo->prepare("
    SELECT DATE(data) as dia, SUM(lucro_diario) as lucro_total
    FROM relatorios WHERE data >= CURDATE() - INTERVAL 7 DAY AND org_id = ?
    GROUP BY dia ORDER BY dia ASC
");
$stmt_line_chart->execute([$org_id]);
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

// --- 3. NOVA QUERY (Top Operadores) (FILTRADA POR ORG_ID) ---
$stmt_top_users = $pdo->prepare("
    SELECT u.nome, SUM(r.lucro_diario) as total_lucro_user
    FROM relatorios r JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.org_id = ?
    GROUP BY u.nome ORDER BY total_lucro_user DESC LIMIT 5
");
$stmt_top_users->execute([$org_id]);
$top_users = $stmt_top_users->fetchAll(PDO::FETCH_ASSOC);

// Busca usuários (para o modal) (FILTRADO POR ORG_ID)
$stmt_all_users = $pdo->prepare("SELECT id_usuario, nome FROM usuarios WHERE org_id = ? ORDER BY nome");
$stmt_all_users->execute([$org_id]);
$all_users = $stmt_all_users->fetchAll();
$hoje = date('Y-m-d');

// **** USA O NOVO HEADER ****
include('templates/header-new.php'); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">Dashboard</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background-color: transparent; padding-left: 0;">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visão Geral</li>
                </ol>
            </nav>
        </div>
        <div class="card kpi-card bg-success-soft" style="min-width: 250px;">
             <div class="card-body">
                <div class="kpi-icon bg-success-soft"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <h6 class="text-muted mb-1">Lucro Total (Geral)</h6>
                    <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_lucro, 2, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-primary-soft"><i class="fas fa-users"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Total Operadores</h6>
                        <h4 class="fw-bold mb-0"><?php echo $total_users; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-info-soft"><i class="fas fa-user-shield"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Total Gerentes</h6>
                        <h4 class="fw-bold mb-0"><?php echo $total_managers; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-warning-soft"><i class="fas fa-percentage"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Comissão Gerentes</h6>
                        <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_comissao_gerentes, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
             <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-danger-soft text-danger"><i class="fas fa-user-plus"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Comissão Operadores</h6>
                        <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_comissao_usuarios, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-7 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Desempenho (Últimos 7 Dias)</h5>
                </div>
                <div class="card-body">
                    <canvas id="lucroLineChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5 mb-3">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Top 5 Operadores (por Lucro)</h5>
                            <a href="reports.php" class="btn btn-sm btn-outline-secondary">Ver Todos</a>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($top_users as $user): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($user['nome']); ?>
                                    <span class="badge bg-success rounded-pill">R$ <?php echo number_format($user['total_lucro_user'], 2, ',', '.'); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm h-100">
                        <div class="card-header"><h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribuição do Lucro (Total)</h5></div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="comissaoDoughnutChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h3 class="h4">Relatórios Recentes</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="relatoriosTable" class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuário</th> <th>Data</th> <th>Depósito</th> <th>Saque</th> <th>Baú</th>
                                    <th>Lucro</th> <th>Com. Usuário</th> <th>Com. Gerente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt_reports = $pdo->prepare("
                                    SELECT r.*, u.nome FROM relatorios r 
                                    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                    WHERE u.id_usuario IS NOT NULL AND r.org_id = ?
                                    ORDER BY r.data DESC LIMIT 7
                                ");
                                $stmt_reports->execute([$org_id]);
                                while ($row = $stmt_reports->fetch()) {
                                    echo "<tr>
                                            <td><strong>" . htmlspecialchars($row['nome']) . "</strong></td>
                                            <td>" . date('d/m/Y H:i', strtotime($row['data'])) . "</td>
                                            <td>R$ " . number_format($row['valor_deposito'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['valor_saque'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['valor_bau'], 2, ',', '.') . "</td>
                                            <td><span class='fw-bold " . ($row['lucro_diario'] >= 0 ? "text-success" : "text-danger") . "'>R$ " . number_format($row['lucro_diario'], 2, ',', '.') . "</span></td>
                                            <td>R$ " . number_format($row['comissao_usuario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_sub_adm'], 2, ',', '.') . "</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
<div class="modal fade" id="modalEnviarRelatorio" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Enviar Novo Relatório</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="process_transaction.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario_id" class="form-label">Usuário</label>
                         <select class="form-control" name="usuario_id" required>
                            <option value="">Selecione um usuário...</option>
                            <?php
                            foreach ($all_users as $user) {
                                echo "<option value='{$user['id_usuario']}'>" . htmlspecialchars($user['nome']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="data_relatorio" class="form-label">Data do Relatório</label>
                        <input type="date" class="form-control" name="data_relatorio" value="<?php echo $hoje; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="deposito" class="form-label">DEPÓSITO</label>
                        <input type="number" step="0.01" class="form-control" name="deposito" required>
                    </div>
                    <div class="mb-3">
                        <label for="saque" class="form-label">SAQUE</label>
                        <input type="number" step="0.01" class="form-control" name="saque" required>
                    </div>
                    <div class="mb-3">
                        <label for="bau" class="form-label">BAÚ (Saldo Final)</label>
                        <input type="number" step="0.01" class="form-control" name="bau" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" <?php echo (empty($all_users)) ? 'disabled' : ''; ?>>
                        <?php echo (empty($all_users)) ? 'Cadastre um usuário primeiro' : 'Enviar Relatório'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- 1. Gráfico de Linha (Lucro 7 dias) ---
    const ctxLine = document.getElementById('lucroLineChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_values($chart_labels)); ?>,
                datasets: [{
                    label: 'Lucro Total (R$)',
                    data: <?php echo json_encode(array_values($chart_lucro)); ?>,
                    borderColor: 'rgba(117, 79, 254, 1)', // Roxo
                    backgroundColor: 'rgba(117, 79, 254, 0.1)',
                    fill: true, tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // --- 2. Gráfico Donut (Distribuição) ---
    const ctxDoughnut = document.getElementById('comissaoDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Lucro (Sistema)', 
                    'Comissão (Gerentes)', 
                    'Comissão (Usuários)'
                ],
                datasets: [{
                    data: [
                        <?php echo $lucro_liquido_sistema; ?>,
                        <?php echo $total_comissao_gerentes; ?>,
                        <?php echo $total_comissao_usuarios; ?>
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
});
</script>

<?php 
// **** USA O NOVO FOOTER ****
include('templates/footer-new.php'); 
?>