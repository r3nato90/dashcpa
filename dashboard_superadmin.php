<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); // Define o Fuso Horário
include('config/logger.php'); // Inclui o sistema de Log

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
$stmt_total_users = $pdo->query("SELECT COUNT(*) FROM usuarios");
$total_users = $stmt_total_users->fetchColumn();
$stmt_total_managers = $pdo->query("SELECT COUNT(*) FROM sub_administradores WHERE role IN ('admin', 'sub_adm')");
$total_managers = $stmt_total_managers->fetchColumn();
$stmt_total_lucro = $pdo->query("SELECT SUM(lucro_diario) FROM relatorios");
$total_lucro = $stmt_total_lucro->fetchColumn() ?? 0;
$stmt_total_comissao_gerentes = $pdo->query("SELECT SUM(comissao_sub_adm) FROM relatorios");
$total_comissao_gerentes = $stmt_total_comissao_gerentes->fetchColumn() ?? 0;

// --- NOVAS QUERIES PARA OS GRÁFICOS ---

// 1. Dados para o Gráfico Donut (Distribuição do Lucro)
$stmt_total_comissao_users = $pdo->query("SELECT SUM(comissao_usuario) FROM relatorios");
$total_comissao_usuarios = $stmt_total_comissao_users->fetchColumn() ?? 0;
// Calcula o lucro líquido (o que sobra para o sistema)
$lucro_liquido_sistema = $total_lucro - $total_comissao_gerentes - $total_comissao_usuarios;

// 2. Dados para o Gráfico de Linha (Últimos 7 dias)
$stmt_line_chart = $pdo->query("
    SELECT 
        DATE(data) as dia, 
        SUM(lucro_diario) as lucro_total,
        SUM(comissao_sub_adm) as comissao_total_gerente
    FROM relatorios
    WHERE data >= CURDATE() - INTERVAL 7 DAY
    GROUP BY dia
    ORDER BY dia ASC
");
$line_chart_data = $stmt_line_chart->fetchAll(PDO::FETCH_ASSOC);

// Preparar dados para o JS (preenche dias vazios com 0)
$chart_labels = [];
$chart_lucro = [];
$chart_comissao_gerente = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('d/m', strtotime($date)); // Formato '30/10'
    $chart_lucro[$date] = 0;
    $chart_comissao_gerente[$date] = 0;
}
foreach ($line_chart_data as $row) {
    $chart_lucro[$row['dia']] = $row['lucro_total'];
    $chart_comissao_gerente[$row['dia']] = $row['comissao_total_gerente'];
}

// Busca todos os usuários (para o modal)
$stmt_users = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome");
$all_users = $stmt_users->fetchAll();
$hoje = date('Y-m-d');

