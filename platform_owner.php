<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php'); 

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id']; // Sua Org ID (geralmente 1)
$id_logado = $_SESSION['id'];

// --- QUERIES GLOBAIS (PARA O DONO DA PLATAFORMA) ---

// 1. Cards de KPI
$stmt_total_orgs = $pdo->query("SELECT COUNT(*) FROM organizations");
$total_orgs = $stmt_total_orgs->fetchColumn();
$stmt_total_users = $pdo->query("SELECT COUNT(*) FROM usuarios");
$total_users = $stmt_total_users->fetchColumn();
$stmt_total_lucro = $pdo->query("SELECT SUM(lucro_diario) FROM relatorios");
$total_lucro = $stmt_total_lucro->fetchColumn() ?? 0;

// 2. Status da API do Mercado Pago
$stmt_mp_keys = $pdo->query("SELECT setting_key, setting_value FROM platform_settings WHERE setting_key IN ('mp_public_key', 'mp_access_token')");
$mp_keys = $stmt_mp_keys->fetchAll(PDO::FETCH_KEY_PAIR);
$is_mp_configured = (!empty($mp_keys['mp_public_key']) && !empty($mp_keys['mp_access_token']));

// 3. Gráfico de Linha (Últimos 7 dias - Global)
$stmt_line_chart = $pdo->query("
    SELECT DATE(data) as dia, SUM(lucro_diario) as lucro_total
    FROM relatorios WHERE data >= CURDATE() - INTERVAL 7 DAY
    GROUP BY dia ORDER BY dia ASC
");
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

// 4. Gráfico Donut (Top 5 Organizações por Lucro)
$stmt_donut_chart = $pdo->query("
    SELECT o.org_name, SUM(r.lucro_diario) as total_lucro_org
    FROM relatorios r
    JOIN organizations o ON r.org_id = o.org_id
    GROUP BY o.org_name
    ORDER BY total_lucro_org DESC
    LIMIT 5
");
$donut_data = $stmt_donut_chart->fetchAll(PDO::FETCH_ASSOC);
$donut_labels = [];
$donut_values = [];
foreach ($donut_data as $data) {
    $donut_labels[] = $data['org_name'];
    $donut_values[] = $data['total_lucro_org'];
}

// **** USA O NOVO HEADER ****
include('templates/header-new.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Painel do Dono da Plataforma (SaaS)</h2>
        <a href="platform_manage_orgs.php" class="btn btn-success btn-lg shadow-sm">
            <i class="fas fa-building me-2"></i> Gerenciar Clientes
        </a>
    </div>

    <div class="alert alert-primary d-flex justify-content-between align-items-center">
        <strong>Status do Registro Público:</strong>
        <span>O formulário de registro de novas empresas está <strong>ATIVO</strong>.</span>
        <a href="login.php" class="btn btn-outline-primary btn-sm">Ver Página de Login</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-primary-soft"><i class="fas fa-building"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Organizações (Clientes)</h6>
                        <h4 class="fw-bold mb-0"><?php echo $total_orgs; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-info-soft"><i class="fas fa-users"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Operadores (Total)</h6>
                        <h4 class="fw-bold mb-0"><?php echo $total_users; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="kpi-icon bg-success-soft"><i class="fas fa-dollar-sign"></i></div>
                    <div>
                        <h6 class="text-muted mb-1">Lucro (Total Clientes)</h6>
                        <h4 class="fw-bold mb-0">R$ <?php echo number_format($total_lucro, 2, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <?php if ($is_mp_configured): ?>
                <div class="card kpi-card bg-success-soft shadow h-100">
                    <div class="card-body">
                        <div class="kpi-icon bg-success-soft"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <h6 class="text-muted mb-1">Pagamentos SaaS</h6>
                            <h4 class="fw-bold mb-0">Ativo</h4>
                            <a href="platform_settings.php" class="text-success small stretched-link">Ver Configurações</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card kpi-card bg-danger-soft text-danger shadow h-100">
                    <div class="card-body">
                        <div class="kpi-icon bg-danger-soft text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <h6 class="text-muted mb-1">Pagamentos SaaS</h6>
                            <h4 class="fw-bold mb-0">Pendente</h4>
                            <a href="platform_settings.php" class="text-danger small stretched-link"><strong>Configurar API Agora</strong></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Lucro Global (Últimos 7 Dias)</div>
                <div class="card-body">
                    <canvas id="lucroLineChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-chart-pie me-2"></i>Top 5 Clientes (por Lucro)</div>
                <div class="card-body">
                    <canvas id="topClientsDoughnutChart"></canvas>
                </div>
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
                    fill: true, tension: 0.2
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }

    // --- 2. Gráfico Donut (Top Clientes) ---
    const ctxDoughnut = document.getElementById('topClientsDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($donut_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($donut_values); ?>,
                    backgroundColor: [
                        'rgba(117, 79, 254, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ]
                }]
            },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
});
</script>

<?php 
// **** USA O NOVO FOOTER ****
include('templates/footer-new.php'); 
?>