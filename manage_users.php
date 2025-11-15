<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

include('templates/header.php');

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'updated') echo "<div class='container-fluid'><div class='alert alert-success'>Usuário atualizado!</div></div>";
    if ($_GET['status'] == 'deleted') echo "<div class='container-fluid'><div class='alert alert-success'>Usuário apagado!</div></div>";
}

// **** MODIFICADO: Query DENTRO da organização ****
$query = "
    SELECT u.*, s.nome AS nome_sub_adm 
    FROM usuarios u
    LEFT JOIN sub_administradores s ON u.id_sub_adm = s.id_sub_adm
    WHERE u.org_id = ? 
";
$params = [$org_id];

if ($role == 'admin' || $role == 'sub_adm') {
    $query .= " AND u.id_sub_adm = ?";
    $params[] = $id_logado;
}
$query .= " ORDER BY u.nome";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();
?>

<div class="container-fluid">
    <h2>Gerenciar Usuários (Operadores)</h2>
    <p>Painel para editar ou apagar usuários.</p>
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th> <th>Email</th> <th>Comissão (%)</th> <th>Vinculado a</th> <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="5" class="text-center">Nenhum usuário encontrado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo number_format($usuario['percentual_comissao'], 2, ',', '.'); ?>%</td>
                        <td><?php echo $usuario['nome_sub_adm'] ? htmlspecialchars($usuario['nome_sub_adm']) : "<span class='text-muted'>Nenhum</span>"; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="delete_user.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?');">Apagar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>