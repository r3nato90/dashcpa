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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_subadmins.php');
    exit;
}
$id_sub_adm = (int)$_GET['id'];

// **** MODIFICADO: Busca DENTRO da organização ****
$stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ? AND role != 'platform_owner'");
$stmt->execute([$id_sub_adm, $org_id]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: manage_subadmins.php');
    exit;
}
$username_value = (isset($admin['username'])) ? htmlspecialchars($admin['username']) : '';
include('templates/header.php');
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white"><h4>Editar Gerente: <?php echo htmlspecialchars($admin['nome']); ?></h4></div>
                <div class="card-body">
                    <form action="process_edit_subadmin.php" method="POST">
                        <input type="hidden" name="id_sub_adm" value="<?php echo $admin['id_sub_adm']; ?>">
                        <div class="mb-3"><label for="nome" class="form-label">Nome</label><input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($admin['nome']); ?>" required></div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required></div>
                        <div class="mb-3"><label for="username" class="form-label">Nome de Usuário (Username)</label><input type="text" class="form-control" name="username" value="<?php echo $username_value; ?>" required></div>
                        <div class="mb-3"><label for="senha" class="form-label">Nova Senha (Deixe em branco)</label><input type="password" class="form-control" name="senha"></div>
                        <div class="mb-3"><label for="percentual_comissao" class="form-label">Percentual de Comissão (%)</label><input type="number" step="0.01" class="form-control" name="percentual_comissao" value="<?php echo $admin['percentual_comissao']; ?>" required></div>
                        <div class="mb-3"><label for="role" class="form-label">Permissão (Role)</label>
                            <select class="form-control" name="role" required>
                                <option value="sub_adm" <?php echo ($admin['role'] == 'sub_adm') ? 'selected' : ''; ?>>sub_adm (Gerente)</option>
                                <option value="admin" <?php echo ($admin['role'] == 'admin') ? 'selected' : ''; ?>>admin (Gerente)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
                        <a href="manage_subadmins.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>