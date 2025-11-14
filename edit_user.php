<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Editar Operador";
$breadcrumb_active = "Editar Operador";

// Verificação de segurança: Apenas Super Admin, Admin e Sub-Admin podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];
$id_usuario_edit = $_GET['id'] ?? null;
$message = "";

if (!$id_usuario_edit) {
    header('Location: manage_users.php');
    exit;
}

// 1. Buscar dados do usuário a ser editado
$stmt_user = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND role = 'usuario'");
$stmt_user->execute([$id_usuario_edit]);
$usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $message = "<div class='alert alert-danger'>Operador não encontrado.</div>";
    include('header.php'); 
    echo "<div class='container mt-5'>{$message} <p><a href='manage_users.php'>Voltar ao Gerenciamento de Operadores</a></p></div>";
    include('footer.php');
    exit;
}

// 2. Verificação de permissão: O usuário logado deve ser o manager do operador (ou Super Admin)
if ($role_logado != 'super_adm' && $usuario['manager_id'] != $id_logado) {
    log_acao("Acesso não autorizado: Tentativa de editar usuário ID " . $id_usuario_edit . " pelo usuário ID " . $id_logado);
    $message = "<div class='alert alert-danger'>Acesso negado. Você só pode editar operadores sob sua gerência.</div>";
    include('header.php'); 
    echo "<div class='container mt-5'>{$message} <p><a href='manage_users.php'>Voltar ao Gerenciamento de Operadores</a></p></div>";
    include('footer.php');
    exit;
}


// 3. Obter lista de Managers (para Super Admin, Admin e Sub-Admin)
$managers = [];
$manager_id_atual = $usuario['manager_id'];

if ($role_logado === 'super_adm') {
    // Super Adm pode atribuir a qualquer Admin ou Sub-Admin
    $stmt_managers = $pdo->query("SELECT id, nome, role FROM sub_administradores WHERE role IN ('admin', 'sub_adm') ORDER BY nome");
    $managers = $stmt_managers->fetchAll();
} elseif ($role_logado === 'admin' || $role_logado === 'sub_adm') {
    // Admin/Sub-Admin só podem atribuir a si mesmos (ou manter o atual se for o caso)
    $managers[] = ['id' => $id_logado, 'nome' => $_SESSION['username'], 'role' => $role_logado];
    // Se o manager atual for diferente (o que só deveria acontecer se o super admin definiu), mantemos a opção atual também
    if ($manager_id_atual != $id_logado) {
        $stmt_current_manager = $pdo->prepare("SELECT id, nome, role FROM sub_administradores WHERE id = ?");
        $stmt_current_manager->execute([$manager_id_atual]);
        $current_manager = $stmt_current_manager->fetch(PDO::FETCH_ASSOC);
        if ($current_manager) {
             // Adiciona o manager atual para que ele não seja perdido
            if (!in_array($current_manager, $managers)) {
                $managers[] = $current_manager;
            }
        }
    }
}

include('header.php'); 
?>

<h2 class="mb-4">Editar Operador: <?php echo htmlspecialchars($usuario['nome']); ?></h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="process_edit_user.php">
            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario_edit); ?>">

            <!-- Campo Nome -->
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>
            
            <!-- Campo Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>
            
            <!-- Campo Comissão -->
            <div class="mb-3">
                <label for="percentual_comissao" class="form-label">Comissão (%)</label>
                <input type="number" step="0.01" class="form-control" id="percentual_comissao" name="percentual_comissao" 
                       value="<?php echo htmlspecialchars($usuario['percentual_comissao']); ?>" required min="0" max="100">
            </div>

            <!-- Campo Manager (Gerente) - Visível apenas se houver opções -->
            <?php if (!empty($managers)): ?>
            <div class="mb-3">
                <label for="manager_id" class="form-label">Administrador Gerente</label>
                <select class="form-select" id="manager_id" name="manager_id" required>
                    <option value="">Selecione um Administrador</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?php echo $manager['id']; ?>" 
                                <?php echo ($manager_id_atual == $manager['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($manager['nome'] . ' (' . $manager['role'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Reset Senha (Opcional) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="new_password" class="form-label">Nova Senha (Deixe em branco para não alterar)</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>
                <div class="col-md-6 mb-4">
                    <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="manage_users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
include('footer.php');
?>