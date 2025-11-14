<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); // Define o Fuso Horário
include('config/logger.php'); // Inclui o sistema de Log

// Verificação de segurança: Apenas 'admin' ou 'sub_adm' pode ver esta página
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$id_admin_logado = $_SESSION['id'];
$role = $_SESSION['role'];

// Mensagem de status (se houver)
$message = "";
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "<div class='alert alert-success'>Relatório enviado com sucesso!</div>";
}


// --- LÓGICA DE FILTRO DE DATA (HOJE/ONTEM/DATA ESPECÍFICA) ---
$date_filter = date('Y-m-d'); // Padrão: Hoje
if (isset($_GET['date'])) {
    if ($_GET['date'] == 'yesterday') {
        $date_filter = date('Y-m-d', strtotime('-1 day'));
    } elseif (DateTime::createFromFormat('Y-m-d', $_GET['date'])) {
        $date_filter = $_GET['date'];
    }
}
$date_end_query = $date_filter . ' 23:59:59';
$date_start_query = $date_filter . ' 00:00:00';
// --- FIM LÓGICA DE FILTRO DE DATA ---


// --- QUERIES GLOBAIS (TOTAIS DA EQUIPE DO ADMIN) ---

// 1. Total de Operadores Vinculados
$stmt_total_users = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_sub_adm = ?");
$stmt_total_users->execute([$id_admin_logado]);
$total_users_equipe = $stmt_total_users->fetchColumn() ?? 0;

