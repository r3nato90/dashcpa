<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Minhas Operações (Operador)";

// Verificação de segurança
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'usuario') {
    header('Location: login.php');
    exit;
}
$id_usuario_logado = $_SESSION['user_id'];

// Mensagem de sucesso/erro
$message = "";
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "<div class='alert alert-success mt-3'>Transação registrada com sucesso!</div>";
} elseif (isset($_GET['status']) && $_GET['status'] == 'error') {
     $message = "<div class='alert alert-danger mt-3'>Erro ao registrar transação.</div>";
}


// --- QUERIES PARA OS CARDS DE ESTATÍSTICAS (KPIs) - Apenas para o usuário logado ---

// 1. Minha Comissão Total (Soma de comissao_usuario)
$stmt_comissao_total = $pdo->prepare("SELECT SUM(comissao_usuario) FROM relatorios WHERE id_usuario = ?");
$stmt_comissao_total->execute([$id_usuario_logado]);
$minha_comissao_total = $stmt_comissao_total->fetchColumn() ?? 0;

// 2. Minha Comissão do Mês Atual
$mes_atual = date('Y-m');
$stmt_comissao_mes = $pdo->prepare("SELECT SUM(comissao_usuario) FROM relatorios WHERE id_usuario = ? AND DATE_FORMAT(data, '%Y-%m') = ?");
$stmt_comissao_mes->execute([$id_usuario_logado, $mes_atual]);
$minha_comissao_mes = $stmt_comissao_mes->fetchColumn() ?? 0;

// 3. Lucro Bruto Gerado (Total)
$stmt_lucro_bruto_total = $pdo->prepare("SELECT SUM(lucro_diario) FROM relatorios WHERE id_usuario = ?");
$stmt_lucro_bruto_total->execute([$id_usuario_logado]);
$lucro_bruto_gerado = $stmt_lucro_bruto_total->fetchColumn() ?? 0;

// 4. Última transação (Data)
$stmt_ultima_transacao = $pdo->prepare("SELECT data FROM relatorios WHERE id_usuario = ? ORDER BY data DESC LIMIT 1");
$stmt_ultima_transacao->execute([$id_usuario_logado]);
$ultima_transacao = $stmt_ultima_transacao->fetchColumn();
$ultima_transacao_formatada = $ultima_transacao ? date('d/m/Y H:i', strtotime($ultima_transacao)) : 'Nenhuma';


// --- DADOS PARA OS GRÁFICOS (Últimos 7 dias) - Minha Comissão ---
$data_corte = date('Y-m-d', strtotime('-7 days'));
$data_comissao_grafico = [];
$labels = [];

$query_grafico = "
    SELECT 
        DATE(data) as dia,
        SUM(comissao_usuario) as minha_comissao
    FROM relatorios 
    WHERE id_usuario = ? AND data >= ?
    GROUP BY dia
    ORDER BY dia ASC
";
$stmt_grafico = $pdo->prepare($query_grafico);
$stmt_grafico->execute([$id_usuario_logado, $data_corte]);
$dados_grafico_bruto = $stmt_grafico->fetchAll(PDO::FETCH_ASSOC);

// Preenche os dados para os últimos 7 dias
for ($i = 6; $i >= 0; $i--) {
    $dia = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($dia));
    
    $found = false;
    foreach ($dados_grafico_bruto as $d) {
        if ($d['dia'] == $dia) {
            $data_comissao_grafico[] = (float)$d['minha_comissao'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $data_comissao_grafico[] = 0;
    }
}

$labels_json = json_encode($labels);
$data_comissao_grafico_json = json_encode($data_comissao_grafico);


// --- ÚLTIMAS 5 TRANSAÇÕES ---
$stmt_ultimas_transacoes = $pdo->prepare("
    SELECT * FROM relatorios 
    WHERE id_usuario = ? 
    ORDER BY data DESC 
    LIMIT 5
");
$stmt_ultimas_transacoes->execute([$id_usuario_logado]);
$ultimas_transacoes = $stmt_ultimas_transacoes->fetchAll(PDO::FETCH_ASSOC);


include('header.php'); 
?>

<div class="container-fluid">
    <h2 class="mb-4">Minha Área de Operações</h2>

    <!-- Botão de Inserir Nova Transação -->
    <div class="d-grid gap-2 mb-4">
        <a href="insert_transaction.php" class="btn btn-primary btn-lg shadow">
            <i class="fas fa-plus-circle me-2"></i> Registrar Nova Transação
        </a>
    </div>

    <?php echo $message; // Exibe feedback de sucesso/erro ?>

    <!-- Linha de KPIs -->
    <div class="row">
        <!-- Comissão Total -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-coins fa-3x me-3"></i>
                        <div>
                            <h5 class="card-title text-white">Minha Comissão (Total)</h5>
                            <h1 class="display-6 mb-0">R$ <?php echo number_format($minha_comissao_total, 2, ',', '.'); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comissão do Mês -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-3x me-3"></i>
                        <div>
                            <h5 class="card-title text-white">Comissão do Mês (<?php echo date('M/Y'); ?>)</h5>
                            <h1 class="display-6 mb-0">R$ <?php echo number_format($minha_comissao_mes, 2, ',', '.'); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lucro Bruto Gerado -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-bar fa-3x me-3"></i>
                        <div>
                            <h5 class="card-title text-white">Lucro Bruto Gerado (Total)</h5>
                            <h1 class="display-6 mb-0">R$ <?php echo number_format($lucro_bruto_gerado, 2, ',', '.'); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Última Transação -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-secondary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock fa-3x me-3"></i>
                        <div>
                            <h5 class="card-title text-white">Última Transação</h5>
                            <h1 class="display-6 mb-0" style="font-size: 1.5rem; line-height: 1.5;"><?php echo $ultima_transacao_formatada; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Minha Comissão -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Minha Comissão (Últimos 7 dias)</h5>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="userComissionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Últimas Transações -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Minhas Últimas Transações</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Depósito</th>
                                <th>Saque</th>
                                <th>Baú</th>
                                <th class="text-success">Minha Comissão (40%)</th>
                                <th>Ações</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimas_transacoes)): ?>
                                <tr><td colspan="6" class="text-center text-muted">Nenhuma transação registrada.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($ultimas_transacoes as $transacao): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($transacao['data'])); ?></td>
                                <td>R$ <?php echo number_format($transacao['valor_deposito'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($transacao['valor_saque'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($transacao['valor_bau'], 2, ',', '.'); ?></td>
                                <td class="text-success">R$ <?php echo number_format($transacao['comissao_usuario'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="edit_report_entry.php?id=<?php echo $transacao['id_relatorio']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados injetados do PHP
    const labels = <?php echo $labels_json; ?>;
    const dataComissao = <?php echo $data_comissao_grafico_json; ?>;

    // --- Gráfico de Linha (Minha Comissão) ---
    const ctxLine = document.getElementById('userComissionChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Minha Comissão Diária',
                    data: dataComissao,
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
include('footer.php');
?>