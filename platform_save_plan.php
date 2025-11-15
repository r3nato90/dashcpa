<?php
session_start();
include('config/db.php');
include('config/logger.php');

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Coleta de dados
    $plan_id = (int)$_POST['plan_id'];
    $price_description = $_POST['price_description'];
    $feature_1 = $_POST['feature_1'];
    $feature_2 = $_POST['feature_2'];
    $feature_3 = $_POST['feature_3'];
    $feature_4 = $_POST['feature_4'];
    $mercadopago_link = $_POST['mercadopago_link'];
    $default_max_admins = (int)$_POST['default_max_admins'];
    $default_max_users = (int)$_POST['default_max_users'];

    try {
        $stmt = $pdo->prepare("
            UPDATE plans SET
                price_description = ?,
                feature_1 = ?,
                feature_2 = ?,
                feature_3 = ?,
                feature_4 = ?,
                mercadopago_link = ?,
                default_max_admins = ?,
                default_max_users = ?
            WHERE plan_id = ?
        ");
        $stmt->execute([
            $price_description, $feature_1, $feature_2, $feature_3, $feature_4,
            $mercadopago_link, $default_max_admins, $default_max_users, $plan_id
        ]);
        
        log_action($pdo, 'PLAN_UPDATE', "Plano (ID: {$plan_id}) foi atualizado.");
        
        header('Location: platform_manage_plans.php?status=plan_updated');
        exit;
    } catch (PDOException $e) {
        log_action($pdo, 'ERROR_PLAN_UPDATE', "Falha ao atualizar plano (ID: {$plan_id}): " . $e->getMessage());
        header('Location: platform_manage_plans.php?status=error');
        exit;
    }
}
?>