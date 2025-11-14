<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// Redireciona para o dashboard apropriado baseado no papel (role) do usuário
$role = $_SESSION['role'];

switch ($role) {
    case 'super_adm':
        header('Location: dashboard_superadmin.php');
        break;
    case 'admin':
        header('Location: dashboard_admin.php');
        break;
    case 'sub_adm':
        header('Location: dashboard_subadmin.php');
        break;
    case 'usuario':
        header('Location: dashboard_usuario.php');
        break;
    default:
        // Caso a role não seja reconhecida ou não esteja logado, redireciona para login
        header('Location: login.php');
        break;
}
exit;
?>