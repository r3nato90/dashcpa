<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas Super Admin, Admin e Sub-Admin podem processar a edição
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_usuario = $_POST['id_usuario'] ?? null;
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comissao = (float)($_POST['percentual_comissao'] ?? 0);
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $manager_id = $_POST['manager_id'] ?? null;
    $redirect_url = 'edit_user.php?id=' . $id_usuario;

    // 1. Validação básica
    if (!$id_usuario || empty($nome) || empty($email) || $comissao == '' || $manager_id === null) {
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

    try {
        // 2. Verificação de permissão (Admin/Sub-Admin só pode editar seus usuários)
        $stmt_check = $pdo->prepare("SELECT manager_id FROM usuarios WHERE id = ? AND role = 'usuario'");
        $stmt_check->execute([$id_usuario]);
        $usuario_edit_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$usuario_edit_data) {
             // Usuário a ser editado não existe ou não é um 'usuario'
            header('Location: manage_users.php?status=error_not_found');
            exit;
        }

        // Se não for super_adm, verifica se é o manager do usuário
        if ($role_logado != 'super_adm' && $usuario_edit_data['manager_id'] != $id_logado) {
            log_acao("Acesso negado (edição): Usuário ID " . $id_logado . " tentou editar operador ID " . $id_usuario);
            header('Location: manage_users.php?status=error_permission');
            exit;
        }

        // 3. Verificar duplicidade de email (exceto o email atual do usuário)
        $stmt_email_check = $pdo->prepare("
            SELECT email FROM usuarios WHERE email = ? AND id != ?
            UNION ALL
            SELECT email FROM sub_administradores WHERE email = ?
        ");
        $stmt_email_check->execute([$email, $id_usuario, $email]);
        if ($stmt_email_check->fetch()) {
            header('Location: ' . $redirect_url . '&status=error_email_exists');
            exit;
        }
        
        // 4. Preparar a atualização do usuário
        $sql = "UPDATE usuarios SET nome = ?, email = ?, percentual_comissao = ?, manager_id = ?";
        $params = [$nome, $email, $comissao, $manager_id];
        
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql .= ", senha = ?";
            $params[] = $hashed_password;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id_usuario;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        log_acao("Operador ID " . $id_usuario . " (Nome: " . htmlspecialchars($nome) . ") editado por " . $_SESSION['username'] . " (" . $role_logado . ")");

        // Redireciona de volta para a lista com sucesso
        header('Location: manage_users.php?status=updated');
        exit;

    } catch (PDOException $e) {
        log_acao("Erro PDO ao editar operador ID " . $id_usuario . ": " . $e->getMessage());
        header('Location: ' . $redirect_url . '&status=error_db');
        exit;
    }
} else {
    header('Location: manage_users.php');
    exit;
}
?>