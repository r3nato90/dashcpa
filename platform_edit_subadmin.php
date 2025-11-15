<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

// **** CORREÇÃO: A verificação de segurança DEVE ser 'platform_owner' ****
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// 1. Validar e buscar o admin (SEM filtro de org_id)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: platform_manage_orgs.php');
    exit;
}
$id_sub_adm = (int)$_GET['id'];
$stmt_admin = $pdo->prepare("SELECT * FROM sub_administradores WHERE id_sub_adm = ? AND role != 'platform_owner'");
$stmt_admin->execute([$id_sub_adm]);
$admin = $stmt_admin->fetch();

if (!$admin) {
    header('Location: platform_manage_orgs.php?status=error_not_found');
    exit;
}

// 2. Buscar todas as organizações para o dropdown de "Mover"
$stmt_orgs = $pdo->query("SELECT org_id, org_name FROM organizations ORDER BY org_name");
$organizations = $stmt_orgs->fetchAll();

include('templates/header-new.php'); // Usa o novo header
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h2 class="h3 mb-4">Edição Global de Gerente</h2>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Editando: <?php echo htmlspecialchars($admin['nome']); ?></h5>
                </div>
                <div class="card-body">
                    <form action="platform_process_edit_subadmin.php" method="POST">
                        <input type="hidden" name="id_sub_adm" value="<?php echo $admin['id_sub_adm']; ?>">

                        <div class="mb-3"><label for="nome" class="form-label">Nome</label><input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($admin['nome']); ?>" required></div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required></div>
                        <div class="mb-3"><label for="username" class="form-label">Username</label><input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required></div>
                        <div class="mb-3"><label for="senha" class="form-label">Nova Senha (Deixe em branco)</label><input type="password" class="form-control" name="senha"></div>
                        <div class="mb-3"><label for="percentual_comissao" class="form-label">Percentual Comissão (%)</label><input type="number" step="0.01" class="form-control" name="percentual_comissao" value="<?php echo $admin['percentual_comissao']; ?>"></div>

                        <hr class="my-4">
                        <h5 class="mb-3">Vinculação e Permissões (Avançado)</h5>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Permissão (Role)</label>
                            <select class="form-control" name="role" required>
                                <option value="super_adm" <?php echo ($admin['role'] == 'super_adm') ? 'selected' : ''; ?>>Super Admin (Dono da Empresa)</option>
                                <option value="admin" <?php echo ($admin['role'] == 'admin') ? 'selected' : ''; ?>>Admin (Gerente N1)</option>
                                <option value="sub_adm" <?php echo ($admin['role'] == 'sub_adm') ? 'selected' : ''; ?>>Sub-Admin (Gerente N2)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="org_id" class="form-label">Mover para Organização (Empresa)</label>
                            <select class="form-control" name="org_id" required>
                                <?php foreach ($organizations as $org): ?>
                                    <option value="<?php echo $org['org_id']; ?>" <?php echo ($admin['org_id'] == $org['org_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($org['org_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>Atenção:</strong> Mover um gerente desvincula automaticamente os usuários dele.
                        </div>

                        <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
                        <a href="platform_manage_orgs.php" class="btn btn-secondary w-100 mt-2">Cancelar (Voltar)</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Usa o novo footer
include('templates/footer-new.php'); 
?>