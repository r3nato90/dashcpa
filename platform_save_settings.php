<?php
session_start();
include('config/db.php');
include('config/logger.php');

// Verificação de segurança: Apenas 'platform_owner'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    log_action($pdo, 'ERROR_AUTH', "Tentativa não autorizada de salvar configurações.");
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pega os valores do POST
    $public_key = $_POST['mp_public_key'] ?? '';
    $access_token = $_POST['mp_access_token'] ?? '';

    try {
        $pdo->beginTransaction();

        // Query 1: Salva a Public Key
        // (INSERT ... ON DUPLICATE KEY UPDATE é uma forma segura de inserir ou atualizar)
        $stmt1 = $pdo->prepare("
            INSERT INTO platform_settings (setting_key, setting_value) 
            VALUES ('mp_public_key', ?)
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        $stmt1->execute([$public_key, $public_key]);

        // Query 2: Salva o Access Token
        // (Só atualiza o Access Token se um novo valor for enviado)
        if (!empty($access_token)) {
            $stmt2 = $pdo->prepare("
                INSERT INTO platform_settings (setting_key, setting_value) 
                VALUES ('mp_access_token', ?)
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt2->execute([$access_token, $access_token]);
        }
        
        $pdo->commit();

        log_action($pdo, 'SETTINGS_UPDATE', "Configurações de pagamento (Mercado Pago) foram atualizadas.");
        
        // Redireciona de volta com sucesso
        header('Location: platform_settings.php?status=success');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        log_action($pdo, 'ERROR_SETTINGS_UPDATE', "Falha ao salvar configurações: " . $e->getMessage());
        header('Location: platform_settings.php?status=error');
        exit;
    }
} else {
    header('Location: platform_settings.php');
    exit;
}
?>