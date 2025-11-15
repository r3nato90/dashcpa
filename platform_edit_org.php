<?php
session_start();
include('config/db.php');
include('config/logger.php');

// Apenas 'platform_owner' pode editar
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $org_id = (int)$_POST['org_id'];
    $org_name = $_POST['org_name']; // **** NOVO CAMPO ****
    $max_admins = (int)$_POST['max_admins'];
    $max_users = (int)$_POST['max_users'];

    try {
        // **** QUERY ATUALIZADA para incluir org_name ****
        $stmt = $pdo->prepare("
            UPDATE organizations 
            SET org_name = ?, max_admins = ?, max_users = ?
            WHERE org_id = ?
        ");
        $stmt->execute([$org_name, $max_admins, $max_users, $org_id]);
        
        log_action($pdo, 'ORG_UPDATE', "Organização '{$org_name}' (ID: {$org_id}) foi atualizada.");
        
        header('Location: platform_manage_orgs.php?status=org_updated');
        exit;
    } catch (PDOException $e) {
        log_action($pdo, 'ERROR_ORG_UPDATE', "Falha ao atualizar organização: " . $e->getMessage());
        header('Location: platform_manage_orgs.php?status=error');
        exit;
    }
}
?>