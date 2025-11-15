<?php
session_start();
include('config/db.php');
include('config/logger.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha']; 
    $percentual_comissao_form = $_POST['percentual_comissao']; 

    try {
        // 1. Busca o estado ATUAL do usuário no banco
        $stmt_check = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ? AND org_id = ?");
        $stmt_check->execute([$id_usuario, $org_id]);
        $current_user = $stmt_check->fetch();

        if (!$current_user) {
            throw new Exception("Usuário não encontrado.");
        }

        // 2. Verifica Permissão de Hierarquia
        if ($role == 'admin') {
            // Admin (N1) só pode editar usuários (N3) que pertencem aos seus Sub-Admins (N2) OU a ele mesmo
            $parent_admin_id = null;
            if ($current_user['id_sub_adm']) {
                $stmt_check_permission = $pdo->prepare("
                    SELECT parent_admin_id 
                    FROM sub_administradores 
                    WHERE id_sub_adm = ? AND org_id = ?
                ");
                $stmt_check_permission->execute([$current_user['id_sub_adm'], $org_id]);
                $parent_admin_id = $stmt_check_permission->fetchColumn();
            }

            if ($parent_admin_id != $id_logado && $current_user['id_sub_adm'] != $id_logado) {
                 throw new Exception("Permissão negada (Admin).");
            }
        } elseif ($role == 'sub_adm') {
            // Sub-Adm (N2) só pode editar seus próprios usuários (N3)
            if ($current_user['id_sub_adm'] != $id_logado) {
                throw new Exception("Permissão negada (Sub-Adm).");
            }
        }
        // Super-Admin (Dono) pode editar (passa direto)

        // 3. Define os valores a salvar (REGRA DE NEGÓCIO)
        $novo_percentual_comissao = $current_user['percentual_comissao']; // Valor antigo por padrão
        $novo_id_sub_adm = $current_user['id_sub_adm']; // Valor antigo por padrão

        // Apenas Super-Admin (Dono) e Admin (N1) podem mudar comissão e vínculo
        if ($role == 'super_adm' || $role == 'admin') {
            $novo_percentual_comissao = $percentual_comissao_form; // Pega o valor do form
            
            if (isset($_POST['id_sub_adm'])) {
                 $novo_id_sub_adm = (!empty($_POST['id_sub_adm'])) ? (int)$_POST['id_sub_adm'] : null;
            }
        }
        
        // 4. Monta a Query
        $params_update = [];
        $sql_update_parts = ["nome = ?", "email = ?", "percentual_comissao = ?", "id_sub_adm = ?"];
        $params_update = [$nome, $email, $novo_percentual_comissao, $novo_id_sub_adm];

        if (!empty($senha)) {
            $sql_update_parts[] = "senha = ?";
            $params_update[] = $senha;
        }
        
        $params_update[] = $id_usuario;
        $params_update[] = $org_id;

        $sql_update = "UPDATE usuarios SET " . implode(", ", $sql_update_parts) . " WHERE id_usuario = ? AND org_id = ?";

        $stmt = $pdo->prepare($sql_update);
        $stmt->execute($params_update);
        
        log_action($pdo, 'USER_UPDATE', "Usuário '{$nome}' (ID: $id_usuario) foi atualizado.");
        header('Location: manage_users.php?status=updated');
        exit;
    } catch (Exception $e) {
        log_action($pdo, 'ERROR_UPDATE', "Falha ao editar usuário (ID: $id_usuario): " . $e->getMessage());
        header('Location: manage_users.php?status=error_general');
        exit;
    }
} else {
    header('Location: manage_users.php');
    exit;
}
?>