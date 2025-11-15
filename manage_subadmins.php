<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// **** MODIFICAÇÃO: 'admin' (N1) também pode acessar ****
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id'];
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

include('templates/header.php');

if (isset($_GET['status']) && $_GET['status'] == 'updated') {
    echo "<div class'container-fluid'><div class='alert alert-success'>Gerente atualizado!</div></div>";
}

// **** CORREÇÃO DA QUERY (Hierarquia) ****
$params = [$org_id];
if ($role == 'super_adm') {
    // Super-Admin (Dono) vê Admins (N1) e Sub-Admins (N2)
    $query_sql = "SELECT * FROM sub_administradores WHERE org_id = ? AND role IN ('admin', 'sub_adm') ORDER BY role, nome";
} else {
    // Admin (N1) vê a si mesmo E seus Sub-Admins (N2)
    $query_sql = "SELECT * FROM sub_administradores 
                  WHERE org_id = ? AND (role = 'sub_adm' AND parent_admin_id = ?) OR (id_sub_adm = ?)
                  ORDER BY role, nome";
    $params[] = $id_logado;
    $params[] = $id_logado;
}
$stmt = $pdo->prepare($query_sql);
$stmt->execute($params);
$sub_admins = $stmt->fetchAll();
?>

<div class="container-fluid">
    <h2>Gerenciar <?php echo ($role == 'super_adm') ? 'Admins (N1) e Sub-Admins (N2)' : 'Meus Gerentes (N1 e N2)'; ?></h2>
    
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
                        <td>
                            <?php echo htmlspecialchars($admin['nome']); ?>
                            <?php if ($admin['id_sub_adm'] == $id_logado) echo " <span class='badge bg-success'>Você</span>"; ?>
                        </td>
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