<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Meus Relatórios Salvos";
$breadcrumb_active = "Relatórios Salvos";

// Verificação de segurança: Apenas Gerentes (Super e Sub) podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$id_logado = $_SESSION['user_id'];
$message = "";

// Mensagens de status
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'deleted') {
        $message = "<div class='alert alert-success mt-3'>Relatório salvo excluído com sucesso!</div>";
    } elseif ($_GET['status'] == 'error_delete') {
         $message = "<div class='alert alert-danger mt-3'>Erro ao excluir relatório salvo.</div>";
    }
}


// --- Query para buscar relatórios salvos pelo usuário logado ---
$stmt_reports = $pdo->prepare("
    SELECT r.*, s.nome AS nome_admin
    FROM relatorios_salvos r
    LEFT JOIN sub_administradores s ON r.admin_id = s.id
    WHERE r.id_admin = ?
    ORDER BY r.data_salva DESC
");
$stmt_reports->execute([$id_logado]);
$reports_salvos = $stmt_reports->fetchAll(PDO::FETCH_ASSOC);


include('header.php'); 
?>

<h2 class="mb-4">Meus Filtros de Relatório Salvos</h2>

<?php echo $message; // Exibe feedback de status ?>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <?php if (empty($reports_salvos)): ?>
            <div class="alert alert-info text-center">Você ainda não tem nenhum filtro de relatório salvo.</div>
        <?php else: ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nome do Relatório</th>
                    <th>Período</th>
                    <th>Gerente Filtrado</th>
                    <th>Operadores Filtrados</th>
                    <th>Salvo em</th>
                    <th>Ações</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports_salvos as $report): 
                    // Tenta decodificar os IDs de usuário
                    $user_ids_list = json_decode($report['user_ids'] ?? '[]', true);
                    $num_users = is_array($user_ids_list) ? count($user_ids_list) : 0;
                    $users_display = $num_users > 0 ? "{$num_users} Operador(es)" : "Todos";
                    
                    // Constrói os parâmetros para carregar o relatório
                    $load_params = http_build_query([
                        'load_report_id' => $report['id']
                    ]);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($report['nome']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($report['date_start'])) . ' a ' . date('d/m/Y', strtotime($report['date_end'])); ?></td>
                    <td>
                        <?php 
                            if (!empty($report['admin_id']) && $report['nome_admin']) {
                                echo htmlspecialchars($report['nome_admin']);
                            } else {
                                echo "Todos";
                            }
                        ?>
                    </td>
                    <td><?php echo $users_display; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($report['data_salva'])); ?></td>
                    <td>
                        <a href="reports.php?<?php echo $load_params; ?>" class="btn btn-success btn-sm me-2" data-bs-toggle="tooltip" title="Carregar Filtros">
                            <i class="fas fa-search"></i> Carregar
                        </a>
                        <a href="delete_report.php?id=<?php echo $report['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja APAGAR este relatório salvo?');">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include('footer.php'); ?>