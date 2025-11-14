<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Logs do Sistema";
$breadcrumb_active = "Logs";

// Verificação de segurança: Apenas Super Admin pode acessar os logs
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm') {
    header('Location: login.php');
    exit;
}

// --- Filtragem ---
$date_filter = $_GET['date'] ?? date('Y-m-d');
$role_filter = $_GET['role'] ?? ''; // Filtro por papel

// --- Query de Logs ---
$query = "SELECT * FROM logs WHERE DATE(timestamp) = ?";
$params = [$date_filter];

if (!empty($role_filter)) {
    $query .= " AND user_role = ?";
    $params[] = $role_filter;
}

$query .= " ORDER BY timestamp DESC";
$stmt_logs = $pdo->prepare($query);
$stmt_logs->execute($params);
$logs = $stmt_logs->fetchAll(PDO::FETCH_ASSOC);

// Opções de Papel para o filtro
$role_options = ['super_adm', 'admin', 'sub_adm', 'usuario', 'visitante'];

include('header.php'); 
?>

<h2 class="mb-4">Logs de Atividade do Sistema</h2>

<!-- Card de Filtros -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Filtros de Logs</h5>
    </div>
    <div class="card-body">
        <form id="filterLogsForm" method="GET" action="view_logs.php">
            <div class="row g-3 align-items-end">
                <!-- Filtro de Data -->
                <div class="col-md-4">
                    <label for="date_filter" class="form-label">Data</label>
                    <input type="date" class="form-control" id="date_filter" name="date" value="<?php echo htmlspecialchars($date_filter); ?>" required>
                </div>

                <!-- Filtro de Papel -->
                <div class="col-md-4">
                    <label for="role_filter" class="form-label">Filtrar por Papel</label>
                    <select class="form-select" id="role_filter" name="role">
                        <option value="">Todos os Papéis</option>
                        <?php foreach ($role_options as $role_opt): ?>
                            <option value="<?php echo $role_opt; ?>" 
                                    <?php echo ($role_filter == $role_opt) ? 'selected' : ''; ?>>
                                <?php echo strtoupper(str_replace('_', ' ', $role_opt)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botão de Filtrar -->
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Filtrar Logs
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Logs -->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Registros de Logs para <?php echo date('d/m/Y', strtotime($date_filter)); ?> (<?php echo count($logs); ?> Registros)</h5>
    </div>
    <div class="card-body table-responsive" style="max-height: 600px; overflow-y: auto;">
        <?php if (empty($logs)): ?>
            <div class="alert alert-warning text-center">Nenhum log encontrado com os filtros selecionados.</div>
        <?php else: ?>
        <table class="table table-striped table-hover table-sm">
            <thead class="table-dark sticky-top" style="z-index: 1;">
                <tr>
                    <th>Timestamp</th>
                    <th>Ação</th>
                    <th>Usuário ID</th>
                    <th>Papel</th>
                    <th>IP</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['timestamp'])); ?></td>
                    <td><?php echo htmlspecialchars($log['acao']); ?></td>
                    <td><?php echo htmlspecialchars($log['user_id'] ?? 'N/A'); ?></td>
                    <td><span class="badge bg-secondary"><?php echo strtoupper(str_replace('_', ' ', htmlspecialchars($log['user_role']))); ?></span></td>
                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    <td title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                        <?php echo htmlspecialchars(substr($log['user_agent'], 0, 50)) . '...'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include('footer.php'); ?>