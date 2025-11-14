<?php
// O session_start() deve estar no topo de cada página ANTES de incluir este header.

// Garante que a conexão $pdo exista
if (!isset($pdo)) {
    include_once(__DIR__ . '/../config/db.php');
}

$brand_name = "DashCPA"; // Nome padrão

// **** LÓGICA DE TEMAS (LÊ O BD E APLICA AS CORES) ****
try {
    $stmt_themes = $pdo->query("SELECT theme_key, theme_value FROM platform_themes WHERE org_id = 0");
    $theme_settings_raw = $stmt_themes->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    // Fallback se a tabela não existir
    $theme_settings_raw = [
        'bg_color_sidebar' => '#0D1117', 'bg_color_content' => '#161B22', 'text_color_primary' => '#E6EDF3',
        'text_color_secondary' => '#FFFFFF', 'button_color_primary' => '#754FFE', 'button_color_success' => '#00D093',
        'owner_bg_color' => '#7600ff', 'footer_bg_color' => '#0D1117', 'footer_text_color' => '#FFFFFF'
    ];
}
$theme_css = '';
foreach ($theme_settings_raw as $key => $value) {
    $theme_css .= "--{$key}: {$value};";
}
$bg_owner = $theme_settings_raw['owner_bg_color'];
// **** FIM DA LÓGICA DE TEMAS ****


