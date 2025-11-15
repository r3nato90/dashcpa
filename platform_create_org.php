<?php
session_start();
include('config/db.php');
include('config/logger.php');

// Apenas 'platform_owner' pode criar organizações
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $org_name = $_POST['org_name'];
    $plan_type = $_POST['plan_type'];
    $max_admins = (int)$_POST['max_admins'];
    $max_users = (int)$_POST['max_users'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO organizations (org_name, plan_type, max_admins, max_users, status) 
            VALUES (?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$org_name, $plan_type, $max_admins, $max_users]);
        
        $new_org_id = $pdo->lastInsertId();
        log_action($pdo, 'ORG_CREATE', "Organização '{$org_name}' (ID: {$new_org_id}) foi criada.");
        
        header('Location: platform_manage_orgs.php?status=org_created');
        exit;
    } catch (PDOException $e) {
        log_action($pdo, 'ERROR_ORG_CREATE', "Falha ao criar organização: " . $e->getMessage());
        header('Location: platform_manage_orgs.php?status=error');
        exit;
    }
}
?>