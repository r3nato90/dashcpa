<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Controle de Despesas Diárias";
$breadcrumb_active = "Controle Diário";

// Verificação de segurança: Apenas Gerentes (Super e Sub) podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$id_admin_logado = $_SESSION['user_id'];
$message = "";

// --- Lógica para buscar despesas ---
$date_selected = $_GET['date'] ?? date('Y-m-d');

// Buscar despesas do dia selecionado para o admin logado
$stmt_expenses = $pdo->prepare("SELECT * FROM despesas_diarias WHERE id_admin_logado = ? AND data = ?");
$stmt_expenses->execute([$id_admin_logado, $date_selected]);
$current_expenses = $stmt_expenses->fetch(PDO::FETCH_ASSOC);

$gastos_proxy_value = $current_expenses ? number_format($current_expenses['gastos_proxy'], 2, ',', '.') : '0,00';
$gastos_numeros_value = $current_expenses ? number_format($current_expenses['gastos_numeros'], 2, ',', '.') : '0,00';

// --- Lógica para buscar histórico de despesas (últimos 30 dias) ---
$date_30_days_ago = date('Y-m-d', strtotime('-30 days'));
$query_history = "
    SELECT 
        data, 
        (gastos_proxy + gastos_numeros) AS total_gasto
    FROM despesas_diarias
    WHERE id_admin_logado = ? AND data >= ?
    ORDER BY data ASC
";
$stmt_history = $pdo->prepare($query_history);
$stmt_history->execute([$id_admin_logado, $date_30_days_ago]);
$history_data = $stmt_history->fetchAll(PDO::FETCH_ASSOC);

$history_labels = [];
$history_data_values = [];
$total_gastos_30d = 0;

// Preenche os dados para o gráfico
foreach ($history_data as $d) {
    $history_labels[] = date('d/m', strtotime($d['data']));
    $history_data_values[] = (float)$d['total_gasto'];
    $total_gastos_30d += (float)$d['total_gasto'];
}
$history_labels_json = json_encode($history_labels);
$history_data_values_json = json_encode($history_data_values);


include('header.php'); 
?>

<h2 class="mb-4">Controle Diário de Gastos Operacionais</h2>

<p class="text-muted">Registre os gastos operacionais (Proxy e Números) para a sua gerência. Os dados são salvos por dia e por gerente.</p>

<?php echo $message; // Exibe feedback de status (se houver) ?>

<!-- Card de Seleção de Data e Acesso -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form id="dateSelectForm" method="GET" action="daily_control.php">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="date_picker" class="form-label">Selecionar Data</label>
                    <input type="date" class="form-control" id="date_picker" name="date" 
                           value="<?php echo htmlspecialchars($date_selected); ?>" required 
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-search me-2"></i> Carregar Despesas
                    </button>
                </div>
                <div class="col-md-4 text-end">
                    <h5 class="mb-0 text-muted">
                        Despesas para: **<?php echo date('d/m/Y', strtotime($date_selected)); ?>**
                    </h5>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Card de Formulário de Despesas -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Registro de Gastos (R$)</h5>
            </div>
            <div class="card-body">
                <form id="expensesForm">
                    <input type="hidden" name="data" value="<?php echo htmlspecialchars($date_selected); ?>">
                    
                    <div class="mb-3">
                        <label for="gastos_proxy" class="form-label">Gastos com Proxy (R$)</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control currency-mask" id="gastos_proxy" name="gastos_proxy" 
                                   value="<?php echo $gastos_proxy_value; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="gastos_numeros" class="form-label">Gastos com Números/Outros (R$)</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" class="form-control currency-mask" id="gastos_numeros" name="gastos_numeros" 
                                   value="<?php echo $gastos_numeros_value; ?>" required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-upload me-2"></i> Salvar Despesas
                        </button>
                    </div>
                    <div id="saveFeedback" class="mt-3 text-center"></div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Card de Histórico e Total -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Histórico da Gerência (Últimos 30 dias)</h5>
            </div>
            <div class="card-body">
                <h4 class="mb-3">Total Gasto (30D): <span class="text-danger">R$ <?php echo number_format($total_gastos_30d, 2, ',', '.'); ?></span></h4>
                <div style="height: 250px;">
                    <canvas id="expensesHistoryChart"></canvas>
                </div>
                <p class="mt-3 text-muted text-center"><small>Representação dos gastos totais por dia.</small></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Usa a biblioteca jQuery Mask Plugin (já deve estar carregada via footer.php)
    $(document).ready(function(){
        // Aplica a máscara de moeda
        $('.currency-mask').mask('0#.#00,00', {reverse: true});

        // Gráfico de Histórico
        const ctxHistory = document.getElementById('expensesHistoryChart');
        if (ctxHistory) {
            new Chart(ctxHistory, {
                type: 'bar',
                data: {
                    labels: <?php echo $history_labels_json; ?>,
                    datasets: [{
                        label: 'Gasto Total Diário',
                        data: <?php echo $history_data_values_json; ?>,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)', 
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
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
                        legend: { display: false },
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
        
        // Processamento da Submissão do Formulário AJAX
        $('#expensesForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const feedbackDiv = $('#saveFeedback');
            const dataToSave = {};

            // Pega o valor formatado, remove máscara e converte para float
            function getUnmaskedValue(id) {
                const valueStr = $('#' + id).val() || '0,00';
                return parseFloat(valueStr.replace(/\./g, '').replace(',', '.'));
            }

            dataToSave.data = form.find('input[name="data"]').val();
            dataToSave.gastos_proxy = getUnmaskedValue('gastos_proxy');
            dataToSave.gastos_numeros = getUnmaskedValue('gastos_numeros');
            
            if (dataToSave.gastos_proxy < 0 || dataToSave.gastos_numeros < 0) {
                 feedbackDiv.html('<div class="alert alert-danger">Os valores não podem ser negativos.</div>');
                 return;
            }

            feedbackDiv.html('<div class="text-center text-warning"><i class="fas fa-spinner fa-spin me-2"></i> Salvando...</div>');

            $.ajax({
                url: 'save_daily_expenses.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(dataToSave),
                success: function(response) {
                    if (response.status === 'success') {
                        feedbackDiv.html('<div class="alert alert-success">' + response.message + '</div>');
                        // Recarrega a página para atualizar o gráfico/histórico (ou apenas o gráfico via AJAX se for mais complexo)
                        setTimeout(function() {
                            window.location.href = 'daily_control.php?date=' + dataToSave.data + '&status=saved';
                        }, 1000);
                    } else {
                        feedbackDiv.html('<div class="alert alert-danger">Erro: ' + (response.message || 'Falha ao salvar.') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    feedbackDiv.html('<div class="alert alert-danger">Erro de comunicação com o servidor. Tente novamente.</div>');
                    console.error("AJAX Error:", status, error);
                }
            });
        });
        
    });
</script>

<?php 
// Inclui o save_daily_expenses.php se houver um status de sucesso
if (isset($_GET['status']) && $_GET['status'] == 'saved') {
    $message = "<div class='alert alert-success mt-3'>Despesas salvas/atualizadas com sucesso para " . date('d/m/Y', strtotime($date_selected)) . "!</div>";
}
include('footer.php');
?>