<?php
// O session_start() deve estar no topo de cada página ANTES de incluir este header.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Garante que a conexão $pdo exista
if (!isset($pdo)) {
    include_once(__DIR__ . '/../config/db.php');
}

$brand_name = "DashCPA";
$user_nome = $_SESSION['nome'] ?? 'Visitante';
$user_role = $_SESSION['role'] ?? 'Visitante';

// Define o nome da marca com base no usuário (exemplo simples, já que não há orgs)
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        $brand_name = "DashCPA (Admin)";
    } elseif ($_SESSION['role'] == 'sub_adm') {
         $brand_name = "DashCPA (Gerente)";
    } else {
         $brand_name = "DashCPA (Operador)";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-layout-mode="detached" data-sidenav-size="full" data-bs-theme="light">
<head>
    <title><?php echo htmlspecialchars($brand_name); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS do Template Attex (Presume que a pasta /assets/ existe) -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/vendor/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    
    <!-- CSS do Tema (Bootstrap e Ícones) -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <!-- Theme Config Js -->
    <script src="assets/js/config.js"></script>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        <div class="navbar-custom">
            <div class="topbar container-fluid">
                <div class="d-flex align-items-center gap-lg-2 gap-1">
                    
                    <!-- Logo (Mobile) -->
                    <div class="logo-topbar">
                        <a href="index.php" class="logo-light">
                            <span class="logo-lg"><img src="assets/images/logo.png" alt="logo" height="22"></span>
                            <span class="logo-sm"><img src="assets/images/logo-sm.png" alt="small logo" height="22"></span>
                        </a>
                        <a href="index.php" class="logo-dark">
                            <span class="logo-lg"><img src="assets/images/logo-dark.png" alt="dark logo" height="22"></span>
                            <span class="logo-sm"><img src="assets/images/logo-sm.png" alt="small logo" height="22"></span>
                        </a>
                    </div>

                    <!-- Botão de Toggle (Menu) -->
                    <button class="button-toggle-menu">
                        <i class="ri-menu-2-fill"></i>
                    </button>
                </div>

                <!-- Itens da Direita (Perfil, Sair) -->
                <ul class="topbar-menu d-flex align-items-center gap-3">
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="account-user-avatar">
                                <img src="assets/images/users/avatar-1.jpg" alt="user-image" width="32" class="rounded-circle">
                            </span>
                            <span class="d-lg-flex flex-column gap-1 d-none">
                                <h5 class="my-0"><?php echo htmlspecialchars($user_nome); ?></h5>
                                <h6 class="my-0 fw-normal"><?php echo htmlspecialchars($user_role); ?></h6>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                            <div class=" dropdown-header noti-title"><h6 class="text-overflow m-0">Bem-vindo(a)!</h6></div>
                            <a href="logout.php" class="dropdown-item">
                                <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                                <span>Sair</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- ========== Topbar End ========== -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="leftside-menu">
            <!-- Brand Logo (Desktop) -->
            <a href="index.php" class="logo logo-dark">
                <span class="logo-lg">
                    <img src="assets/images/logo-dark.png" alt="dark logo" height="22" />
                </span>
                <span class="logo-sm">
                    <img src="assets/images/logo-sm.png" alt="small logo" height="22" />
                </span>
            </a>

            <!-- Sidebar -left -->
            <div class="h-100" id="leftside-menu-container" data-simplebar>
                <!--- Sidemenu -->
                <ul class="side-nav">
                    
                    <li class="side-nav-title">Navegação</li>

                    <?php if (isset($_SESSION['role'])): ?>
                        
                        <!-- MENU DO USUÁRIO (OPERADOR) -->
                        <?php if ($_SESSION['role'] == 'usuario'): ?>
                            <li class="side-nav-item">
                                <a href="dashboard_usuario.php" class="side-nav-link active">
                                    <i class="ri-dashboard-3-line"></i> <span> Meu Painel </span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- MENU DO GERENTE (Admin / Sub-Admin) -->
                        <?php if (in_array($_SESSION['role'], ['admin', 'sub_adm'])): ?>
                            <li class="side-nav-item">
                                <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'dashboard_admin.php' : 'dashboard_subadmin.php'; ?>" class="side-nav-link active">
                                    <i class="ri-dashboard-3-line"></i> <span> Visão Geral </span>
                                </a>
                            </li>
                             <li class="side-nav-item">
                                <a href="reports.php" class="side-nav-link">
                                    <i class="ri-bar-chart-box-line"></i> <span> Relatórios </span>
                                </a>
                            </li>
                             <li class="side-nav-item">
                                <a href="manage_users.php" class="side-nav-link">
                                    <i class="ri-group-line"></i> <span> Gerenciar Operadores </span>
                                </a>
                            </li>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li class="side-nav-item">
                                <a href="manage_subadmins.php" class="side-nav-link">
                                    <i class="ri-user-shield-line"></i> <span> Gerenciar Gerentes </span>
                                </a>
                            </li>
                            <?php endif; ?>
                            <li class="side-nav-item">
                                <a href="create_user.php" class="side-nav-link">
                                    <i class="ri-user-add-line"></i> <span> Criar Conta </span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <!--- End Sidemenu -->
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- ========== Left Sidebar End ========== -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">