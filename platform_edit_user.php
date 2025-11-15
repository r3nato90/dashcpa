<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// 1. Validar e buscar o usuário (SEM filtro de org_id)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: platform_manage_users.php'); // Link corrigido
    exit;
}
$id_usuario = (int)$_GET['id'];
$stmt_user = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt_user->execute([$id_usuario]);
$user = $stmt_user->fetch();

if (!$user) {
    header('Location: platform_manage_users.php?status=error_not_found'); // Link corrigido
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
            <h2 class="h3 mb-4">Edição Global de Usuário</h2>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Editando: <?php echo htmlspecialchars($user['nome']); ?></h5>
                </div>
                <div class="card-body">
                    <form action="platform_process_edit_user.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?php echo $user['id_usuario']; ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Nova Senha (Deixe em branco para não alterar)</label>
                            <input type="password" class="form-control" name="senha">
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Vinculação (Avançado)</h5>
                        <div class="mb-3">
                            <label for="org_id" class="form-label">Mover para Organização (Empresa)</label>
                            <select class="form-control" name="org_id" required>
                                <?php foreach ($organizations as $org): ?>
                                    <option value="<?php echo $org['org_id']; ?>" <?php echo ($user['org_id'] == $org['org_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($org['org_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>Atenção:</strong> Ao mover um usuário para uma nova organização, ele será desvinculado de seu gerente atual (Super Admin precisará re-vincular).
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