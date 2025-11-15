<?php
session_start();
// Estes includes estão CORRETOS, pois o dashboard_superadmin.php está na raiz.
include('config/db.php'); 
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Dashboard (Super Admin)";

// Verificação de segurança
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm') {
    header('Location: login.php');
    exit;
}

// Mensagem de sucesso/erro
$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = "<div class='alert alert-success mt-3'>Relatório enviado com sucesso!</div>";
    } elseif ($_GET['status'] == 'error_no_user') {
        $message = "<div class='alert alert-danger mt-3'>Erro: Nenhum usuário foi selecionado.</div>";
    }
}

// --- QUERIES PARA OS CARDS DE ESTATÍSTICAS (KPIs) ---
// $pdo já foi carregado acima, então estas queries funcionam.
$stmt_total_users = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE role = 'usuario'");
$total_users = $stmt_total_users->fetchColumn();

// Conta admins e sub_admins como managers
$stmt_total_managers = $pdo->query("SELECT COUNT(*) FROM sub_administradores WHERE role IN ('admin', 'sub_adm')");
$total_managers = $stmt_total_managers->fetchColumn();

// Lucro bruto total
$stmt_total_lucro_bruto = $pdo->query("SELECT SUM(lucro_diario) FROM relatorios");
$total_lucro_bruto = $stmt_total_lucro_bruto->fetchColumn() ?? 0;

// Lucro líquido do sistema (Super Admin) - Campo comissao_admin
$stmt_lucro_liquido_sistema = $pdo->query("SELECT SUM(comissao_admin) FROM relatorios");
$lucro_liquido_sistema = $stmt_lucro_liquido_sistema->fetchColumn() ?? 0;

// Total de comissão dos gerentes (Admin + Sub-Admin) - Campo comissao_sub_adm
$stmt_total_comissao_gerentes = $pdo->query("SELECT SUM(comissao_sub_adm) FROM relatorios");
$total_comissao_gerentes = $stmt_total_comissao_gerentes->fetchColumn() ?? 0;

// Total de comissão dos usuários (Operadores) - Campo comissao_usuario
$stmt_total_comissao_usuarios = $pdo->query("SELECT SUM(comissao_usuario) FROM relatorios");
$total_comissao_usuarios = $stmt_total_comissao_usuarios->fetchColumn() ?? 0;


// --- DADOS PARA OS GRÁFICOS (Últimos 7 dias) ---
$data_corte = date('Y-m-d', strtotime('-7 days'));
$query_grafico = "
    SELECT 
        DATE(data) as dia,
        SUM(lucro_diario) as lucro_diario,
        SUM(comissao_admin) as lucro_sistema
    FROM relatorios 
    WHERE data >= ?
    GROUP BY dia
    ORDER BY dia ASC
";
$stmt_grafico = $pdo->prepare($query_grafico);
$stmt_grafico->execute([$data_corte]);
$dados_grafico_bruto = $stmt_grafico->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data_lucro = [];
$data_sistema = [];

