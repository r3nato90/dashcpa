<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Incluído

// Apenas Gerentes (todos os níveis) podem apagar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$id_usuario = (int)$_GET['id'];

try {
    // --- Lógica de Segurança ---
    $query_check = "SELECT nome, id_sub_adm FROM usuarios WHERE id_usuario = ?";
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([$id_usuario]);
    $user = $stmt_check->fetch();
    $nome_usuario_apagado = $user['nome'] ?? 'ID ' . $id_usuario;

    if (!$user) {
        throw new Exception("Usuário (ID: $id_usuario) não encontrado.");
    }

    // Se for Admin/Sub-Admin, verifica se o usuário é dele
    if ($role != 'super_adm' && $user['id_sub_adm'] != $id_logado) {
        throw new Exception("Permissão negada. Você não pode apagar um usuário que não é seu (ID: $id_usuario).");
    }
    
    // --- Início da Transação de Exclusão ---
    $pdo->beginTransaction();

    // 1. Desvincular relatórios (define id_usuario como NULL)
    $stmt_rel = $pdo->prepare("UPDATE relatorios SET id_usuario = NULL WHERE id_usuario = ?");
    $stmt_rel->execute([$id_usuario]);

    // 2. Desvincular transações (define id_usuario como NULL)
    $stmt_trans = $pdo->prepare("UPDATE transacoes SET id_usuario = NULL WHERE id_usuario = ?");
    $stmt_trans->execute([$id_usuario]);

    // 3. Apagar o usuário
    $stmt_del = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt_del->execute([$id_usuario]);

    // 4. Confirmar a transação
    $pdo->commit();

    log_action($pdo, 'USER_DELETE', "Usuário '{$nome_usuario_apagado}' (ID: $id_usuario) foi permanentemente apagado.");
    header('Location: manage_users.php?status=deleted');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    log_action($pdo, 'ERROR_DELETE', "Falha ao apagar usuário (ID: $id_usuario): " . $e->getMessage());
    header('Location: manage_users.php?status=error_delete&msg=' . urlencode($e->getMessage()));
    exit;
}
?>