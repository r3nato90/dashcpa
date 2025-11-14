<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas Super Admin e Admin podem processar a edição
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_admin = $_POST['id_admin'] ?? null;
    $current_role = $_POST['current_role'] ?? null; // Role do gerente a ser editado
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comissao = (float)($_POST['comissao'] ?? 0);
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $manager_id = $_POST['manager_id'] ?? null;
    $redirect_url = 'edit_subadmin.php?id=' . $id_admin;

    // 1. Validação básica
    if (!$id_admin || empty($nome) || empty($email) || $comissao == '' || empty($current_role)) {
        header('Location: ' . $redirect_url . '&status=error_fields');
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         header('Location: ' . $redirect_url . '&status=error_email_format');
        exit;
    }
    if ($comissao < 0 || $comissao > 100) {
        header('Location: ' . $redirect_url . '&status=error_comissao_range');
        exit;
    }
    if (!empty($new_password) && $new_password !== $confirm_password) {
        header('Location: ' . $redirect_url . '&status=error_password_match');
        exit;
    }
    if ($current_role === 'sub_adm' && $manager_id === null) {
        // Sub-admin deve ter um manager definido
        header('Location: ' . $redirect_url . '&status=error_manager_required');
        exit;
    }

    try {
        // 2. Verificação de permissão (Admin só pode editar seus Sub-Admins)
        if ($role_logado == 'admin') {
            if ($current_role == 'super_adm' || $gerente['manager_id'] != $id_logado) {
                 // Busca o manager_id do gerente que está sendo editado
                $stmt_check_manager = $pdo->prepare("SELECT manager_id FROM sub_administradores WHERE id = ?");
                $stmt_check_manager->execute([$id_admin]);
                $target_manager_id = $stmt_check_manager->fetchColumn();

                if ($target_manager_id != $id_logado) {
                    log_acao("Acesso negado (edição subadmin): Admin ID " . $id_logado . " tentou editar gerente ID " . $id_admin);
                    header('Location: manage_subadmins.php?status=error_permission');
                    exit;
                }
            }
            // Garante que o Admin não pode mudar o manager_id do Sub-Admin para outro Admin/Super Admin
            if ($current_role == 'sub_adm' && $manager_id != $id_logado) {
                 header('Location: ' . $redirect_url . '&status=error_permission_manager');
                 exit;
            }
        }
         // Super Admin pode editar todos, incluindo Super Admin (com exceção de si mesmo, evitado no frontend)


        // 3. Verificar duplicidade de email (exceto o email atual do gerente)
        $stmt_email_check = $pdo->prepare("
            SELECT email FROM sub_administradores WHERE email = ? AND id != ?
            UNION ALL
            SELECT email FROM usuarios WHERE email = ?
        ");
        $stmt_email_check->execute([$email, $id_admin, $email]);
        if ($stmt_email_check->fetch()) {
            header('Location: ' . $redirect_url . '&status=error_email_exists');
            exit;
        }
        
        // 4. Preparar a atualização
        $sql = "UPDATE sub_administradores SET nome = ?, email = ?, comissao = ?, manager_id = ?";
        $params = [$nome, $email, $comissao, $manager_id];
        
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql .= ", senha = ?";
            $params[] = $hashed_password;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id_admin;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_acao("Gerente ID " . $id_admin . " (Role: " . $current_role . ") editado por " . $_SESSION['username'] . " (" . $role_logado . ")");

        // Redireciona de volta para a lista com sucesso
        header('Location: manage_subadmins.php?status=updated');
        exit;

    } catch (PDOException $e) {
        log_acao("Erro PDO ao editar gerente ID " . $id_admin . ": " . $e->getMessage());
        header('Location: ' . $redirect_url . '&status=error_db');
        exit;
    }
} else {
    header('Location: manage_subadmins.php');
    exit;
}
?>