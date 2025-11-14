<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas operadores (usuario) podem apagar suas transações.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'usuario') {
    header('Location: login.php');
    exit;
}
$id_usuario_logado = $_SESSION['user_id'];
$id_relatorio = $_GET['id'] ?? null;

if (!$id_relatorio) {
    header('Location: dashboard_usuario.php');
    exit;
}

try {
    // Verifica se a transação pertence ao usuário logado (segurança)
    $stmt_check = $pdo->prepare("SELECT id_relatorio FROM relatorios WHERE id_relatorio = ? AND id_usuario = ?");
    $stmt_check->execute([$id_relatorio, $id_usuario_logado]);
    
    if ($stmt_check->rowCount() > 0) {
        // Excluir a transação
        $stmt_delete = $pdo->prepare("DELETE FROM relatorios WHERE id_relatorio = ?");
        $stmt_delete->execute([$id_relatorio]);
        
        log_acao("Transação ID " . $id_relatorio . " apagada pelo usuário ID: " . $id_usuario_logado);
        header('Location: dashboard_usuario.php?status=success_deleted');
        exit;
    } else {
        log_acao("Erro de segurança: Tentativa de apagar transação de ID inválido ou não pertencente ao usuário ID: " . $id_usuario_logado);
        header('Location: dashboard_usuario.php?status=error_security');
        exit;
    }
} catch (PDOException $e) {
    log_acao("Erro PDO ao apagar transação ID " . $id_relatorio . ": " . $e->getMessage());
    header('Location: dashboard_usuario.php?status=error_delete');
    exit;
}
?>