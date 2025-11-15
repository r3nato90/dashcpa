<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// --- Lógica do Filtro ---
$date_filter = date('Y-m-d'); 
$org_filter = ''; // Filtro de Organização

// Busca todas as organizações para o dropdown
$stmt_orgs = $pdo->query("SELECT org_id, org_name FROM organizations ORDER BY org_name");
$organizations = $stmt_orgs->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['date_filter'])) $date_filter = $_POST['date_filter'];
    if (isset($_POST['org_filter'])) $org_filter = $_POST['org_filter'];
}

// --- Query de Logs (Global) ---
$query_logs = "SELECT l.*, o.org_name FROM logs l LEFT JOIN organizations o ON l.org_id = o.org_id WHERE DATE(l.data) = ?";
$params_logs = [$date_filter];

if (!empty($org_filter)) {
    $query_logs .= " AND l.org_id = ?";
    $params_logs[] = $org_filter;
}
$query_logs .= " ORDER BY l.data DESC";

$stmt_logs = $pdo->prepare($query_logs);
$stmt_logs->execute($params_logs);
$logs = $stmt_logs->fetchAll();

// **** CORREÇÃO: USA O NOVO HEADER ****
include('templates/header-new.php');
?>

<div class="container-fluid">
    <h2>Logs Globais (Todos os Clientes)</h2>
    <p>Selecione uma data e (opcionalmente) uma organização para ver as atividades.</p>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="platform_logs.php" method="POST">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="date_filter" class="form-label">Selecionar Data:</label>
                        <input type="date" class="form-control" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label for="org_filter" class="form-label">Filtrar por Organização:</label>
                        <select name="org_filter" class="form-control">
                            <option value="">Todas as Organizações</option>
                            <?php foreach ($organizations as $org): ?>
                                <option value="<?php echo $org['org_id']; ?>" <?php if ($org_filter == $org['org_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($org['org_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</Logs</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Logs de <?php echo date('d/m/Y', strtotime($date_filter)); ?></div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-striped table-hover table-sm">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Data / Hora</th>
                            <th>Organização (Cliente)</th>
                            <th>Usuário da Ação</th>
                            <th>Role</th>
                            <th>Tipo de Ação</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" class="text-center">Nenhum log encontrado.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($logs as $log) : ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['data'])); ?></td>
                            <td><?php echo htmlspecialchars($log['org_name']); ?> (ID: <?php echo $log['org_id']; ?>)</td>
                            <td><?php echo htmlspecialchars($log['nome_usuario_acao']); ?></td>
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

<?php 
// **** CORREÇÃO: USA O NOVO FOOTER ****
include('templates/footer-new.php'); 
?>