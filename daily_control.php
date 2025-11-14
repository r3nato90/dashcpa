<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Apenas Gerentes (Super e Sub) podem ver
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_admin_logado = $_SESSION['id'];

// --- LÓGICA DE NAVEGAÇÃO DE DATA ---
$data_selecionada = date('Y-m-d'); // Padrão é hoje
if (isset($_GET['data'])) {
    $data_selecionada = $_GET['data'];
}
$data_anterior = date('Y-m-d', strtotime($data_selecionada . ' -1 day'));
$data_seguinte = date('Y-m-d', strtotime($data_selecionada . ' +1 day'));
$data_formatada = strftime('%d de %B de %Y', strtotime($data_selecionada));
$e_hoje = ($data_selecionada == date('Y-m-d'));


// --- BUSCAR DADOS DOS RELATÓRIOS PARA ESTA DATA ---
$report_totals = [
    'total_deposito' => 0,
    'total_saque_bau' => 0,
    'total_lucro' => 0,
    'total_comissao_admin' => 0,
    'total_comissao_gerente' => 0,
    'total_comissao_operador' => 0
];
$params_reports = [];

if ($role == 'super_adm') {
    // Super Admin vê o total de TUDO
    $query_reports = "
        SELECT 
            SUM(valor_deposito) as total_deposito, 
            SUM(valor_saque + valor_bau) as total_saque_bau,
            SUM(lucro_diario) as total_lucro,
            SUM(comissao_admin) as total_comissao_admin,
            SUM(comissao_sub_adm) as total_comissao_gerente,
            SUM(comissao_usuario) as total_comissao_operador
        FROM relatorios 
        WHERE DATE(data) = ?
    ";
    $params_reports = [$data_selecionada];
} else {
    // Admin/Sub-Admin vê o total APENAS dos seus usuários
    $query_reports = "
        SELECT 
            SUM(r.valor_deposito) as total_deposito, 
            SUM(r.valor_saque + r.valor_bau) as total_saque_bau,
            SUM(r.lucro_diario) as total_lucro,
            SUM(r.comissao_admin) as total_comissao_admin,
            SUM(r.comissao_sub_adm) as total_comissao_gerente,
            SUM(r.comissao_usuario) as total_comissao_operador
        FROM relatorios r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE u.id_sub_adm = ? AND DATE(r.data) = ?
    ";
    $params_reports = [$id_admin_logado, $data_selecionada];
}

$stmt_reports = $pdo->prepare($query_reports);
$stmt_reports->execute($params_reports);
$fetched_totals = $stmt_reports->fetch(PDO::FETCH_ASSOC);
if ($fetched_totals) {
    // Mistura os resultados com os padrões para evitar erro de null
    $report_totals = array_merge($report_totals, array_filter($fetched_totals));
}


// --- BUSCAR DADOS DE DESPESAS PARA ESTA DATA ---
// (Apenas despesas do admin logado)
$stmt_despesas = $pdo->prepare("SELECT gastos_proxy, gastos_numeros FROM despesas_diarias WHERE id_admin_logado = ? AND data = ?");
$stmt_despesas->execute([$id_admin_logado, $data_selecionada]);
$despesas = $stmt_despesas->fetch(PDO::FETCH_ASSOC);

$gastos_proxy = $despesas['gastos_proxy'] ?? 0;
$gastos_numeros = $despesas['gastos_numeros'] ?? 0;

include('templates/header.php'); 
?>

