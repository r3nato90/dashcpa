<?php
// O session_start() deve estar no topo de cada página ANTES de incluir este header.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="/favicon/favicon.jpeg" type="image/jpeg">

    <style>
        :root {
            --primary-purple: #754FFE; /* Cor principal da imagem */
            --sidebar-bg: #ffffff;
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --body-bg: #f4f7fa;
            --card-shadow: 0 0 1.25rem rgba(30,34,40,.04);
            --card-border-radius: 0.5rem;
        }
        body { background-color: var(--body-bg); overflow-x: hidden; }
        
        /* --- Sidebar (Menu Lateral) --- */
        .sidebar { position: fixed; top: 0; left: 0; height: 100%; width: var(--sidebar-width); background-color: var(--sidebar-bg); border-right: 1px solid #eef2f7; padding: 1rem; transition: left 0.3s ease-in-out; z-index: 1030; display: flex; flex-direction: column; box-shadow: 0 0 1.25rem rgba(30,34,40,.04); }
        .sidebar-brand { font-size: 1.5rem; font-weight: 700; color: #000; text-decoration: none; padding: 0.5rem 0; margin-bottom: 1.5rem; text-align: center; }
        .sidebar-nav { list-style: none; padding-left: 0; flex-grow: 1; overflow-y: auto; }
        .sidebar-nav .nav-item { margin-bottom: 0.25rem; }
        .sidebar-nav .nav-link { color: #5a5f7d; text-decoration: none; display: block; padding: 0.75rem 1rem; border-radius: 0.375rem; transition: all 0.2s; font-weight: 500; }
        .sidebar-nav .nav-link:hover { color: var(--primary-purple); }
        .sidebar-nav .nav-link.active, .sidebar-nav .nav-item:hover .nav-link { background-color: var(--primary-purple); color: #fff; box-shadow: 0 4px 8px -4px var(--primary-purple); }
        .sidebar-nav .nav-link i { margin-right: 0.75rem; width: 20px; text-align: center; }

        /* --- Topbar (Menu Superior) --- */
        .topbar { position: fixed; top: 0; left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); height: var(--topbar-height); background-color: #fff; border-bottom: 1px solid #eef2f7; padding: 0 2rem; z-index: 1020; display: flex; justify-content: space-between; align-items: center; transition: left 0.3s ease-in-out, width 0.3s ease-in-out; }
        #sidebarToggle { background: none; border: none; font-size: 1.5rem; color: #5a5f7d; display: none; }
        .topbar-nav { list-style: none; padding-left: 0; margin-bottom: 0; display: flex; align-items: center; }
        .topbar-nav .nav-item { margin-left: 1.5rem; }
        .topbar-nav .nav-link { color: #5a5f7d; }

        /* --- Conteúdo Principal --- */
        .main-content { margin-left: var(--sidebar-width); margin-top: var(--topbar-height); padding: 2rem; transition: margin-left 0.3s ease-in-out; }
        .footer-main { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease-in-out; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1029; }
        
        /* --- Estilos dos Cards (da imagem) --- */
        .card { box-shadow: var(--card-shadow); border: none; border-radius: var(--card-border-radius); margin-bottom: 1.5rem; }
        .kpi-card .card-body { display: flex; align-items: center; }
        .kpi-card .kpi-icon { font-size: 2rem; padding: 1.25rem; border-radius: 50%; margin-right: 1rem; }
        .kpi-card .kpi-icon.bg-primary-soft { background-color: rgba(117, 79, 254, 0.1); color: var(--primary-purple); }
        .kpi-card .kpi-icon.bg-success-soft { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .kpi-card .kpi-icon.bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .kpi-card .kpi-icon.bg-info-soft { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
        
        /* --- Responsividade --- */
        @media (max-width: 992px) {
            .sidebar { left: -260px; }
            .topbar { left: 0; width: 100%; }
            .main-content, .footer-main { margin-left: 0; }
            #sidebarToggle { display: block; }
            body.sidebar-open .sidebar { left: 0; }
            body.sidebar-open .sidebar-overlay { display: block; }
        }
    </style>
</head>
<body>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="sidebar">
        <a class="sidebar-brand" href="index.php">Acnoo Admin</a>
        
        <ul class="sidebar-nav">
            <?php if (isset($_SESSION['role'])): ?>
                
                <?php if ($_SESSION['role'] == 'usuario'): ?>
                    <li class="nav-item"> <a class="nav-link active" href="dashboard_usuario.php"><i class="fas fa-home"></i> Meu Painel</a> </li>
                <?php endif; ?>

                <?php if (in_array($_SESSION['role'], ['admin', 'sub_adm'])): ?>
                    <li class="nav-item"> <a class="nav-link" href="create_user.php"><i class="fas fa-user-plus"></i> Criar Conta</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="reports.php"><i class="fas fa-chart-line"></i> Novo Relatório</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="saved_reports.php"><i class="fas fa-save"></i> Relatórios Salvos</a> </li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item"> <a class="nav-link active" href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Painel Admin</a> </li>
                    <?php else: ?>
                        <li class="nav-item"> <a class="nav-link active" href="dashboard_subadmin.php"><i class="fas fa-tachometer-alt"></i> Painel Sub-Adm</a> </li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($_SESSION['role'] == 'super_adm'): ?>
                    <li class="nav-item"> <a class="nav-link active" href="dashboard_superadmin.php"><i class="fas fa-crown"></i> Painel Super Admin</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="create_user.php"><i class="fas fa-user-plus"></i> Criar Conta</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Gerenciar Admins</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="reports.php"><i class="fas fa-chart-line"></i> Novo Relatório</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="saved_reports.php"><i class="fas fa-save"></i> Relatórios Salvos</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="view_logs.php"><i class="fas fa-clipboard-list"></i> Ver Logs Diários</a> </li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] == 'platform_owner'): ?>
                    <li class="nav-item"> <a class="nav-link active" href="platform_owner.php"><i class="fas fa-server"></i> Painel do Dono</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_manage_orgs.php"><i class="fas fa-building"></i> Gerenciar Clientes</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_manage_users.php"><i class="fas fa-users"></i> Usuários Globais</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_manage_plans.php"><i class="fas fa-gem"></i> Gerenciar Planos</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_logs.php"><i class="fas fa-globe"></i> Logs Globais</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_settings.php"><i class="fas fa-cogs"></i> Configurações (Pagto)</a> </li>
                <?php endif; ?>

                <hr style="color: #eef2f7;">
                <li class="nav-item"> <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a> </li>

            <?php else: ?>
                <li class="nav-item"> <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a> </li>
                <li class="nav-item"> <a class="nav-link" href="pricing.php"><i class="fas fa-building"></i> Criar Conta (Empresa)</a> </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="main-content-wrapper">
        <nav class="navbar topbar">
            <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <ul class="topbar-nav">
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])): ?>
                <li class="nav-item">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarRelatorio">
                        <i class="fas fa-plus-circle me-1"></i> Enviar Relatório
                    </button>
                </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div class="main-content">