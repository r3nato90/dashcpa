<?php
session_start();
include('config/db.php');
include('config/logger.php');

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
    $percentual_comissao = $_POST['percentual_comissao']; // Adicionado
    $nova_org_id = (int)$_POST['org_id'];
    
    // **** MODIFICAÇÃO: Captura o ID do Sub-Admin ****
    $id_sub_adm = (!empty($_POST['id_sub_adm'])) ? (int)$_POST['id_sub_adm'] : null;

    try {
        $stmt_user = $pdo->prepare("SELECT org_id FROM usuarios WHERE id_usuario = ?");
        $stmt_user->execute([$id_usuario]);
        $user = $stmt_user->fetch();

        if (!$user) {
            throw new Exception("Usuário não encontrado.");
        }
        
        // 3. Preparar a query de atualização
        $params_update = [];
        $sql_update_parts = ["nome = ?", "email = ?", "org_id = ?", "percentual_comissao = ?", "id_sub_adm = ?"];
        $params_update = [$nome, $email, $nova_org_id, $percentual_comissao, $id_sub_adm];

        if (!empty($senha)) {
            $sql_update_parts[] = "senha = ?";
            $params_update[] = $senha;
        }
        
        // Adiciona o ID do usuário no final dos parâmetros
        $params_update[] = $id_usuario;
        
        $sql_update = "UPDATE usuarios SET " . implode(", ", $sql_update_parts) . " WHERE id_usuario = ?";
        
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute($params_update);

        log_action($pdo, 'USER_UPDATE_GLOBAL', "Usuário '{$nome}' (ID: $id_usuario) foi atualizado globalmente.");
        
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