// 2. Lucro TOTAL BRUTO, Investido, Saque+Baú (DA SUA EQUIPE)
$stmt_totais_equipe_geral = $pdo->prepare("
    SELECT 
        SUM(r.lucro_diario) AS total_lucro_bruto,
        SUM(r.valor_deposito) AS total_deposito,
        SUM(r.valor_saque) + SUM(r.valor_bau) AS total_saque_bau,
        SUM(r.comissao_sub_adm) AS total_comissao_gerente
    FROM relatorios r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE u.id_sub_adm = ?
");
$stmt_totais_equipe_geral->execute([$id_admin_logado]);
$totais_equipe_geral = $stmt_totais_equipe_geral->fetch(PDO::FETCH_ASSOC);

$lucro_total_bruto_equipe = $totais_equipe_geral['total_lucro_bruto'] ?? 0;
$lucro_liquido_gerente_acumulado = $totais_equipe_geral['total_comissao_gerente'] ?? 0;
$total_investido_geral = $totais_equipe_geral['total_deposito'] ?? 0;
$total_saque_bau_geral = $totais_equipe_geral['total_saque_bau'] ?? 0;


// 3. Registros (Dias Únicos com Operações da SUA EQUIPE)
$stmt_registros = $pdo->prepare("
    SELECT COUNT(DISTINCT DATE(r.data)) 
    FROM relatorios r 
    JOIN usuarios u ON r.id_usuario = u.id_usuario 
    WHERE u.id_sub_adm = ?
");
$stmt_registros->execute([$id_admin_logado]);
$dias_registrados = $stmt_registros->fetchColumn() ?? 0;

// --- CÁLCULOS DE MÉDIA E ROI ---
$media_diaria = ($dias_registrados > 0) ? ($lucro_liquido_gerente_acumulado / $dias_registrados) : 0;
$roi = 0;
if ($total_investido_geral > 0) {
    $lucro_bruto_total_roi = $total_saque_bau_geral - $total_investido_geral; 
    $roi = ($lucro_bruto_total_roi / $total_investido_geral) * 100;
}


// 4. Melhor Dia e Pior Dia (Lucro líquido do Gerente nos últimos 7 dias)
$stmt_melhor_pior = $pdo->prepare("
    SELECT 
        MAX(lucro_gerente_diario) as melhor_dia_valor,
        MIN(lucro_gerente_diario) as pior_dia_valor
    FROM (
        SELECT 
            DATE(r.data) as dia, 
            SUM(r.comissao_sub_adm) as lucro_gerente_diario
        FROM relatorios r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE u.id_sub_adm = ? AND r.data >= CURDATE() - INTERVAL 7 DAY
        GROUP BY dia
    ) as lucros_diarios
");
$stmt_melhor_pior->execute([$id_admin_logado]);
$melhor_pior = $stmt_melhor_pior->fetch(PDO::FETCH_ASSOC);
$melhor_dia_valor = $melhor_pior['melhor_dia_valor'] ?? 0;
$pior_dia_valor = $melhor_pior['pior_dia_valor'] ?? 0;


// 5. Dados para o GRÁFICO (Evolução do Saldo - 30 dias)
$stmt_line_chart = $pdo->prepare("
    SELECT 
        DATE(r.data) as dia, 
        SUM(r.comissao_sub_adm) as lucro_gerente_diario,
        SUM(r.lucro_diario) as lucro_bruto_diario
    FROM relatorios r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE u.id_sub_adm = ? AND r.data >= CURDATE() - INTERVAL 30 DAY
    GROUP BY dia
    ORDER BY dia ASC
");
$stmt_line_chart->execute([$id_admin_logado]);
$line_chart_data_raw = $stmt_line_chart->fetchAll(PDO::FETCH_ASSOC);

// Preparar dados para o JS (Acúmulo de Lucro do Gerente)
$chart_labels = [];
$chart_lucro_bruto_diario = []; 
$chart_lucro_gerente_acumulado = [];
$date_keys = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_keys[$date] = ['lucro_gerente' => 0, 'lucro_bruto' => 0];
    $chart_labels[] = date('d/m', strtotime($date));
}
foreach ($line_chart_data_raw as $row) {
    if (isset($date_keys[$row['dia']])) {
        $date_keys[$row['dia']]['lucro_gerente'] = (float)$row['lucro_gerente_diario'];
        $date_keys[$row['dia']]['lucro_bruto'] = (float)$row['lucro_bruto_diario'];
    }
}

// Cálculo do Acumulado (Começa em 0 no período de 30 dias)
$saldo_acumulado_temp = 0;
foreach ($date_keys as $data) {
    $saldo_acumulado_temp += $data['lucro_gerente']; 
    $chart_lucro_gerente_acumulado[] = $saldo_acumulado_temp;
    $chart_lucro_bruto_diario[] = $data['lucro_bruto'];
}


// 6. Totais do Dia Filtrado (Para os 4 Cards de Cima)
$stmt_acumulado_dia = $pdo->prepare("
    SELECT 
        SUM(r.lucro_diario) as total_lucro_bruto_dia,
        SUM(r.comissao_sub_adm) as total_comissao_gerente_dia,
        SUM(r.comissao_usuario) as total_pago_operadores_dia,
        SUM(r.comissao_admin) as total_comissao_admin_dia
    FROM relatorios r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE u.id_sub_adm = ? AND (r.data BETWEEN ? AND ?)
");
$stmt_acumulado_dia->execute([$id_admin_logado, $date_start_query, $date_end_query]);
$acumulado_dia = $stmt_acumulado_dia->fetch(PDO::FETCH_ASSOC);

$total_lucro_bruto_dia = $acumulado_dia['total_lucro_bruto_dia'] ?? 0;
$total_comissao_gerente_dia = $acumulado_dia['total_comissao_gerente_dia'] ?? 0;
$total_pagamentos_operadores_dia = $acumulado_dia['total_pago_operadores_dia'] ?? 0;
$total_comissao_admin_dia = $acumulado_dia['total_comissao_admin_dia'] ?? 0;


include('templates/header.php'); 
?>

<div class="container-fluid">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Painel de Gerência</h1>
            <p class="text-muted-foreground">Visão geral e desempenho da sua equipe</p>
        </div>
        
        <div class="d-flex flex-row gap-2 align-items-center">
            <a href="?date=yesterday" class="btn btn-outline-secondary btn-sm <?php echo ($_GET['date'] ?? '') == 'yesterday' ? 'active' : ''; ?>">Ontem</a>
            <a href="?date=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm <?php echo !isset($_GET['date']) || $_GET['date'] == date('Y-m-d') ? 'active' : ''; ?>">Hoje</a>
            <form method="GET" class="d-flex gap-2">
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($date_filter); ?>">
                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
            </form>
        </div>
    </div>
    <?php echo $message; // Exibe mensagens de sucesso/erro ?>

    <h3 class="h5 mb-3">Métricas Acumuladas (Dia Filtrado: <?php echo date('d/m/Y', strtotime($date_filter)); ?>)</h3>
    <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
        
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-green-500">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-xs text-muted-foreground mt-1">Sua Comissão</h3>
                <i class="fas fa-chart-line h-4 w-4 text-green-600"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-green-600">R$ <?php echo number_format($total_comissao_gerente_dia, 2, ',', '.'); ?></div>
                <p class="text-xs text-muted-foreground mt-1">Lucro Líquido do Subadmin</p>
            </div>
        </div>
        
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-blue-500">
            
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-blue-600">R$ <?php echo number_format($total_pagamentos_operadores_dia, 2, ',', '.'); ?></div>
                <p class="text-xs text-muted-foreground mt-1">Total pago à sua equipe</p>
            </div>
        </div>
</div>
    
    

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h3 class="text-lg font-semibold">Evolução da Sua Comissão (Últimos 30 Dias)</h3>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="lucroLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div> <script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Configuração global de cores para o Chart.js (para o tema dark)
    Chart.defaults.color = 'hsl(var(--muted-foreground))'; 
    
    const datasets = [];

    // --- 1. Saldo Acumulado (Gerente - 10%) ---
    datasets.push({
        label: 'Sua Comissão Acumulada', 
        data: <?php echo json_encode(array_values($chart_lucro_gerente_acumulado)); ?>,
        borderColor: 'rgba(117, 79, 254, 1)', // Roxo
        backgroundColor: 'rgba(117, 79, 254, 0.1)',
        fill: true,
        tension: 0.4,
        borderWidth: 3
    });

    // --- 2. Lucro Bruto Diário (Equipe) ---
    datasets.push({
        label: 'Lucro Bruto Diário (Equipe)', 
        data: <?php echo json_encode(array_values($chart_lucro_bruto_diario)); ?>,
        borderColor: 'rgba(13, 110, 253, 0.5)', // Azul (Mais discreto)
        backgroundColor: 'transparent',
        fill: false,
        tension: 0.2,
        borderDash: [5, 5] // Linha tracejada para o valor diário
    });

    // --- Renderiza o Gráfico de Linha ---
    const ctxLine = document.getElementById('lucroLineChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_values($chart_labels)); ?>,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { 
                        ticks: { color: 'hsl(var(--muted-foreground))' }, 
                        grid: { color: 'hsl(var(--border))' } 
                    },
                    y: { 
                        ticks: { color: 'hsl(var(--muted-foreground))' }, 
                        grid: { color: 'hsl(var(--border))' } 
                    }
                },
                plugins: {
                    legend: { labels: { color: 'hsl(var(--foreground))' } }
                }
            }
        });
    }
    
});
</script>

<?php 
// Inclui o footer (que fecha o layout e carrega o Chart.js)
include('templates/footer.php'); 
?>