// Determina o nome da marca (Logo)
if (isset($_SESSION['role']) && isset($_SESSION['org_id'])) {
    if ($_SESSION['role'] == 'platform_owner') {
        $brand_name = "DashCPA";
    } else {
        try {
            $stmt_org_name = $pdo->prepare("SELECT org_name FROM organizations WHERE org_id = ?");
            $stmt_org_name->execute([$_SESSION['org_id']]);
            $org = $stmt_org_name->fetch();
            if ($org && !empty($org['org_name'])) {
                $brand_name = $org['org_name'];
            }
        } catch (PDOException $e) {}
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($brand_name); ?> - Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="/favicon/favicon.jpeg" type="image/jpeg">

    <style>
        :root {
            <?php echo $theme_css; /* Variáveis Dinâmicas */ ?>
            --primary-color: var(--button_color_primary, #754FFE); 
            --success-color: var(--button_color_success, #00D093);
            --dark-bg-primary: var(--bg_color_sidebar, #0D1117); 
            --dark-bg-secondary: var(--bg_color_content, #161B22); 
            --border-color: #30363D;
            --text-primary: var(--text_color_primary, #E6EDF3); 
            --text-secondary: var(--text_color_secondary, #FFFFFF); /* BRANCO */
            --footer-bg: var(--footer_bg_color, #0D1117);
            --footer-text: var(--footer_text_color, #FFFFFF);
            --sidebar-width: 260px;
            --topbar-height: 70px;
        }
        body { 
            background-color: var(--dark-bg-secondary); 
            color: var(--text-primary); 
            overflow-x: hidden; 
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'platform_owner') echo "background-color: var(--owner_bg_color, #7600ff) !important;"; ?>
        }
        
        /* --- Sidebar (Menu Lateral) --- */
        .sidebar { position: fixed; top: 0; left: 0; height: 100%; width: var(--sidebar-width); background-color: var(--dark-bg-primary); border-right: 1px solid var(--border-color); padding: 1.5rem 1rem; transition: left 0.3s ease-in-out; z-index: 1030; display: flex; flex-direction: column; }
        .sidebar-brand { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); text-decoration: none; padding: 0.5rem 0; margin-bottom: 1.5rem; text-align: center; }
        .sidebar-nav { list-style: none; padding-left: 0; flex-grow: 1; overflow-y: auto; }
        .sidebar-nav .nav-item { margin-bottom: 0.25rem; }
        .sidebar-nav .nav-link { color: var(--text-secondary); text-decoration: none; display: block; padding: 0.75rem 1rem; border-radius: 0.375rem; transition: all 0.2s; font-weight: 500; }
        .sidebar-nav .nav-link:hover { color: var(--text-primary); }
        .sidebar-nav .nav-link.active, .sidebar-nav .nav-item:hover .nav-link { background-color: var(--primary-color); color: #fff; }
        .sidebar-nav .nav-link i { margin-right: 0.75rem; width: 20px; text-align: center; }

        /* --- Topbar (Menu Superior) --- */
        .topbar { position: fixed; top: 0; left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); height: var(--topbar-height); background-color: var(--dark-bg-secondary); border-bottom: 1px solid var(--border-color); padding: 0 2rem; z-index: 1020; display: flex; justify-content: space-between; align-items: center; transition: left 0.3s ease-in-out, width 0.3s ease-in-out; }
        .main-content { margin-left: var(--sidebar-width); margin-top: var(--topbar-height); padding: 2rem; transition: margin-left 0.3s ease-in-out; }
        .footer-main { margin-left: var(--sidebar-width); transition: margin-left 0.3s ease-in-out; background-color: var(--footer-bg); color: var(--footer-text); }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1029; }
        
        /* --- Estilos dos Cards (Dark Mode) --- */
        .card { background-color: var(--dark-bg-primary); border: 1px solid var(--border-color); border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .card-header { background-color: var(--dark-bg-primary); border-bottom: 1px solid var(--border-color); }
        .kpi-card-1 { background: linear-gradient(135deg, #FF6B6B, #FA5252); color: #fff; }
        .kpi-card-2 { background: linear-gradient(135deg, #00D093, #00B37F); color: #fff; }
        .kpi-card-3 { background: linear-gradient(135deg, #AF31FF, #8E30FF); color: #fff; }
        .kpi-card-4 { background: linear-gradient(135deg, #4299FF, #3B82F6); color: #fff; }
        .kpi-card .kpi-icon { font-size: 2.5rem; opacity: 0.8; }
        .kpi-card h5 { color: rgba(255, 255, 255, 0.8); font-size: 1rem; margin-bottom: 0.25rem; }
        .kpi-card h3 { color: var(--text-primary); font-weight: 700; }
        
        /* --- Elementos Bootstrap (Dark Mode) --- */
        .table { --bs-table-color: var(--text-primary); --bs-table-border-color: var(--border-color); --bs-table-striped-bg: #161B22; --bs-table-hover-bg: #222831; }
        .table-light { --bs-table-color: var(--text-primary); --bs-table-bg: #222831; }
        .modal-content { background-color: var(--dark-bg-primary); border: 1px solid var(--border-color); }
        .modal-header { border-bottom: 1px solid var(--border-color); }
        .form-control { background-color: var(--dark-bg-secondary); border-color: var(--border-color); color: var(--text-primary); }
        .form-control:focus { background-color: var(--dark-bg-secondary); border-color: var(--primary-color); color: var(--text-primary); box-shadow: 0 0 0 0.25rem rgba(117, 79, 254, 0.25); }
        .form-select { background-color: var(--dark-bg-secondary); border-color: var(--border-color); color: var(--text-primary); }
        
        /* Aplicação de Botões Dinâmicos */
        .btn-primary { background-color: var(--button_color_primary); border: none; }
        .btn-success { background-color: var(--button_color_success); border: none; }
        .btn-outline-primary { color: var(--button_color_primary); border-color: var(--button_color_primary); }
        .btn-outline-primary:hover { background-color: var(--button_color_primary); color: #fff; }
        
        /* Correção de Texto */
        .text-primary { color: var(--primary-color) !important; }
        .text-muted { color: var(--text-secondary) !important; opacity: 0.7; }
        .text-secondary { color: var(--text-secondary) !important; }
        
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
<body class="body-dark-mode">

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="sidebar">
        <a class="sidebar-brand" href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'platform_owner') ? 'platform_owner.php' : 'index.php'; ?>">
            <?php echo htmlspecialchars($brand_name); ?>
        </a>
        
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
                    <li class="nav-item"> <a class="nav-link" href="platform_manage_accounts.php"><i class="fas fa-users"></i> Contas Globais</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_manage_plans.php"><i class="fas fa-gem"></i> Gerenciar Planos</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_financial_reports.php"><i class="fas fa-chart-pie"></i> Financeiro</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_transaction_logs.php"><i class="fas fa-exchange-alt"></i> Log de Transações</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_theme_editor.php"><i class="fas fa-palette"></i> Editor de Tema</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_logs.php"><i class="fas fa-globe"></i> Logs Globais</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="platform_settings.php"><i class="fas fa-cogs"></i> Configurações (Pagto)</a> </li>
                <?php endif; ?>

                <hr style="border-top: 1px solid var(--border-color);">
                <li class="nav-item"> <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a> </li>

            <?php else: ?>
                <li class="nav-item"> <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a> </li>
                <li class="nav-item"> <a class="nav-link" href="index.php"><i class="fas fa-gem"></i> Ver Planos</a> </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="main-content-wrapper">
        <nav class="navbar topbar">
            <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <ul class="topbar-nav">
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])): ?>
                <li class="nav-item">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarRelatorio" style="background-color: var(--button_color_success); border: none;">
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