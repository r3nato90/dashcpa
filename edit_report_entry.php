<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Apenas Gerentes (todos os níveis) podem ver
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

// 1. Validar ID do relatório
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reports.php?status=error_invalid_id');
    exit;
}
$id_relatorio = (int)$_GET['id'];

// 2. Buscar o relatório e os dados do usuário
$stmt_report = $pdo->prepare("
    SELECT r.*, u.nome AS nome_usuario, u.id_sub_adm 
    FROM relatorios r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    WHERE r.id_relatorio = ?
");
$stmt_report->execute([$id_relatorio]);
$report = $stmt_report->fetch();

if (!$report) {
    header('Location: reports.php?status=error_not_found');
    exit;
}

// 3. Verificação de Permissão
// Se não for Super Admin, verifica se o relatório pertence a um usuário gerenciado por ele
if ($role != 'super_adm' && $report['id_sub_adm'] != $id_logado) {
    header('Location: reports.php?status=error_permission');
    exit;
}

include('templates/header.php');
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-warning">
                    <h4>Corrigir Relatório</h4>
                </div>
                <div class="card-body">
                    <p>Você está editando o relatório de <strong><?php echo htmlspecialchars($report['nome_usuario']); ?></strong></p>
                    <p>Data Original: <strong><?php echo date('d/m/Y H:i', strtotime($report['data'])); ?></strong></p>
                    <hr>
                    
                    <form action="process_edit_report_entry.php" method="POST">
                        <input type="hidden" name="id_relatorio" value="<?php echo $report['id_relatorio']; ?>">
                        
                        <div class="mb-3">
                            <label for="valor_deposito" class="form-label">Valor do DEPÓSITO</label>
                            <input type="number" step="0.01" class="form-control" name="valor_deposito" value="<?php echo $report['valor_deposito']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor_saque" class="form-label">Valor do SAQUE</label>
                            <input type="number" step="0.01" class="form-control" name="valor_saque" value="<?php echo $report['valor_saque']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor_bau" class="form-label">Valor do BAÚ (Saldo Final)</label>
                            <input type="number" step="0.01" class="form-control" name="valor_bau" value="<?php echo $report['valor_bau']; ?>" required>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Atenção:</strong> O Lucro e as Comissões (do usuário e do gerente) serão recalculados automaticamente com base nos novos valores.
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">Salvar Correção</button>
                        <a href="reports.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>