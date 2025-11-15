<?php
session_start();
include('config/db.php');
include('config/logger.php');

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}
$id_usuario = (int)$_GET['id'];

try {
    // **** MODIFICADO: Busca DENTRO da organização ****
    $query_check = "SELECT nome, id_sub_adm FROM usuarios WHERE id_usuario = ? AND org_id = ?";
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([$id_usuario, $org_id]);
    $user = $stmt_check->fetch();
    $nome_usuario_apagado = $user['nome'] ?? 'ID ' . $id_usuario;

    if (!$user) {
        throw new Exception("Usuário (ID: $id_usuario) não encontrado.");
    }
    if ($role != 'super_adm' && $user['id_sub_adm'] != $id_logado) {
        throw new Exception("Permissão negada (ID: $id_usuario).");
    }
    
    $pdo->beginTransaction();
    // **** MODIFICADO: Filtra por org_id ****
    $stmt_rel = $pdo->prepare("UPDATE relatorios SET id_usuario = NULL WHERE id_usuario = ? AND org_id = ?");
    $stmt_rel->execute([$id_usuario, $org_id]);
    $stmt_trans = $pdo->prepare("UPDATE transacoes SET id_usuario = NULL WHERE id_usuario = ? AND org_id = ?");
    $stmt_trans->execute([$id_usuario, $org_id]);
    $stmt_del = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND org_id = ?");
    $stmt_del->execute([$id_usuario, $org_id]);
    $pdo->commit();

    log_action($pdo, 'USER_DELETE', "Usuário '{$nome_usuario_apagado}' (ID: $id_usuario) foi apagado.");
    header('Location: manage_users.php?status=deleted');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    log_action($pdo, 'ERROR_DELETE', "Falha ao apagar usuário (ID: $id_usuario): " . $e->getMessage());
    header('Location: manage_users.php?status=error_delete&msg=' . urlencode($e->getMessage()));
    exit;
}
?>