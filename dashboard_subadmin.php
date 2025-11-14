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


// --- QUERIES GLOBAIS (TOTAIS DA EQUIPE DO SUB-ADMIN) ---

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

// Busca os usuários vinculados para o formulário de envio de ciclo
$stmt_linked_users = $pdo->prepare("SELECT id_usuario, nome FROM usuarios WHERE id_sub_adm = ? ORDER BY nome");
$stmt_linked_users->execute([$id_admin_logado]);
$linked_users = $stmt_linked_users->fetchAll();


include('templates/header.php'); 
?>

<!-- O conteúdo começa aqui -->
<div class="container-fluid">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Painel de Gerência (Sub-Admin)</h1>
            <p class="text-muted-foreground">Visão geral e desempenho da sua equipe</p>
        </div>
        
        <!-- Botões de Filtro Rápido (Ontem, Hoje, Data) -->
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

    <div class="row g-4">
        <!-- Coluna Esquerda: Enviar Ciclo -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Enviar Ciclo (Depósito/Saque/Baú)</h5>
                </div>
                <div class="card-body">
                    <form action="process_transaction.php" method="POST">
                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Selecione o Operador</label>
                            <select class="form-control" name="usuario_id" required>
                                <option value="">Escolha um operador...</option>
                                <?php
                                foreach ($linked_users as $user) {
                                    echo "<option value='{$user['id_usuario']}'>" . htmlspecialchars($user['nome']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data_relatorio" class="form-label">Data do Ciclo</label>
                            <input type="date" class="form-control" name="data_relatorio" value="<?php echo date('Y-m-d'); ?>" required>
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
                        
                        <button type="submit" class="btn btn-success w-100" <?php echo (empty($linked_users)) ? 'disabled' : ''; ?>>
                            <?php echo (empty($linked_users)) ? 'Vincule um operador primeiro' : 'Enviar Ciclo'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Métricas e Gráfico -->
        <div class="col-lg-8">
            <!-- CARDS DE MÉTRICAS ACUMULADAS (Dia Filtrado) -->
            <h3 class="h5 mb-3">Métricas Acumuladas (Dia Filtrado: <?php echo date('d/m/Y', strtotime($date_filter)); ?>)</h3>
            <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-3">
                
                <!-- Card 1: Sua Comissão (Líquido Gerente 10%) -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-green-500">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Sua Comissão</h3>
                        <i class="fas fa-chart-line h-4 w-4 text-green-600"></i>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-green-600">R$ <?php echo number_format($total_comissao_gerente_dia, 2, ',', '.'); ?></div>
                        <p class="text-xs text-muted-foreground mt-1">Lucro Líquido do Gerente</p>
                    </div>
                </div>
                
                <!-- Card 2: Pagamentos Operadores (40%) -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-blue-500">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Pagamentos Operadores</h3>
                        <i class="fas fa-user-tag h-4 w-4 text-blue-600"></i>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-blue-600">R$ <?php echo number_format($total_pagamentos_operadores_dia, 2, ',', '.'); ?></div>
                        <p class="text-xs text-muted-foreground mt-1">Total pago à sua equipe</p>
                    </div>
                </div>

                <!-- Card 3: Comissão Administrador (50%) -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-orange-500">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Comissão Admin</h3>
                        <i class="fas fa-crown h-4 w-4 text-orange-600"></i>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-orange-600">R$ <?php echo number_format($total_comissao_admin_dia, 2, ',', '.'); ?></div>
                        <p class="text-xs text-muted-foreground mt-1">Repasse ao Super Admin</p>
                    </div>
                </div>
                
            </div>
            
            <!-- CARDS DE ESTATÍSTICAS GLOBAIS (Melhor/Pior Dia, ROI, etc.) -->
            <h3 class="h5 mb-3">Estatísticas (Total da Sua Equipe)</h3>
            <div class="grid gap-6 mb-8 md:grid-cols-2 lg:grid-cols-3">

                <!-- Card: Total Investido (Equipe) -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-blue-500">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Total Investido (Geral)</h3>
                        <i class="fas fa-dollar-sign h-4 w-4 text-blue-600"></i>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-blue-600">R$ <?php echo number_format($total_investido_geral, 2, ',', '.'); ?></div>
                        <p class="text-xs text-muted-foreground mt-1">ROI: <?php echo number_format($roi, 1, ',', '.'); ?>%</p>
                    </div>
                </div>

                <!-- Card: Registros (Operadores Vinculados) -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-orange-500">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Operadores Vinculados</h3>
                        <i class="fas fa-user-plus h-4 w-4 text-orange-600"></i>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-orange-600"><?php echo $total_users_equipe; ?></div>
                        <p class="text-xs text-muted-foreground mt-1">Total na sua equipe</p>
                    </div>
                </div>

                <!-- Card: Lucro Bruto da Equipe -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-purple-500">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Lucro Bruto (Total)</h3>
                        <i class="fas fa-calculator h-4 w-4 text-purple-600"></i>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-purple-600">R$ <?php echo number_format($lucro_total_bruto_equipe, 2, ',', '.'); ?></div>
                        <p class="text-xs text-muted-foreground mt-1">Total da sua equipe (Histórico)</p>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de Evolução -->
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
    
    <!-- Tabela de Relatórios Recentes (Extrato) -->
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="h4">Extrato (Últimas Operações da Equipe)</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table id="extratoTable" class="table table-striped table-bordered table-sm">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Operador</th><th>Data</th><th>Lucro Bruto</th>
                                    <th>Com. Op.</th><th>Sua Com.</th><th>Com. Adm.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt_extrato = $pdo->prepare("
                                    SELECT r.data, r.lucro_diario, r.comissao_usuario, r.comissao_sub_adm, r.comissao_admin, u.nome
                                    FROM relatorios r 
                                    JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                    WHERE u.id_sub_adm = ? ORDER BY r.data DESC LIMIT 15
                                ");
                                $stmt_extrato->execute([$id_admin_logado]);
                                while ($row = $stmt_extrato->fetch()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['nome']) . "</td>
                                            <td>" . date('d/m/Y H:i', strtotime($row['data'])) . "</td>
                                            <td>R$ " . number_format($row['lucro_diario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_usuario'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_sub_adm'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['comissao_admin'], 2, ',', '.') . "</td>
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


</div> <!-- Fecha .container-fluid -->

<script>
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