// Preenche os dados para os últimos 7 dias (incluindo dias sem dados com valor 0)
for ($i = 6; $i >= 0; $i--) {
    $dia = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($dia));
    
    $found = false;
    foreach ($dados_grafico_bruto as $d) {
        if ($d['dia'] == $dia) {
            $data_lucro[] = (float)$d['lucro_diario'];
            $data_sistema[] = (float)$d['lucro_sistema'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $data_lucro[] = 0;
        $data_sistema[] = 0;
    }
}

$labels_json = json_encode($labels);
$data_lucro_json = json_encode($data_lucro);
$data_sistema_json = json_encode($data_sistema);

// --- TABELA DE PERFORMANCE DOS GERENTES (Admin/Sub-Admin) ---
$query_ranking_managers = "
    SELECT 
        s.nome, 
        s.role, 
        SUM(r.comissao_sub_adm) AS comissao_total
    FROM sub_administradores s
    JOIN usuarios u ON u.manager_id = s.id
    JOIN relatorios r ON r.id_usuario = u.id
    GROUP BY s.id, s.nome, s.role
    ORDER BY comissao_total DESC
    LIMIT 5
";
$stmt_ranking = $pdo->query($query_ranking_managers);
$ranking_managers = $stmt_ranking->fetchAll(PDO::FETCH_ASSOC);

// --- DESPESAS DIÁRIAS GLOBAIS (Últimos 7 dias) ---
$query_despesas_grafico = "
    SELECT 
        data, 
        SUM(gastos_proxy) AS total_proxy, 
        SUM(gastos_numeros) AS total_numeros
    FROM despesas_diarias
    WHERE data >= ?
    GROUP BY data
    ORDER BY data ASC
";
$stmt_despesas = $pdo->prepare($query_despesas_grafico);
$stmt_despesas->execute([$data_corte]);
$dados_despesas_bruto = $stmt_despesas->fetchAll(PDO::FETCH_ASSOC);

$data_gastos_proxy = [];
$data_gastos_numeros = [];
$total_gastos_7d = 0;

// Preenche os dados para os últimos 7 dias (usando as mesmas labels)
for ($i = 0; $i < count($labels); $i++) {
    $dia_format_db = date('Y-m-d', strtotime("-$i days", strtotime('+' . count($labels) . ' days', strtotime($data_corte))));
    $found = false;
    foreach ($dados_despesas_bruto as $d) {
        if ($d['data'] == $dia_format_db) {
            $data_gastos_proxy[] = (float)$d['total_proxy'];
            $data_gastos_numeros[] = (float)$d['total_numeros'];
            $total_gastos_7d += (float)$d['total_proxy'] + (float)$d['total_numeros'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $data_gastos_proxy[] = 0;
        $data_gastos_numeros[] = 0;
    }
}
// Como o loop acima foi decrescente, reverte as listas
$data_gastos_proxy = array_reverse($data_gastos_proxy);
$data_gastos_numeros = array_reverse($data_gastos_numeros);
$data_gastos_proxy_json = json_encode($data_gastos_proxy);
$data_gastos_numeros_json = json_encode($data_gastos_numeros);

// ### CORREÇÃO 1: Adicionar a pasta 'templates/' ao caminho ###
include('templates/header.php'); 
?>

<h2 class="mb-4">Visão Geral do Sistema</h2>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-line fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-white">Lucro Bruto (Total)</h5>
                        <h1 class="display-6 mb-0">R$ <?php echo number_format($total_lucro_bruto, 2, ',', '.'); ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-wallet fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-white">Lucro Líquido (Seu)</h5>
                        <h1 class="display-6 mb-0">R$ <?php echo number_format($lucro_liquido_sistema, 2, ',', '.'); ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-tie fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-dark">Gerentes (Admin/Sub-Admin)</h5>
                        <h1 class="display-6 mb-0"><?php echo $total_managers; ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users fa-3x me-3"></i>
                    <div>
                        <h5 class="card-title text-white">Operadores (Usuários)</h5>
                        <h1 class="display-6 mb-0"><?php echo $total_users; ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Lucro Bruto e Líquido (Últimos 7 dias)</h5>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="dailyProfitChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Distribuição Global de Lucro</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <div style="width: 80%; max-height: 300px;">
                    <canvas id="comissaoDoughnutChart"></canvas>
                </div>
            </div>
            <div class="card-footer text-muted text-center">
                <small>Divisão Baseada na Receita Total.</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Top 5 Gerentes por Comissão (Total)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nome (Role)</th>
                            <th class="text-end">Comissão Recebida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ranking_managers)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Nenhum dado de comissão encontrado.</td></tr>
                        <?php endif; ?>
                        <?php $rank = 1; foreach ($ranking_managers as $manager): ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($manager['nome']); ?> (<?php echo strtoupper(str_replace('_', ' ', $manager['role'])); ?>)</td>
                            <td class="text-end">R$ <?php echo number_format($manager['comissao_total'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Despesas Operacionais (Últimos 7 dias)</h5>
            </div>
            <div class="card-body">
                <h4 class="mb-3">Total de Gastos (7D): <span class="text-danger">R$ <?php echo number_format($total_gastos_7d, 2, ',', '.'); ?></span></h4>
                <div style="height: 250px;">
                    <canvas id="expensesBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados injetados do PHP
    const labels = <?php echo $labels_json; ?>;
    const dataLucro = <?php echo $data_lucro_json; ?>;
    const dataSistema = <?php echo $data_sistema_json; ?>;
    const lucroLiquidoSistema = <?php echo $lucro_liquido_sistema; ?>;
    const totalComissaoGerentes = <?php echo $total_comissao_gerentes; ?>;
    const totalComissaoUsuarios = <?php echo $total_comissao_usuarios; ?>;
    const dataGastosProxy = <?php echo $data_gastos_proxy_json; ?>;
    const dataGastosNumeros = <?php echo $data_gastos_numeros_json; ?>;


    // --- 1. Gráfico de Linha (Lucro Diário) ---
    const ctxLine = document.getElementById('dailyProfitChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Lucro Bruto (Total CPA)',
                    data: dataLucro,
                    borderColor: 'rgba(0, 123, 255, 1)', // Azul (Primary)
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    fill: true,
                    tension: 0.2
                },
                {
                    label: 'Lucro Líquido (Seu)',
                    data: dataSistema,
                    borderColor: 'rgba(25, 135, 84, 1)', // Verde (Success)
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Valor (R$)'
                        }
                    }
                }
            }
        });
    }

    // --- 2. Gráfico Donut (Distribuição) ---
    const ctxDoughnut = document.getElementById('comissaoDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Lucro (Sistema) - (50%)', 
                    'Comissão (Gerentes) - (10%)', 
                    'Comissão (Usuários) - (40%)'
                ],
                datasets: [{
                    data: [
                        lucroLiquidoSistema,
                        totalComissaoGerentes,
                        totalComissaoUsuarios
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)', // Verde (Sistema - Success)
                        'rgba(255, 193, 7, 0.8)', // Amarelo (Gerentes - Warning)
                        'rgba(13, 110, 253, 0.8)'  // Azul (Usuários - Primary)
                    ],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += 'R$ ' + context.parsed.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // --- 3. Gráfico de Barras (Despesas) ---
    const ctxExpenses = document.getElementById('expensesBarChart');
    if (ctxExpenses) {
        new Chart(ctxExpenses, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gastos com Proxy',
                    data: dataGastosProxy,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)', // Vermelho (Danger)
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Gastos com Números',
                    data: dataGastosNumeros,
                    backgroundColor: 'rgba(108, 117, 125, 0.8)', // Cinza (Secondary)
                    borderColor: 'rgba(108, 117, 125, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Valor (R$)'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'R$ ' + context.parsed.y.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php 
// ### CORREÇÃO 2: Adicionar a pasta 'templates/' ao caminho ###
include('templates/footer.php');
?>