include('templates/header.php'); 
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Painel do Super Administrador</h2>
        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalEnviarRelatorio">
            <i class="fas fa-plus-circle me-2"></i> Enviar Novo Relatório
        </button>
    </div>
    <?php echo $message; ?>

    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body text-center">
                <h5 class="card-title text-muted">Total de Operadores</h5>
                <p class="h2 display-5 fw-bold"><?php echo $total_users; ?></p>
                <i class="fas fa-users fa-3x text-primary opacity-25"></i>
            </div></div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body text-center">
                <h5 class="card-title text-muted">Total de Gerentes</h5>
                <p class="h2 display-5 fw-bold"><?php echo $total_managers; ?></p>
                <i class="fas fa-user-shield fa-3x text-info opacity-25"></i>
            </div></div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body text-center">
                <h5 class="card-title text-muted">Lucro Total (Geral)</h5>
                <p class="h2 display-5 fw-bold">R$ <?php echo number_format($total_lucro, 2, ',', '.'); ?></p>
                <i class="fas fa-dollar-sign fa-3x text-success opacity-25"></i>
            </div></div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body text-center">
                <h5 class="card-title text-muted">Comissão Gerentes (Geral)</h5>
                <p class="h2 display-5 fw-bold">R$ <?php echo number_format($total_comissao_gerentes, 2, ',', '.'); ?></p>
                <i class="fas fa-percentage fa-3x text-warning opacity-25"></i>
            </div></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i>Desempenho (Últimos 7 Dias)
                </div>
                <div class="card-body">
                    <canvas id="lucroLineChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i>Distribuição do Lucro (Total)
                </div>
                <div class="card-body">
                    <canvas id="comissaoDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h3 class="h4">Relatórios Recentes (Todos Usuários)</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table id="relatoriosTable" class="table table-striped table-bordered table-sm">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Usuário</th><th>Depósito</th><th>Saque</th><th>Baú</th>
                                    <th>Lucro</th><th>Com. U</th><th>Com. Sub</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt_reports = $pdo->query("
                                    SELECT r.*, u.nome FROM relatorios r 
                                    LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                    WHERE u.id_usuario IS NOT NULL ORDER BY r.data DESC LIMIT 15
                                ");
                                while ($row = $stmt_reports->fetch()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['nome']) . "</td>
                                            <td>R$ " . number_format($row['valor_deposito'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['valor_saque'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['valor_bau'], 2, ',', '.') . "</td>
                                            <td>R$ " . number_format($row['lucro_diario'], 2, ',', '.') . "</td>
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

    <hr class="my-4">
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-warning text-center shadow-sm">
                <h6 class="alert-heading mb-0" style="font-weight: 300;">Próximo Pagamento do Servidor em:</h6>
                <p class="h4" id="payment-countdown-main" style="font-weight: 700;">Calculando...</p>
                <a href="https://mpago.la/1VcrHae" target="_blank" class="btn btn-danger btn-sm">Pagar Agora</a>
            </div>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const countdownElement = document.getElementById("payment-countdown-main");
                if (countdownElement) {
                    const anchorDate = new Date("2025-10-01T00:00:00").getTime();
                    const cycleLength = 30 * 24 * 60 * 60 * 1000;
                    function updateTimerMain() {
                        const now = new Date().getTime();
                        const diff = now - anchorDate;
                        const elapsedInCycle = diff % cycleLength;
                        const timeRemaining = cycleLength - elapsedInCycle;
                        const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        countdownElement.innerHTML = days + "d " + hours + "h";
                    }
                    updateTimerMain(); setInterval(updateTimerMain, 1000 * 60);
                }
            });
            </script>
        </div>
    </div>

    <hr class="my-4">
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2>Registro de Atividades (Últimas 50)</h2>
                <a href="view_logs.php" class="btn btn-primary">Ver Logs Diários (Histórico)</a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover table-sm">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Data</th><th>Usuário</th><th>Role</th><th>Ação</th><th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt_logs = $pdo->query("SELECT * FROM logs ORDER BY data DESC LIMIT 50");
                                foreach ($stmt_logs->fetchAll() as $log) :
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['data'])); ?></td>
                                    <td><?php echo htmlspecialchars($log['nome_usuario_acao']); ?> (ID: <?php echo $log['id_usuario_acao'] ?? 'N/A'; ?>)</td>
                                    <td><?php echo htmlspecialchars($log['role_usuario_acao']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($log['acao_tipo']); ?></span></td>
                                    <td><?php echo htmlspecialchars($log['descricao']); ?></td>
                                </tr>
                                <?php endforeach; ?>
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
                        <label for="usuario_id" class="form-label">Usuário (Todos)</label>
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
                    borderColor: 'rgba(25, 135, 84, 1)', // Verde
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.2
                }, {
                    label: 'Comissão Gerentes (R$)',
                    data: <?php echo json_encode(array_values($chart_comissao_gerente)); ?>,
                    borderColor: 'rgba(255, 193, 7, 1)', // Amarelo
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
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
                        'rgba(25, 135, 84, 0.8)', // Verde (Sistema)
                        'rgba(255, 193, 7, 0.8)', // Amarelo (Gerentes)
                        'rgba(13, 110, 253, 0.8)'  // Azul (Usuários)
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
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