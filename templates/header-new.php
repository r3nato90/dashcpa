<?php
// Arquivo de cabeçalho alternativo (header-new.php) - Layout com Topbar (Horizontal)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Inclui o arquivo de conexão e o logger, se ainda não estiverem incluídos
if (!isset($pdo)) {
    include('db.php');
}
if (!function_exists('log_acao')) {
    include('logger.php');
}

// Verifica se o usuário está logado e define o papel para o menu
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'visitante';
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Convidado';

// Define o título da página
$title = "Dashboard CPA - ";
if (isset($page_title)) {
    $title .= $page_title;
} else {
    $title .= "Página Inicial";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Incluindo Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluindo Font Awesome (Ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJg1iIN50xT5Zux0795K6t20f7NOfXf7sB9+74f00G6S/v2l7nI6zX7E7/h12g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .topbar {
            background-color: #343a40; /* Cor escura para o topo */
            color: white;
            padding: 10px 20px;
        }
        .topbar .nav-link {
            color: #adb5bd;
        }
        .topbar .nav-link:hover {
            color: white;
        }
        .content {
            padding: 20px;
            padding-top: 80px; /* Espaço para o topbar */
        }
        .footer {
            padding: 10px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
        /* Fixa o topbar no topo */
        .topbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030; /* Valor padrão de z-index para navbar */
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <nav class="topbar navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="index.php">DashCPA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Dashboard</a>
                        </li>
                        <?php if ($user_role === 'super_adm'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-users-cog me-1"></i> Gerenciamento
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li><a class="dropdown-item" href="manage_subadmins.php">Gerenciar Admins</a></li>
                                    <li><a class="dropdown-item" href="manage_users.php">Gerenciar Usuários</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-line me-1"></i> Relatórios
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                                    <li><a class="dropdown-item" href="reports.php">Visualizar Relatórios</a></li>
                                    <li><a class="dropdown-item" href="saved_reports.php">Relatórios Salvos</a></li>
                                </ul>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'view_logs.php') ? 'active' : ''; ?>" href="view_logs.php">
                                    <i class="fas fa-history me-1"></i> Logs
                                </a>
                            </li>
                        <?php elseif ($user_role === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>" href="manage_users.php">
                                    <i class="fas fa-users me-1"></i> Gerenciar Usuários
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-line me-1"></i> Relatórios
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                                    <li><a class="dropdown-item" href="reports.php">Visualizar Relatórios</a></li>
                                    <li><a class="dropdown-item" href="saved_reports.php">Relatórios Salvos</a></li>
                                </ul>
                            </li>
                        <?php elseif ($user_role === 'sub_adm'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-chart-line me-1"></i> Relatórios
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                                    <li><a class="dropdown-item" href="reports.php">Visualizar Relatórios</a></li>
                                    <li><a class="dropdown-item" href="saved_reports.php">Relatórios Salvos</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $username; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                <li><span class="dropdown-item-text">Logado como: **<?php echo strtoupper(str_replace('_', ' ', $user_role)); ?>**</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Sair</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>" href="login.php">Entrar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>" href="register.php">Registrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid content">
        <header class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h3 mb-0"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                    <?php if (isset($breadcrumb_active)): ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $breadcrumb_active; ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </header>

        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <main>