<?php
session_start();
include('config/db.php');
include('config/logger.php');

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha']; 
    $percentual_comissao = $_POST['percentual_comissao'];

    try {
        // **** MODIFICADO: Verifica DENTRO da organização ****
        $where_clause = " WHERE id_usuario = ? AND org_id = ?";
        $params_check = [$id_usuario, $org_id];

        if ($role == 'admin' || $role == 'sub_adm') {
            $where_clause .= " AND id_sub_adm = ?";
            $params_check[] = $id_logado;
        }
        $stmt_check = $pdo->prepare("SELECT id_usuario FROM usuarios" . $where_clause);
        $stmt_check->execute($params_check);
        if (!$stmt_check->fetch()) {
            throw new Exception("Permissão negada (ID: $id_usuario).");
        }

        $params_update = [];
        if ($role == 'super_adm') {
            $id_sub_adm = (!empty($_POST['id_sub_adm'])) ? (int)$_POST['id_sub_adm'] : null;
            if (!empty($senha)) {
                $sql_update = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, percentual_comissao = ?, id_sub_adm = ? WHERE id_usuario = ? AND org_id = ?";
                $params_update = [$nome, $email, $senha, $percentual_comissao, $id_sub_adm, $id_usuario, $org_id];
            } else {
                $sql_update = "UPDATE usuarios SET nome = ?, email = ?, percentual_comissao = ?, id_sub_adm = ? WHERE id_usuario = ? AND org_id = ?";
                $params_update = [$nome, $email, $percentual_comissao, $id_sub_adm, $id_usuario, $org_id];
            }
        } else {
            if (!empty($senha)) {
                $sql_update = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, percentual_comissao = ? WHERE id_usuario = ? AND id_sub_adm = ? AND org_id = ?";
                $params_update = [$nome, $email, $senha, $percentual_comissao, $id_usuario, $id_logado, $org_id];
            } else {
                $sql_update = "UPDATE usuarios SET nome = ?, email = ?, percentual_comissao = ? WHERE id_usuario = ? AND id_sub_adm = ? AND org_id = ?";
                $params_update = [$nome, $email, $percentual_comissao, $id_usuario, $id_logado, $org_id];
            }
        }
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute($params_update);
        log_action($pdo, 'USER_UPDATE', "Usuário '{$nome}' (ID: $id_usuario) foi atualizado.");
        header('Location: manage_users.php?status=updated');
        exit;
    } catch (Exception $e) {
        log_action($pdo, 'ERROR_UPDATE', "Falha ao editar usuário (ID: $id_usuario): " . $e->getMessage());
        echo "Erro: " . $e->getMessage();
    }
} else {
    header('Location: manage_users.php');
    exit;
}
?>