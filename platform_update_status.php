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
    $org_id = (int)$_POST['org_id'];
    $new_status = $_POST['new_status'];

    // Valida o status para evitar injeção de dados
    if (!in_array($new_status, ['active', 'inactive', 'suspended'])) {
        log_action($pdo, 'ERROR_STATUS_UPDATE', "Tentativa de status inválido ({$new_status}) para Org ID: {$org_id}.");
        header('Location: platform_manage_orgs.php?status=error');
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE organizations 
            SET status = ?
            WHERE org_id = ?
        ");
        $stmt->execute([$new_status, $org_id]);
        
        log_action($pdo, 'ORG_STATUS_UPDATE', "Status da Organização (ID: {$org_id}) foi alterado para '{$new_status}'.");
        
        header('Location: platform_manage_orgs.php?status=status_updated');
        exit;
    } catch (PDOException $e) {
        log_action($pdo, 'ERROR_STATUS_UPDATE', "Falha ao atualizar status da Org ID {$org_id}: " . $e->getMessage());
        header('Location: platform_manage_orgs.php?status=error');
        exit;
    }
}
?>