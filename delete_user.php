<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas Super Admin, Admin e Sub-Admin podem apagar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];
$id_usuario_delete = $_GET['id'] ?? null;

if (!$id_usuario_delete) {
    header('Location: manage_users.php');
    exit;
}

try {
    // 1. Buscar dados do usuário a ser apagado e verificar permissão
    $stmt_check = $pdo->prepare("SELECT nome, manager_id FROM usuarios WHERE id = ? AND role = 'usuario'");
    $stmt_check->execute([$id_usuario_delete]);
    $usuario_delete_data = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $nome_usuario = $usuario_delete_data['nome'] ?? 'ID ' . $id_usuario_delete;

    if (!$usuario_delete_data) {
        header('Location: manage_users.php?status=error_not_found');
        exit;
    }

    // Se não for super_adm, verifica se é o manager do usuário
    if ($role_logado != 'super_adm' && $usuario_delete_data['manager_id'] != $id_logado) {
        log_acao("Acesso negado (exclusão): Usuário ID " . $id_logado . " tentou apagar operador ID " . $id_usuario_delete);
        header('Location: manage_users.php?status=error_permission');
        exit;
    }
    
    // 2. Excluir os relatórios associados (CASCADE ou DELETE manual)
    // Se o banco de dados não tiver CASCADE ON DELETE configurado:
    // $stmt_delete_reports = $pdo->prepare("DELETE FROM relatorios WHERE id_usuario = ?");
    // $stmt_delete_reports->execute([$id_usuario_delete]);
    
    // 3. Excluir o usuário
    $stmt_delete_user = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt_delete_user->execute([$id_usuario_delete]);
    
    log_acao("Operador " . $nome_usuario . " (ID " . $id_usuario_delete . ") apagado por " . $_SESSION['username'] . " (" . $role_logado . ")");

    header('Location: manage_users.php?status=deleted');
    exit;

} catch (PDOException $e) {
    log_acao("Erro PDO ao apagar operador ID " . $id_usuario_delete . ": " . $e->getMessage());
    header('Location: manage_users.php?status=error_db');
    exit;
}
?>