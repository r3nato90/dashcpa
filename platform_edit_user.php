<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// 1. Validar e buscar o usuário
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: platform_manage_users.php');
    exit;
}
$id_usuario = (int)$_GET['id'];
$stmt_user = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt_user->execute([$id_usuario]);
$user = $stmt_user->fetch();

if (!$user) {
    header('Location: platform_manage_users.php?status=error_not_found');
    exit;
}

// 2. Buscar todas as organizações
$stmt_orgs = $pdo->query("SELECT org_id, org_name FROM organizations ORDER BY org_name");
$organizations = $stmt_orgs->fetchAll();

// **** NOVO: Buscar TODOS os Sub-Admins (N2) e Admins (N1) da plataforma ****
$stmt_gerentes = $pdo->query("
    SELECT s.id_sub_adm, s.nome, s.role, o.org_name 
    FROM sub_administradores s
    JOIN organizations o ON s.org_id = o.org_id
    WHERE s.role IN ('admin', 'sub_adm')
    ORDER BY o.org_name, s.nome
");
$gerentes_list = $stmt_gerentes->fetchAll();

include('templates/header-new.php'); 
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

                        <div class="mb-3"><label for="nome" class="form-label">Nome</label><input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required></div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
                        <div class="mb-3"><label for="senha" class="form-label">Nova Senha (Deixe em branco)</label><input type="password" class="form-control" name="senha"></div>
                        <div class="mb-3"><label for="percentual_comissao" class="form-label">Percentual Comissão (%)</label><input type="number" step="0.01" class="form-control" name="percentual_comissao" value="<?php echo $user['percentual_comissao']; ?>"></div>

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

                        <div class="mb-3">
                            <label for="id_sub_adm" class="form-label">Vincular ao Gerente (Nível 2)</label>
                            <select class="form-control" name="id_sub_adm">
                                <option value="">Nenhum (Desvinculado)</option>
                                <?php foreach ($gerentes_list as $gerente): ?>
                                    <option value="<?php echo $gerente['id_sub_adm']; ?>" <?php echo ($user['id_sub_adm'] == $gerente['id_sub_adm']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($gerente['nome']); ?> (<?php echo htmlspecialchars($gerente['role']); ?>) - [<?php echo htmlspecialchars($gerente['org_name']); ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
                        <a href="platform_manage_users.php" class="btn btn-secondary w-100 mt-2">Cancelar (Voltar)</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include('templates/footer-new.php'); 
?>