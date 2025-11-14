<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo');

// Apenas 'super_adm' pode editar
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm') {
    header('Location: login.php');
    exit;
}

// Verificar se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_subadmins.php');
    exit;
}

$id_sub_adm = (int)$_GET['id'];

// Buscar dados do sub-admin
$stmt = $pdo->prepare("SELECT * FROM sub_administradores WHERE id_sub_adm = ?");
$stmt->execute([$id_sub_adm]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: manage_subadmins.php');
    exit;
}

// *** CORREÇÃO: Define um valor padrão para 'username' caso a coluna ainda não exista ou esteja NULL ***
$username_value = (isset($admin['username'])) ? htmlspecialchars($admin['username']) : '';

include('templates/header.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4>Editar: <?php echo htmlspecialchars($admin['nome']); ?></h4>
                </div>
                <div class="card-body">
                    <form action="process_edit_subadmin.php" method="POST">
                        <input type="hidden" name="id_sub_adm" value="<?php echo $admin['id_sub_adm']; ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($admin['nome']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Usuário (para link)</label>
                            <input type="text" class="form-control" name="username" value="<?php echo $username_value; ?>" required>
                        </div>

                         <div class="mb-3">
                            <label for="senha" class="form-label">Nova Senha (Deixe em branco para não alterar)</label>
                            <input type="password" class="form-control" name="senha">
                        </div>
                        <div class="mb-3">
                            <label for="percentual_comissao" class="form-label">Percentual de Comissão (%)</label>
                            <input type="number" step="0.01" class="form-control" name="percentual_comissao" value="<?php echo $admin['percentual_comissao']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Permissão (Role)</label>
                            <select class="form-control" name="role" required>
                                <?php if ($admin['id_sub_adm'] == 1): ?>
                                    <option value="super_adm" selected>super_adm (Master)</option>
                                <?php else: ?>
                                    <option value="sub_adm" <?php echo ($admin['role'] == 'sub_adm') ? 'selected' : ''; ?>>sub_adm (Gerente)</option>
                                    <option value="admin" <?php echo ($admin['role'] == 'admin') ? 'selected' : ''; ?>>admin (Gerente)</option>
                                    <option value="super_adm" <?php echo ($admin['role'] == 'super_adm') ? 'selected' : ''; ?>>super_adm (Controle Total)</option>
                                <?php endif; ?>
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