<?php
session_start();
include('config/db.php');
include('config/logger.php');

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'super_adm' || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_sub_adm = $_POST['id_sub_adm'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $percentual_comissao = $_POST['percentual_comissao'];
    $role = $_POST['role'];

    // Garante que o cliente não possa escalar para super_adm ou platform_owner
    if (!in_array($role, ['admin', 'sub_adm'])) {
        $role = 'sub_adm'; 
    }

    try {
        // **** MODIFICADO: Atualiza DENTRO da organização ****
        if (!empty($senha)) {
            $sql_update = "UPDATE sub_administradores SET nome = ?, email = ?, username = ?, senha = ?, percentual_comissao = ?, role = ?
                           WHERE id_sub_adm = ? AND org_id = ?";
            $params_update = [$nome, $email, $username, $senha, $percentual_comissao, $role, $id_sub_adm, $org_id];
        } else {
            $sql_update = "UPDATE sub_administradores SET nome = ?, email = ?, username = ?, percentual_comissao = ?, role = ?
                           WHERE id_sub_adm = ? AND org_id = ?";
            $params_update = [$nome, $email, $username, $percentual_comissao, $role, $id_sub_adm, $org_id];
        }
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute($params_update);
        
        log_action($pdo, 'MANAGER_UPDATE', "Gerente '{$nome}' (ID: $id_sub_adm) foi atualizado.");
        header('Location: manage_subadmins.php?status=updated');
        exit;
    } catch (PDOException $e) {
        // ... (Lógica de erro) ...
        log_action($pdo, 'ERROR_UPDATE', "Falha ao editar gerente (ID: $id_sub_adm): " . $e->getMessage());
        echo "Erro: " . $e->getMessage();
    }
} else {
    header('Location: manage_subadmins.php');
    exit;
}
?>