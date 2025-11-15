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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_subadmins.php');
    exit;
}
$id_sub_adm = (int)$_GET['id'];

// **** CORREÇÃO DA QUERY (Hierarquia) ****
// Busca o gerente que está sendo editado
$query_sql = "SELECT * FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ? AND role != 'platform_owner'";
$params_sql = [$id_sub_adm, $org_id];

$stmt = $pdo->prepare($query_sql);
$stmt->execute($params_sql);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: manage_subadmins.php?status=error_not_found');
    exit;
}

// 2. Verifica Permissão
if ($role == 'admin') {
    // Admin (N1) só pode editar a si mesmo ou seus Sub-Admins (N2)
    if ($admin['id_sub_adm'] != $id_logado && $admin['parent_admin_id'] != $id_logado) {
        header('Location: manage_subadmins.php?status=error_permission');
        exit;
    }
}
// Super-Admin (Dono) pode editar qualquer um

$username_value = (isset($admin['username'])) ? htmlspecialchars($admin['username']) : '';

// Apenas o Super-Admin (Dono) pode ver a lista de Admins (N1) para definir o "Pai"
$admins_list = [];
if ($role == 'super_adm') {
    $stmt_admins = $pdo->prepare("SELECT id_sub_adm, nome FROM sub_administradores WHERE org_id = ? AND role = 'admin' AND id_sub_adm != ?");
    $stmt_admins->execute([$org_id, $id_sub_adm]);
    $admins_list = $stmt_admins->fetchAll();
}

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
                        
                        <div class="mb-3">
                            <label for="percentual_comissao" class="form-label">Percentual de Comissão do Gerente (%)</label>
                            <input type="number" step="0.01" class="form-control" name="percentual_comissao" value="<?php echo $admin['percentual_comissao']; ?>" required>
                            <small class="form-text text-muted">
                                <b>Para Admin (N1):</b> Esta % é ignorada (ele sempre fica com a "sobra").<br>
                                <b>Para Sub-Admin (N2):</b> Esta é a % que ele ganha do Lucro Total (ex: 10%).
                            </small>
                        </div>
                        
                        <?php if ($role == 'super_adm'): ?>
                            <div class="mb-3"><label for="role" class="form-label">Permissão (Role)</label>
                                <select class="form-control" name="role" required>
                                    <option value="admin" <?php echo ($admin['role'] == 'admin') ? 'selected' : ''; ?>>Admin (Nível 1)</option>
                                    <option value="sub_adm" <?php echo ($admin['role'] == 'sub_adm') ? 'selected' : ''; ?>>Sub-Admin (Nível 2)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="parent_admin_id" class="form-label">Vincular a (Admin Pai)</label>
                                <select class="form-control" name="parent_admin_id">
                                    <option value="">Nenhum (Vinculado direto ao Super-Admin)</option>
                                    <?php foreach ($admins_list as $a): ?>
                                        <option value="<?php echo $a['id_sub_adm']; ?>" <?php echo ($a['id_sub_adm'] == $admin['parent_admin_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($a['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Apenas 'Sub-Admins' devem ser vinculados a um 'Admin'.</small>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($admin['role']); ?>">
                            <input type="hidden" name="parent_admin_id" value="<?php echo htmlspecialchars($admin['parent_admin_id']); ?>">
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
                        <a href="manage_subadmins.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('templates/footer.php'); ?>