<div class="container-fluid">

    <!-- Cabeçalho -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Controle Diário</h1>
            <p class="text-muted-foreground">Gerencie suas operações diárias - Salvamento automático ativo</p>
        </div>
        <a href="dashboard_superadmin.php" class="btn btn-success btn-lg"> <!-- Modificado: Link "Novo Registro" removido pois o dia é controlado pela data -->
             <i class="fas fa-arrow-left me-2"></i> Voltar ao Dashboard
        </a>
    </div>

    <div class="row g-4">
        
        <!-- Coluna da Data -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Data Selecionada</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="?data=<?php echo $data_anterior; ?>" class="btn btn-outline-secondary" id="btn-dia-anterior">&lsaquo;</a>
                        <span class="font-bold text-lg" id="data-formatada"><?php echo $data_formatada; ?></span>
                        <a href="?data=<?php echo $data_seguinte; ?>" class="btn btn-outline-secondary" id="btn-dia-seguinte">&rsaquo;</a>
                    </div>
                    <?php if (!$e_hoje): ?>
                    <a href="daily_control.php" class="btn btn-primary w-100 mb-3">Ir para Hoje</a>
                    <?php endif; ?>
                    
                    <div class="mt-auto text-muted-foreground text-sm">
                         <hr style="border-top-color: hsl(var(--border));">
                        <p class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Salvamento automático ativo.</p>
                        <p class="mb-1"><i class="fas fa-keyboard me-2"></i>Use as setas (← e →) para navegar.</p>
                        <p class="mb-0"><i class="fas fa-info-circle me-2"></i>Alterações são salvas ao sair do campo.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna de Totais do Dia -->
        <div class="col-lg-9">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Relatório do Dia (<?php echo $role == 'super_adm' ? 'Total' : 'Sua Equipe'; ?>)</h5>
                </div>
                <div class="card-body">
                    <!-- Grid de 6 cards para os totais -->
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        
                        <!-- Total Investido -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-blue-500">
                            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                                <h3 class="tracking-tight text-sm font-medium">Total Investido (Depósito)</h3>
                                <i class="fas fa-dollar-sign h-4 w-4 text-blue-600"></i>
                            </div>
                            <div class="p-4 pt-0"><div class="text-2xl font-bold text-blue-600">R$ <?php echo number_format($report_totals['total_deposito'], 2, ',', '.'); ?></div></div>
                        </div>

                        <!-- Total Saque + Baú -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-purple-500">
                            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                                <h3 class="tracking-tight text-sm font-medium">Total Saque + Baú</h3>
                                <i class="fas fa-piggy-bank h-4 w-4 text-purple-600"></i>
                            </div>
                            <div class="p-4 pt-0"><div class="text-2xl font-bold text-purple-600">R$ <?php echo number_format($report_totals['total_saque_bau'], 2, ',', '.'); ?></div></div>
                        </div>

                        <!-- Lucro Total (Bruto) -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-green-500">
                            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                                <h3 class="tracking-tight text-sm font-medium">Lucro Total (Bruto)</h3>
                                <i class="fas fa-chart-line h-4 w-4 text-green-600"></i>
                            </div>
                            <div class="p-4 pt-0"><div class="text-2xl font-bold text-green-600">R$ <?php echo number_format($report_totals['total_lucro'], 2, ',', '.'); ?></div></div>
                        </div>

                        <!-- Comissão Admin (50%) -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-orange-500">
                            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                                <h3 class="tracking-tight text-sm font-medium">Comissão Admin</h3>
                                <i class="fas fa-crown h-4 w-4 text-orange-600"></i>
                            </div>
                            <div class="p-4 pt-0"><div class="text-2xl font-bold text-orange-600">R$ <?php echo number_format($report_totals['total_comissao_admin'], 2, ',', '.'); ?></div></div>
                        </div>

                        <!-- Comissão Gerentes (10%) -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-yellow-500" style="border-left-color: #eab308;">
                            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                                <h3 class="tracking-tight text-sm font-medium">Comissão Gerentes</h3>
                                <i class="fas fa-user-tie h-4 w-4 text-yellow-500" style="color: #eab308;"></i>
                            </div>
                            <div class="p-4 pt-0"><div class="text-2xl font-bold" style="color: #eab308;">R$ <?php echo number_format($report_totals['total_comissao_gerente'], 2, ',', '.'); ?></div></div>
                        </div>

                        <!-- Comissão Operadores (40%) -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm border-l-4 border-l-red-500">
                            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                                <h3 class="tracking-tight text-sm font-medium">Comissão Operadores</h3>
                                <i class="fas fa-users h-4 w-4 text-red-600"></i>
                            </div>
                            <div class="p-4 pt-0"><div class="text-2xl font-bold text-red-600">R$ <?php echo number_format($report_totals['total_comissao_operador'], 2, ',', '.'); ?></div></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Fim da Row dos Totais -->

    <!-- Despesas Operacionais -->
    

</div>

<?php include('templates/footer.php'); ?>