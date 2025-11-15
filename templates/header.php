<?php
// O session_start() deve estar no topo de cada página ANTES de incluir este header.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="icon" href="/favicon/favicon.jpeg" type="image/jpeg">

    <style>
        body { background-color: #f8f9fa; overflow-x: hidden; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100%; width: 260px; background-color: #212529; color: #fff; padding: 1.5rem 1rem; transition: left 0.3s ease-in-out; z-index: 1030; display: flex; flex-direction: column; }
        .sidebar-brand { font-size: 1.5rem; font-weight: 700; color: #fff; text-decoration: none; margin-bottom: 1.5rem; text-align: center; }
        .sidebar-nav { list-style: none; padding-left: 0; flex-grow: 1; overflow-y: auto; }
        .sidebar-nav .nav-item { margin-bottom: 0.5rem; }
        .sidebar-nav .nav-link { color: #adb5bd; text-decoration: none; display: block; padding: 0.75rem 1rem; border-radius: 0.375rem; transition: background-color 0.2s, color 0.2s; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background-color: #495057; color: #fff; }
        .sidebar-nav .nav-link i { margin-right: 0.75rem; width: 20px; text-align: center; }
        .sidebar-footer { margin-top: auto; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1029; }
        .mobile-header { display: none; position: fixed; top: 0; left: 0; width: 100%; background-color: #212529; color: #fff; padding: 0.5rem 1rem; z-index: 1020; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        #sidebarToggle { background: none; border: none; color: #adb5bd; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; }
        #sidebarToggle:hover { color: #fff; }
        .mobile-brand { font-size: 1.25rem; font-weight: 700; color: #fff; text-decoration: none; }
        .main-content, .footer-main { transition: margin-left 0.3s ease-in-out; }

        @media (min-width: 992.01px) {
            .sidebar { left: 0; }
            .main-content { margin-left: 260px; }
            .footer-main { margin-left: 260px; }
            .mobile-header { display: none; }
            .sidebar-overlay { display: none !important; } 
        }
        @media (max-width: 992px) {
            .sidebar { left: -260px; }
            .main-content { margin-left: 0; padding-top: 60px; }
            .footer-main { margin-left: 0; }
            .mobile-header { display: flex; justify-content: space-between; align-items: center; }
            body.sidebar-open .sidebar { left: 0; }
            body.sidebar-open .sidebar-overlay { display: block; }
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <a class="mobile-brand" href="index.php">Dashboard</a>
        <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </div>

    <div class="sidebar">
        <a class="sidebar-brand" href="index.php">Dashboard</a>
        
        <ul class="sidebar-nav">
            <?php if (isset($_SESSION['role'])): ?>
                
                <?php if ($_SESSION['role'] == 'usuario'): ?>
                    <li class="nav-item"> <a class="nav-link" href="dashboard_usuario.php"><i class="fas fa-home"></i> Meu Painel</a> </li>
                <?php endif; ?>

                <?php if (in_array($_SESSION['role'], ['admin', 'sub_adm'])): ?>
                    <li class="nav-item"> <a class="nav-link" href="create_user.php"><i class="fas fa-user-plus"></i> Criar Conta</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="reports.php"><i class="fas fa-chart-line"></i> Novo Relatório</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="saved_reports.php"><i class="fas fa-save"></i> Relatórios Salvos</a> </li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item"> <a class="nav-link" href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Painel Admin</a> </li>
                    <?php else: ?>
                        <li class="nav-item"> <a class="nav-link" href="dashboard_subadmin.php"><i class="fas fa-tachometer-alt"></i> Painel Sub-Adm</a> </li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($_SESSION['role'] == 'super_adm'): ?>
                    <li class="nav-item"> <a class="nav-link" href="dashboard_superadmin.php"><i class="fas fa-crown"></i> Painel Super Admin</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="create_user.php"><i class="fas fa-user-plus"></i> Criar Conta</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="manage_users.php"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="manage_subadmins.php"><i class="fas fa-user-shield"></i> Gerenciar Admins</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="reports.php"><i class="fas fa-chart-line"></i> Novo Relatório</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="saved_reports.php"><i class="fas fa-save"></i> Relatórios Salvos</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="view_logs.php"><i class="fas fa-clipboard-list"></i> Ver Logs Diários</a> </li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] == 'platform_owner'): ?>
                    <li class="nav-item"> <a class="nav-link" href="platform_owner.php"><i class="fas fa-server"></i> Painel do Dono</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_manage_orgs.php"><i class="fas fa-building"></i> Gerenciar Clientes</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_logs.php"><i class="fas fa-globe"></i> Logs Globais</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_settings.php"><i class="fas fa-cogs"></i> Configurações (Pagto)</a> </li>
                <?php endif; ?>

                <hr style="color: #6c757d;">
                <li class="nav-item"> <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a> </li>

            <?php else: ?>
                <li class="nav-item"> <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a> </li>
            <?php endif; ?>
        </ul>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'super_adm'): ?>
            <div class="sidebar-footer">
                <div class="alert alert-warning text-center small p-2">
                    <h6 class="alert-heading mb-1" style="font-weight: 300; font-size: 0.8rem;">Próximo Pagamento:</h6>
                    <p class="h6 mb-2" id="payment-countdown-sidebar" style="font-weight: 700;">Calculando...</p>
                    <a href="https://mpago.la/1VcrHae" target="_blank" class="btn btn-danger btn-sm w-100">Pagar Agora</a>
                </div>
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const countdownElement = document.getElementById("payment-countdown-sidebar");
                    if(countdownElement){
                        const anchorDate = new Date("2025-10-01T00:00:00").getTime();
                        const cycleLength = 30 * 24 * 60 * 60 * 1000;
                        function updateTimerSidebar() {
                            const now = new Date().getTime();
                            const diff = now - anchorDate;
                            const elapsedInCycle = diff % cycleLength;
                            const timeRemaining = cycleLength - elapsedInCycle;
                            const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            countdownElement.innerHTML = days + "d " + hours + "h";
                        }
                        updateTimerSidebar(); setInterval(updateTimerSidebar, 1000 * 60);
                    }
                });
                </script>
            </div>
        <?php endif; ?>
    </div>
    <div class="main-content">
        <div id="sidebarOverlay" class="sidebar-overlay"></div>