<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm' || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

$date_filter = date('Y-m-d'); 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date_filter'])) {
    $date_filter = $_POST['date_filter'];
}

// **** MODIFICADO: Busca DENTRO da organização ****
$stmt_logs = $pdo->prepare("
    SELECT * FROM logs 
    WHERE DATE(data) = ? AND org_id = ?
    ORDER BY data DESC
");
$stmt_logs->execute([$date_filter, $org_id]);
$logs = $stmt_logs->fetchAll();

include('templates/header.php');
?>

<div class="container-fluid">
    <h2>Registro de Atividades Diárias (Sua Organização)</h2>
    <p>Selecione uma data para ver as atividades da sua organização.</p>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="view_logs.php" method="POST" class="d-flex justify-content-start align-items-end">
                <div class="me-3">
                    <label for="date_filter" class="form-label">Selecionar Data:</label>
                    <input type="date" class="form-control" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</Logs</button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header">Logs de <?php echo date('d/m/Y', strtotime($date_filter)); ?></div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-striped table-hover table-sm">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Data / Hora</th><th>Usuário da Ação</th><th>Role</th>
                            <th>Tipo de Ação</th><th>Descrição Detalhada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="5" class="text-center">Nenhum log encontrado para esta data.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($logs as $log) : ?>
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
<?php include('templates/footer.php'); ?>