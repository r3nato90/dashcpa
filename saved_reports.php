<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); // Garante fuso horário

// Apenas Gerentes (todos os níveis) podem ver
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'sub_adm', 'super_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

// Mensagens de status
$message = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'saved') {
        $message = "<div class='alert alert-success'>Relatório salvo com sucesso!</div>";
    }
    if ($_GET['status'] == 'deleted') {
        $message = "<div class='alert alert-success'>Relatório salvo apagado com sucesso!</div>";
    }
}

// --- **** CORREÇÃO NA QUERY DE BUSCA **** ---
$query = "SELECT * FROM saved_reports";
$params = [];

// Se for Admin ou Sub-Admin (NÃO Super Admin), filtra por ID
if ($role == 'admin' || $role == 'sub_adm') {
    $query .= " WHERE id_salvo_por = ?";
    $params[] = $id_logado;
}
// Se for Super Admin, o WHERE não é adicionado, mostrando todos.

$query .= " ORDER BY data_criacao DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$relatorios_salvos = $stmt->fetchAll();
// --- **** FIM DA CORREÇÃO **** ---

include('templates/header.php');
?>

<div class="container mt-5">
    <h2>Relatórios Salvos</h2>
    
    <?php if ($role == 'super_adm'): ?>
        <p>Todos os relatórios salvos por todos os gerentes do sistema.</p>
    <?php else: ?>
        <p>Relatórios com filtros que você salvou.</p>
    <?php endif; ?>
    
    <?php echo $message; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome do Relatório</th>
                        <th>Salvo Por</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($relatorios_salvos)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum relatório salvo encontrado.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($relatorios_salvos as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['nome_relatorio']); ?></td>
                        <td><?php echo htmlspecialchars($r['nome_salvo_por']); ?> (ID: <?php echo $r['id_salvo_por']; ?>)</td>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['data_criacao'])); ?></td>
                        <td>
                            <a href="reports.php?report_id=<?php echo $r['id_report_salvo']; ?>" class="btn btn-primary btn-sm">Ver</a>
                            
                            <a href="delete_report.php?id=<?php echo $r['id_report_salvo']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja apagar este relatório salvo?');">Apagar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('templates/footer.php'); ?>