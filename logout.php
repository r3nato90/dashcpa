<?php
session_start();

// Inclui o sistema de Log se ainda não estiver incluído
if (!function_exists('log_acao')) {
    include('config/db.php');
    include('config/logger.php');
}

// Registra a ação de logout se o usuário estava logado
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'] ?? 'Desconhecido';
    $role = $_SESSION['role'] ?? 'Desconhecido';
    log_acao("Logout bem-sucedido. Usuário: " . $username . " (" . $role . ")");
}

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se for preciso destruir completamente a sessão, apague também o cookie de sessão.
// Nota: Isso apagará a sessão e não apenas os dados da sessão!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão.
session_destroy();

// Redireciona para a página de login
header('Location: login.php');
exit;
?>