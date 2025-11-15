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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}
$id_usuario = (int)$_GET['id'];

// **** MODIFICADO: Busca DENTRO da organização ****
$query_user = "SELECT * FROM usuarios WHERE id_usuario = ? AND org_id = ?";
$params_user = [$id_usuario, $org_id];

if ($role == 'admin' || $role == 'sub_adm') {
    $query_user .= " AND id_sub_adm = ?";
    $params_user[] = $id_logado;
}
$stmt_user = $pdo->prepare($query_user);
$stmt_user->execute($params_user);
$user = $stmt_user->fetch();

if (!$user) {
    header('Location: manage_users.php?status=error_permission');
    exit;
}

$admins_list = [];
if ($role == 'super_adm') {
    // **** MODIFICADO: Busca gerentes DENTRO da organização ****
    $stmt_admins = $pdo->prepare("SELECT id_sub_adm, nome, role FROM sub_administradores WHERE org_id = ? ORDER BY nome");
    $stmt_admins->execute([$org_id]);
    $admins_list = $stmt_admins->fetchAll();
}
include('templates/header.php');
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white"><h4>Editar Usuário: <?php echo htmlspecialchars($user['nome']); ?></h4></div>
                <div class="card-body">
                    <form action="process_edit_user.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">
                        <div class="mb-3"><label for="nome" class="form-label">Nome</label><input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required></div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                        <div class="mb-3"><label for="senha" class="form-label">Nova Senha (Deixe em branco)</label><input type="password" class="form-control" name="senha"></div>
                        <div class="mb-3"><label for="percentual_comissao" class="form-label">Percentual de Comissão (%)</label><input type="number" step="0.01" class="form-control" name="percentual_comissao" value="<?php echo $user['percentual_comissao']; ?>" required></div>
                        
                        <?php if ($role == 'super_adm'): ?>
                        <div class="mb-3"><label for="id_sub_adm" class="form-label">Vincular a (Gerente)</label>
                            <select class="form-control" name="id_sub_adm">
                                <option value="">Nenhum (Sem vínculo)</option>
                                <?php foreach ($admins_list as $admin): ?>
                                    <option value="<?php echo $admin['id_sub_adm']; ?>" <?php echo ($admin['id_sub_adm'] == $user['id_sub_adm']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($admin['nome']) . " (" . $admin['role'] . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
                    </form>
                    <hr>
                    <a href="delete_user.php?id=<?php echo $user['id_usuario']; ?>" class="btn btn-danger w-100" onclick="return confirm('Tem certeza?');">Apagar Usuário</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>