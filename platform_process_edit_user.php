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
    
    // 1. Coletar dados
    $id_usuario = $_POST['id_usuario'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nova_org_id = (int)$_POST['org_id'];

    try {
        // 2. Buscar o usuário e sua organização atual
        $stmt_user = $pdo->prepare("SELECT org_id FROM usuarios WHERE id_usuario = ?");
        $stmt_user->execute([$id_usuario]);
        $user = $stmt_user->fetch();

        if (!$user) {
            throw new Exception("Usuário não encontrado.");
        }
        $antiga_org_id = $user['org_id'];
        
        // 3. Preparar a query de atualização
        $params_update = [];
        $sql_update = "";
        
        // 4. Lógica de Vínculo: Se a Organização mudou, desvincula do gerente (seta id_sub_adm = NULL)
        $id_sub_adm_sql_snippet = ($antiga_org_id != $nova_org_id) ? "id_sub_adm = NULL, " : "";

        if (!empty($senha)) {
            $sql_update = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, org_id = ?, {$id_sub_adm_sql_snippet} data_atualizacao = NOW() WHERE id_usuario = ?";
            $params_update = [$nome, $email, $senha, $nova_org_id, $id_usuario];
        } else {
            $sql_update = "UPDATE usuarios SET nome = ?, email = ?, org_id = ?, {$id_sub_adm_sql_snippet} data_atualizacao = NOW() WHERE id_usuario = ?";
            $params_update = [$nome, $email, $nova_org_id, $id_usuario];
        }
        
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute($params_update);

        // 5. Log
        $log_message = "Usuário '{$nome}' (ID: $id_usuario) foi atualizado.";
        if ($antiga_org_id != $nova_org_id) {
            $log_message .= " Usuário movido da Org ID: $antiga_org_id para a Org ID: $nova_org_id.";
        }
        log_action($pdo, 'USER_UPDATE_GLOBAL', $log_message);
        
        header('Location: platform_manage_users.php?status=user_updated');
        exit;

    } catch (Exception $e) {
        log_action($pdo, 'ERROR_USER_UPDATE_GLOBAL', "Falha ao editar usuário (ID: $id_usuario): " . $e->getMessage());
        header('Location: platform_manage_users.php?status=error');
        exit;
    }
} else {
    header('Location: platform_manage_users.php');
    exit;
}
?>