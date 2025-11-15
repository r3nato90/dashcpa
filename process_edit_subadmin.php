<?php
session_start();
include('config/db.php');
include('config/logger.php');

// **** MODIFICAÇÃO: 'admin' (N1) também pode acessar ****
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id'];
$role = $_SESSION['role']; // Role de quem está logado
$id_logado = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_sub_adm = $_POST['id_sub_adm'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $senha = $_POST['senha'];
    $percentual_comissao = $_POST['percentual_comissao'];
    $role_form = $_POST['role'];
    $parent_admin_id_form = (!empty($_POST['parent_admin_id'])) ? (int)$_POST['parent_admin_id'] : null;

    try {
        // 1. Busca o estado ATUAL do gerente no banco
        $stmt_check = $pdo->prepare("SELECT * FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ?");
        $stmt_check->execute([$id_sub_adm, $org_id]);
        $current_admin = $stmt_check->fetch();

        if (!$current_admin) {
            throw new Exception("Gerente não encontrado.");
        }

        // 2. Verifica Permissão
        if ($role == 'admin' && $current_admin['id_sub_adm'] != $id_logado && $current_admin['parent_admin_id'] != $id_logado) {
             throw new Exception("Permissão negada. Você só pode editar a si mesmo ou seus Sub-Admins diretos.");
        }
        // Super-Admin (Dono) pode editar

        // 3. Define os valores a salvar
        $novo_role = $current_admin['role'];
        $novo_parent_id = $current_admin['parent_admin_id'];

        // Apenas o Super-Admin (Dono) pode mudar o Role e o Pai
        if ($role == 'super_adm') {
            if (in_array($role_form, ['admin', 'sub_adm'])) {
                $novo_role = $role_form;
            }
            // Se o novo Role for 'admin', ele não pode ter um pai
            $novo_parent_id = ($novo_role == 'admin') ? null : $parent_admin_id_form;
        }
        
        // 4. Monta a Query
        if (!empty($senha)) {
            $sql_update = "UPDATE sub_administradores SET nome = ?, email = ?, username = ?, senha = ?, percentual_comissao = ?, role = ?, parent_admin_id = ?
                           WHERE id_sub_adm = ? AND org_id = ?";
            $params_update = [$nome, $email, $username, $senha, $percentual_comissao, $novo_role, $novo_parent_id, $id_sub_adm, $org_id];
        } else {
            $sql_update = "UPDATE sub_administradores SET nome = ?, email = ?, username = ?, percentual_comissao = ?, role = ?, parent_admin_id = ?
                           WHERE id_sub_adm = ? AND org_id = ?";
            $params_update = [$nome, $email, $username, $percentual_comissao, $novo_role, $novo_parent_id, $id_sub_adm, $org_id];
        }
        
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute($params_update);
        
        log_action($pdo, 'MANAGER_UPDATE', "Gerente '{$nome}' (ID: $id_sub_adm) foi atualizado.");
        header('Location: manage_subadmins.php?status=updated');
        exit;
    } catch (Exception $e) {
        log_action($pdo, 'ERROR_UPDATE', "Falha ao editar gerente (ID: $id_sub_adm): " . $e->getMessage());
        header('Location: manage_subadmins.php?status=error_general');
        exit;
    }
} else {
    header('Location: manage_subadmins.php');
    exit;
}
?>