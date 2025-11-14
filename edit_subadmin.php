<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

$page_title = "Editar Administrador";
$breadcrumb_active = "Editar Admin";

// Verificação de segurança: Apenas Super Admin e Admin podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];
$id_admin_edit = $_GET['id'] ?? null;
$message = "";

if (!$id_admin_edit) {
    header('Location: manage_subadmins.php');
    exit;
}

// 1. Buscar dados do administrador a ser editado
$stmt_admin = $pdo->prepare("SELECT * FROM sub_administradores WHERE id = ?");
$stmt_admin->execute([$id_admin_edit]);
$gerente = $stmt_admin->fetch(PDO::FETCH_ASSOC);

if (!$gerente) {
    $message = "<div class='alert alert-danger'>Administrador/Sub-Admin não encontrado.</div>";
    include('header.php'); 
    echo "<div class='container mt-5'>{$message} <p><a href='manage_subadmins.php'>Voltar ao Gerenciamento</a></p></div>";
    include('footer.php');
    exit;
}

// 2. Verificação de permissão
$target_role = $gerente['role'];

// Regras de edição:
// - Super Admin pode editar qualquer um (exceto a si mesmo, se for o único Super Admin)
// - Admin pode editar apenas Sub-Admins sob sua gerência
if ($role_logado == 'admin' && ($target_role == 'super_adm' || $gerente['manager_id'] != $id_logado)) {
    log_acao("Acesso não autorizado: Admin ID " . $id_logado . " tentou editar gerente ID " . $id_admin_edit);
    $message = "<div class='alert alert-danger'>Acesso negado. Você não tem permissão para editar este usuário.</div>";
    include('header.php'); 
    echo "<div class='container mt-5'>{$message} <p><a href='manage_subadmins.php'>Voltar ao Gerenciamento</a></p></div>";
    exit;
}

// 3. Obter lista de Managers para reatribuição (se a função do gerente for sub_adm)
$managers = [];
if ($target_role == 'sub_adm') {
    if ($role_logado === 'super_adm') {
        // Super Adm pode reatribuir para qualquer Admin ou Super Admin
        $stmt_managers = $pdo->query("SELECT id, nome, role FROM sub_administradores WHERE role IN ('admin', 'super_adm') ORDER BY nome");
    } else {
        // Admin só pode reatribuir para si mesmo
        $stmt_managers = $pdo->prepare("SELECT id, nome, role FROM sub_administradores WHERE id = ?");
        $stmt_managers->execute([$id_logado]);
    }
    $managers = $stmt_managers->fetchAll(PDO::FETCH_ASSOC);
}


include('header.php'); 
?>

<h2 class="mb-4">Editar Gerente: <?php echo htmlspecialchars($gerente['nome']); ?> 
    <span class="badge bg-<?php echo ($target_role == 'admin' ? 'warning' : 'info'); ?> text-dark">
        <?php echo strtoupper(str_replace('_', ' ', $target_role)); ?>
    </span>
</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <?php echo $message; // Exibe feedback de status ?>

        <form method="POST" action="process_edit_subadmin.php">
            <input type="hidden" name="id_admin" value="<?php echo htmlspecialchars($id_admin_edit); ?>">
            <input type="hidden" name="current_role" value="<?php echo htmlspecialchars($target_role); ?>">

            <!-- Campo Nome -->
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($gerente['nome']); ?>" required>
            </div>
            
            <!-- Campo Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($gerente['email']); ?>" required>
            </div>
            
            <!-- Campo Comissão -->
            <div class="mb-3">
                <label for="comissao" class="form-label">Comissão Padrão (%)</label>
                <input type="number" step="0.01" class="form-control" id="comissao" name="comissao" 
                       value="<?php echo number_format($gerente['comissao'], 2, '.', ''); ?>" required min="0" max="100">
            </div>

            <!-- Campo Manager (Gerente Superior) - Apenas para Sub-Admins e Super Admin editando -->
            <?php if ($target_role == 'sub_adm' && $role_logado == 'super_adm' && !empty($managers)): ?>
            <div class="mb-3">
                <label for="manager_id" class="form-label">Administrador Gerente</label>
                <select class="form-select" id="manager_id" name="manager_id" required>
                    <option value="">Selecione um Administrador</option>
                    <?php foreach ($managers as $manager): ?>
                        <option value="<?php echo $manager['id']; ?>" 
                                <?php echo ($gerente['manager_id'] == $manager['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($manager['nome'] . ' (' . $manager['role'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php elseif ($target_role == 'sub_adm' && $role_logado == 'admin' && !empty($managers)): ?>
                <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($managers[0]['id']); ?>">
                 <div class="alert alert-info">
                     Este Sub-Admin está sob sua gerência (<?php echo htmlspecialchars($managers[0]['nome']); ?>).
                 </div>
            <?php else: ?>
                 <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($gerente['manager_id']); ?>">
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
                <a href="manage_subadmins.php" class="btn btn-secondary">
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