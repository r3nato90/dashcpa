<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role']; // Role de quem está LOGADO
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}
$id_usuario = (int)$_GET['id'];

// 1. Busca o usuário para garantir que ele exista na organização
$stmt_user_check = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ? AND org_id = ?");
$stmt_user_check->execute([$id_usuario, $org_id]);
$user = $stmt_user_check->fetch();

if (!$user) {
    header('Location: manage_users.php?status=error_not_found');
    exit;
}

// 2. Verifica a permissão de hierarquia
if ($role == 'admin') {
    // Admin (N1) só pode editar usuários (N3) que pertencem aos seus Sub-Admins (N2) OU a ele mesmo
    $parent_admin_id = null;
    if ($user['id_sub_adm']) {
        $stmt_check_permission = $pdo->prepare("
            SELECT parent_admin_id 
            FROM sub_administradores 
            WHERE id_sub_adm = ? AND org_id = ?
        ");
        $stmt_check_permission->execute([$user['id_sub_adm'], $org_id]);
        $parent_admin_id = $stmt_check_permission->fetchColumn();
    }

    if ($parent_admin_id != $id_logado && $user['id_sub_adm'] != $id_logado) {
        header('Location: manage_users.php?status=error_permission_admin');
        exit;
    }
    
} elseif ($role == 'sub_adm') {
    // Sub-Adm (N2) só pode editar seus próprios usuários (N3)
    if ($user['id_sub_adm'] != $id_logado) {
        header('Location: manage_users.php?status=error_permission_subadm');
        exit;
    }
}
// Super-Admin (Dono) pode editar qualquer um

// 3. Busca a lista de Gerentes para o dropdown (se necessário)
$admins_list = [];
if ($role == 'super_adm' || $role == 'admin') {
    
    $query_admins = "SELECT id_sub_adm, nome, role FROM sub_administradores WHERE org_id = ? AND role IN ('admin', 'sub_adm')";
    $params_admins = [$org_id];

    if ($role == 'admin') {
        // Admin (N1) só pode vincular usuários a ele mesmo (N1) ou aos seus Sub-Admins (N2)
        $query_admins .= " AND (parent_admin_id = ? OR id_sub_adm = ?)";
        $params_admins[] = $id_logado;
        $params_admins[] = $id_logado;
    }
    
    $query_admins .= " ORDER BY nome";
    $stmt_admins = $pdo->prepare($query_admins);
    $stmt_admins->execute($params_admins);
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
                        
                        <div class="mb-3">
                            <label for="percentual_comissao" class="form-label">Percentual de Comissão do Operador (N3) (%)</label>
                            <input type="number" step="0.01" class="form-control" name="percentual_comissao" 
                                   value="<?php echo $user['percentual_comissao']; ?>" 
                                   <?php if ($role == 'sub_adm') echo 'readonly'; // TRAVADO (Apenas N2) ?>
                                   required>
                            <?php if ($role == 'sub_adm'): ?>
                                <small class="form-text text-danger">Apenas Admins (N1) ou o Super-Admin (Dono) podem alterar comissões.</small>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($role == 'super_adm' || $role == 'admin'): ?>
                        <div class="mb-3">
                            <label for="id_sub_adm" class="form-label">Vincular a 
                                <?php echo ($role == 'super_adm') ? '(Gerente N1/N2)' : '(Sub-Gerente N2 ou Você)'; ?>
                            </label>
                            <select class="form-control" name="id_sub_adm">
                                <option value="">Nenhum (Sem vínculo)</option>
                                <?php foreach ($admins_list as $admin): ?>
                                    <option value="<?php echo $admin['id_sub_adm']; ?>" <?php echo ($admin['id_sub_adm'] == $user['id_sub_adm']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($admin['nome']) . " (" . $admin['role'] . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="id_sub_adm" value="<?php echo $user['id_sub_adm']; ?>">
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