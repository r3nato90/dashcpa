<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); // Define o Fuso Horário

// Verificar se o usuário está logado
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'usuario') {
    header('Location: login.php');
    exit;
}
$id_usuario_logado = $_SESSION['id'];
$message = (isset($_GET['status']) && $_GET['status'] == 'success') ? "<div class='alert alert-success'>Relatório enviado com sucesso!</div>" : "";


// --- Lógica do Filtro de Data ---
$date_start = date('Y-m-d');
$date_end = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'filtrar') {
    if (!empty($_POST['date_start'])) {
        $date_start = $_POST['date_start'];
    }
    if (!empty($_POST['date_end'])) {
        $date_end = $_POST['date_end'];
    }
}
$date_end_query = $date_end . ' 23:59:59';
// --- Fim da Lógica do Filtro ---

include('templates/header.php'); 
?>

<!-- Título -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Painel do Operador</h1>
        <p class="text-muted-foreground">Envie seus relatórios diários e acompanhe seus ganhos.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
         <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h3 class="text-lg font-semibold tracking-tight">Enviar Relatório de Operação</h3>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="process_transaction.php" method="POST">
                    <input type="hidden" name="usuario_id" value="<?php echo $id_usuario_logado; ?>">
                    <div class="mb-3">
                        <label for="deposito" class="form-label">Valor do DEPÓSITO</label>
                        <input type="number" step="0.01" class="form-control" name="deposito" required>
                    </div>
                    <div class="mb-3">
                        <label for="saque" class="form-label">Valor do SAQUE</label>
                        <input type="number" step="0.01" class="form-control" name="saque" required>
                    </div>
                    <div class="mb-3">
                        <label for="bau" class="form-label">Valor do BAÚ (Saldo Final)</label>
                        <input type="number" step="0.01" class="form-control" name="bau" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Enviar Relatório</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm mb-4">
             <div class="card-header">
                <h3 class="text-lg font-semibold tracking-tight">Seus Relatórios</h3>
            </div>
            <div class="card-body">
                <form action="dashboard_usuario.php" method="POST" class="mb-3 p-3 border rounded shadow-sm">
                    <input type="hidden" name="action" value="filtrar">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label for="date_start" class="form-label small mb-1">De:</label>
                            <input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>">
                        </div>
                        <div class="col-md-5">
                            <label for="date_end" class="form-label small mb-1">Até:</label>
                            <input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Depósito</th>
                                <th>Saque</th>
                                <th>Baú</th>
                                <th>Lucro Bruto</th>
                                <th>Sua Comissão</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $stmt = $pdo->prepare("
                                    SELECT * FROM relatorios 
                                    WHERE id_usuario = ? 
                                    AND (data BETWEEN ? AND ?)
                                    ORDER BY data DESC
                                ");
                                $stmt->execute([$id_usuario_logado, $date_start, $date_end_query]);
                                $relatorios = $stmt->fetchAll();

                                // Variáveis para os totais
                                $total_lucro = 0;
                                $total_comissao = 0;

                                if (count($relatorios) == 0) {
                                    echo "<tr><td colspan='6' class='text-center'>Nenhum relatório encontrado para este período.</td></tr>";
                                } else {
                                    foreach ($relatorios as $r) {
                                        // Soma os totais
                                        $total_lucro += $r['lucro_diario'];
                                        $total_comissao += $r['comissao_usuario'];
                                        
                                        echo "<tr>
                                                <td>" . date('d/m/Y H:i', strtotime($r['data'])) . "</td>
                                                <td>R$ " . number_format($r['valor_deposito'], 2, ',', '.') . "</td>
                                                <td>R$ " . number_format($r['valor_saque'], 2, ',', '.') . "</td>
                                                <td>R$ " . number_format($r['valor_bau'], 2, ',', '.') . "</td>
                                                <td>R$ " . number_format($r['lucro_diario'], 2, ',', '.') . "</td>
                                                <td>R$ " . number_format($r['comissao_usuario'], 2, ',', '.') . "</td>
                                              </tr>";
                                    }
                                }
                            ?>
                        </tbody>
                        
                        <tfoot class="table-group-divider">
                            <tr>
                                <td colspan="4" class="text-end"><strong>TOTAIS (Período Filtrado):</strong></td>
                                
                                <td class="text-success fw-bold">
                                    R$ <?php echo number_format($total_lucro, 2, ',', '.'); ?>
                                </td>
                                <td class="text-success fw-bold">
                                    R$ <?php echo number_format($total_comissao, 2, ',', '.'); ?>
                                </td>
                            </tr>
                        </tfoot>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>