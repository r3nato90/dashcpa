<?php
$page_title = "Criar Novo Usuário/Admin";
include('config/db.php');
include('config/logger.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário logado tem permissão para acessar esta página
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: index.php');
    exit;
}

// Redireciona para o formulário apropriado
if ($_SESSION['role'] === 'super_adm') {
    // Super Admin pode criar Admin ou Usuário Comissionado
    $options = [
        'admin' => ['url' => 'register_admin.php', 'label' => 'Novo Administrador (Admin)'],
        'sub_adm' => ['url' => 'register_subadmin.php', 'label' => 'Novo Sub Administrador (Sub-Admin)'],
        'usuario' => ['url' => 'register_user.php', 'label' => 'Novo Usuário Comissionado'],
    ];
} elseif ($_SESSION['role'] === 'admin') {
     // Admin pode criar Sub Admin ou Usuário Comissionado
     $options = [
        'sub_adm' => ['url' => 'register_subadmin.php', 'label' => 'Novo Sub Administrador (Sub-Admin)'],
        'usuario' => ['url' => 'register_user.php', 'label' => 'Novo Usuário Comissionado'],
    ];
} elseif ($_SESSION['role'] === 'sub_adm') {
    // Sub Admin pode criar apenas Usuário Comissionado
     $options = [
        'usuario' => ['url' => 'register_user.php', 'label' => 'Novo Usuário Comissionado'],
    ];
} else {
    // Outros papéis não devem acessar esta página
    header('Location: index.php');
    exit;
}

// Se houver apenas uma opção, redireciona diretamente
if (count($options) === 1) {
    header('Location: ' . reset($options)['url']);
    exit;
}

include('header.php'); 
?>

<h2 class="mb-4">Criar Novo Usuário/Admin</h2>
<p class="text-muted">Selecione o tipo de conta que deseja criar:</p>

<div class="row">
    <?php foreach ($options as $role_key => $option): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm text-center">
            <div class="card-body d-flex flex-column justify-content-between">
                <i class="fas fa-<?php 
                    if ($role_key === 'super_adm') echo 'crown'; 
                    elseif ($role_key === 'admin') echo 'user-shield'; 
                    elseif ($role_key === 'sub_adm') echo 'users-cog';
                    else echo 'user'; 
                ?> fa-3x mb-3 text-primary"></i>
                <h5 class="card-title"><?php echo $option['label']; ?></h5>
                <p class="card-text text-muted">Cria uma nova conta com o papel de **<?php echo $role_key; ?>** para gerenciar a plataforma ou registrar operações.</p>
                <a href="<?php echo $option['url']; ?>" class="btn btn-primary mt-auto">
                    <i class="fas fa-plus me-1"></i> Criar <?php echo $role_key; ?>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mt-4">
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Voltar ao Dashboard
    </a>
</div>

<?php 
include('footer.php');
?>