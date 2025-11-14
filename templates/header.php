<?php
// Arquivo de cabeçalho (header.php)
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
        .sidebar {
            height: 100vh;
            background-color: #343a40; /* Cor escura para o sidebar */
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }
        .sidebar .active {
            background-color: #007bff; /* Cor primária para o item ativo */
            color: white;
        }
        .content {
            padding: 20px;
        }
        .footer {
            padding: 10px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        /* Estilo para links que não cabem no menu */
        .hidden-link { display: none !important; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-3">
            <h2 class="text-white mb-4">DashCPA</h2>
            <ul class="nav nav-pills flex-column mb-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <?php if ($user_role === 'super_adm'): ?>
                        <li class="nav-item">
                            <a href="manage_subadmins.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_subadmins.php') ? 'active' : ''; ?>">
                                <i class="fas fa-user-shield me-2"></i> Gerenciar Admins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage_users.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>">
                                <i class="fas fa-users me-2"></i> Gerenciar Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reports.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                                <i class="fas fa-chart-line me-2"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="saved_reports.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'saved_reports.php') ? 'active' : ''; ?>">
                                <i class="fas fa-save me-2"></i> Relatórios Salvos
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="view_logs.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'view_logs.php') ? 'active' : ''; ?>">
                                <i class="fas fa-history me-2"></i> Logs do Sistema
                            </a>
                        </li>
                    <?php elseif ($user_role === 'admin'): ?>
                        <li class="nav-item">
                            <a href="manage_users.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>">
                                <i class="fas fa-users me-2"></i> Gerenciar Usuários
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="reports.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                                <i class="fas fa-chart-line me-2"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="saved_reports.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'saved_reports.php') ? 'active' : ''; ?>">
                                <i class="fas fa-save me-2"></i> Relatórios Salvos
                            </a>
                        </li>
                    <?php elseif ($user_role === 'sub_adm'): ?>
                         <li class="nav-item">
                            <a href="reports.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                                <i class="fas fa-chart-line me-2"></i> Relatórios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="saved_reports.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'saved_reports.php') ? 'active' : ''; ?>">
                                <i class="fas fa-save me-2"></i> Relatórios Salvos
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Sair
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">
                            <i class="fas fa-sign-in-alt me-2"></i> Entrar
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="register.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus me-2"></i> Registrar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="mt-auto pt-3 border-top">
                <small class="text-muted">Usuário: <?php echo $username; ?> (<?php echo strtoupper(str_replace('_', ' ', $user_role)); ?>)</small>
            </div>
        </div>
        <!-- Conteúdo Principal -->
        <div class="content flex-grow-1">
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