<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Incluído

// Apenas 'super_adm' pode processar
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm') {
    log_action($pdo, 'ERROR_AUTH', "Tentativa não autorizada de editar gerente.");
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coletar dados do formulário
    $id_sub_adm = $_POST['id_sub_adm'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $percentual_comissao = $_POST['percentual_comissao'];
    $role = $_POST['role'];

    // Impede que o admin ID 1 seja rebaixado de 'super_adm'
    if ($id_sub_adm == 1 && $role != 'super_adm') {
        $role = 'super_adm';
    }

    try {
        // Se a senha foi fornecida, atualiza a senha
        if (!empty($senha)) {
            $sql_update = "
                UPDATE sub_administradores 
                SET nome = ?, email = ?, username = ?, senha = ?, percentual_comissao = ?, role = ?
                WHERE id_sub_adm = ?
            ";
            $params_update = [$nome, $email, $username, $senha, $percentual_comissao, $role, $id_sub_adm];
        } else {
            // Se a senha estiver vazia, não atualiza a senha
            $sql_update = "
                UPDATE sub_administradores 
                SET nome = ?, email = ?, username = ?, percentual_comissao = ?, role = ?
                WHERE id_sub_adm = ?
            ";
            $params_update = [$nome, $email, $username, $percentual_comissao, $role, $id_sub_adm];
        }
        
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute($params_update);
        
        log_action($pdo, 'MANAGER_UPDATE', "Gerente '{$nome}' (ID: $id_sub_adm) foi atualizado.");
        header('Location: manage_subadmins.php?status=updated');
        exit;

    } catch (PDOException $e) {
        $error_code = $e->errorInfo[1] ?? 0;
        $error_message = $e->getMessage();
        
        if ($error_code == 1062) { // Erro de duplicidade (Email ou Username)
            $error_message = ($e->errorInfo[2] ?? '' == 'username') ? "Username duplicado." : "Email duplicado.";
            log_action($pdo, 'ERROR_UPDATE', "Falha ao editar gerente (ID: $id_sub_adm): $error_message");
            echo "Erro: O Nome de Usuário ou Email escolhido já existe. <a href='javascript:history.back()'>Voltar</a>";
        } else {
             log_action($pdo, 'ERROR_UPDATE', "Falha ao editar gerente (ID: $id_sub_adm): " . $e->getMessage());
            echo "Erro ao atualizar Sub-Administrador: " . $e->getMessage();
        }
    }
} else {
    header('Location: manage_subadmins.php');
    exit;
}
?>