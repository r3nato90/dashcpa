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
    $id_sub_adm = $_POST['id_sub_adm'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $percentual_comissao = $_POST['percentual_comissao'];
    $role = $_POST['role'];
    $nova_org_id = (int)$_POST['org_id'];

    // Validação da Role (segurança)
    if (!in_array($role, ['super_adm', 'admin', 'sub_adm'])) {
        $role = 'sub_adm'; // Default seguro
    }

    try {
        // 2. Buscar o admin e sua organização atual
        $stmt_admin = $pdo->prepare("SELECT org_id FROM sub_administradores WHERE id_sub_adm = ?");
        $stmt_admin->execute([$id_sub_adm]);
        $admin = $stmt_admin->fetch();

        if (!$admin) {
            throw new Exception("Gerente não encontrado.");
        }
        $antiga_org_id = $admin['org_id'];

        // 3. Preparar a query de atualização
        $params_update = [];
        $sql_update = "";
        
        // 4. Lógica de Vínculo: Se a Organização mudou, desvincula os usuários dele
        $desvincular_sql = "";
        if ($antiga_org_id != $nova_org_id) {
            $stmt_desvincular = $pdo->prepare("UPDATE usuarios SET id_sub_adm = NULL WHERE id_sub_adm = ? AND org_id = ?");
            $stmt_desvincular->execute([$id_sub_adm, $antiga_org_id]);
        }

        if (!empty($senha)) {
            $sql_update = "UPDATE sub_administradores SET nome = ?, email = ?, username = ?, senha = ?, percentual_comissao = ?, role = ?, org_id = ? WHERE id_sub_adm = ?";
            $params_update = [$nome, $email, $username, $senha, $percentual_comissao, $role, $nova_org_id, $id_sub_adm];
        } else {
            $sql_update = "UPDATE sub_administradores SET nome = ?, email = ?, username = ?, percentual_comissao = ?, role = ?, org_id = ? WHERE id_sub_adm = ?";
            $params_update = [$nome, $email, $username, $percentual_comissao, $role, $nova_org_id, $id_sub_adm];
        }
        
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute($params_update);

        // 5. Log
        $log_message = "Gerente '{$nome}' (ID: $id_sub_adm) foi atualizado.";
        if ($antiga_org_id != $nova_org_id) {
            $log_message .= " Gerente movido da Org ID: $antiga_org_id para a Org ID: $nova_org_id.";
        }
        log_action($pdo, 'MANAGER_UPDATE_GLOBAL', $log_message);
        
        header('Location: platform_manage_orgs.php?status=org_updated'); // Redireciona de volta para a lista de Orgs
        exit;

    } catch (Exception $e) {
        log_action($pdo, 'ERROR_MANAGER_UPDATE_GLOBAL', "Falha ao editar gerente (ID: $id_sub_adm): " . $e->getMessage());
        header('Location: platform_manage_orgs.php?status=error');
        exit;
    }
} else {
    header('Location: platform_manage_orgs.php');
    exit;
}
?>