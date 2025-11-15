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

include('templates/header.php');

if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    echo "<div class='container-fluid'><div class='alert alert-success'>Gerente atualizado!</div></div>";
}

// **** MODIFICADO: Busca DENTRO da organização e exclui 'super_adm' ****
$stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE org_id = ? AND role != 'super_adm' ORDER BY nome");
$stmt->execute([$org_id]);
$sub_admins = $stmt->fetchAll();
?>

<div class="container-fluid">
    <h2>Gerenciar Administradores e Sub-Admins</h2>
    <p>Painel para editar dados e permissões dos gerentes da sua organização.</p>
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th><th>Email</th><th>Username</th><th>Permissão (Role)</th>
                        <th>Comissão (%)</th><th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sub_admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['nome']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td>
                            <span class="<?php echo $admin['role'] == 'admin' ? 'badge bg-danger' : 'badge bg-info'; ?>">
                                <?php echo htmlspecialchars($admin['role']); ?>
                            </span>
                        </td>
                        <td><?php echo number_format($admin['percentual_comissao'], 2, ',', '.'); ?>%</td>
                        <td>
                            <a href="edit_subadmin.php?id=<?php echo $admin['id_sub_adm']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>