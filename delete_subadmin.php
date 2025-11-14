<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas Super Admin e Admin podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin'])) {
    header('Location: login.php');
    exit;
}
$role_logado = $_SESSION['role'];
$id_logado = $_SESSION['user_id'];
$id_admin_delete = $_GET['id'] ?? null;

if (!$id_admin_delete) {
    header('Location: manage_subadmins.php');
    exit;
}

// Não permitir que um admin tente apagar a si mesmo (deve ser feito pelo Super Admin ou outro Admin se houver)
if ($id_admin_delete == $id_logado) {
    header('Location: manage_subadmins.php?status=error_permission');
    exit;
}

try {
    // 1. Buscar dados do gerente a ser apagado e verificar permissão
    $stmt_check = $pdo->prepare("SELECT nome, role, manager_id FROM sub_administradores WHERE id = ?");
    $stmt_check->execute([$id_admin_delete]);
    $gerente_delete_data = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $nome_gerente = $gerente_delete_data['nome'] ?? 'ID ' . $id_admin_delete;
    $target_role = $gerente_delete_data['role'] ?? null;

    if (!$gerente_delete_data) {
        header('Location: manage_subadmins.php?status=error_not_found');
        exit;
    }

    // Regra de permissão para Admin: só pode apagar Sub-Admins sob sua gerência
    if ($role_logado == 'admin') {
        if ($target_role == 'super_adm' || $target_role == 'admin' || $gerente_delete_data['manager_id'] != $id_logado) {
            log_acao("Acesso negado (exclusão subadmin): Admin ID " . $id_logado . " tentou apagar gerente ID " . $id_admin_delete);
            header('Location: manage_subadmins.php?status=error_permission');
            exit;
        }
    }
    
    // --- Lógica de Reatribuição ---
    
    $pdo->beginTransaction();

    if ($target_role == 'admin' || $target_role == 'sub_adm') {
        
        // Encontra o manager de reatribuição:
        // Se o apagado era um Sub-Admin, reatribuir para seu Manager (que pode ser Super Admin ou Admin)
        // Se o apagado era um Admin, reatribuir para o Super Admin
        $new_manager_id = null;
        if ($target_role == 'sub_adm') {
            // Reatribui para o Manager superior
            $new_manager_id = $gerente_delete_data['manager_id'];
        } elseif ($target_role == 'admin') {
            // Reatribui para o Super Admin (Assumindo que o Super Admin tem ID conhecido ou é o único com essa role)
            $stmt_super_admin = $pdo->query("SELECT id FROM sub_administradores WHERE role = 'super_adm' LIMIT 1");
            $new_manager_id = $stmt_super_admin->fetchColumn();
        }

        if ($new_manager_id) {
            // 2. Reatribuir Sub-Admins e Operadores que tinham este gerente como manager
            
            // Reatribui Sub-Admins (se houver)
            $stmt_reassign_subadmins = $pdo->prepare("UPDATE sub_administradores SET manager_id = ? WHERE manager_id = ? AND role = 'sub_adm'");
            $stmt_reassign_subadmins->execute([$new_manager_id, $id_admin_delete]);
            
            // Reatribui Operadores (usuarios)
            $stmt_reassign_users = $pdo->prepare("UPDATE usuarios SET manager_id = ? WHERE manager_id = ?");
            $stmt_reassign_users->execute([$new_manager_id, $id_admin_delete]);
            
            // Atualiza os relatórios com o novo id_sub_adm
            $stmt_update_reports = $pdo->prepare("UPDATE relatorios SET id_sub_adm = ? WHERE id_sub_adm = ?");
            $stmt_update_reports->execute([$new_manager_id, $id_admin_delete]);

            log_acao("Gerente " . $nome_gerente . " (ID " . $id_admin_delete . ") apagado. Usuários/SubAdmins reatribuídos ao Gerente ID: " . $new_manager_id);
            
        } else {
             // Caso não haja para onde reatribuir (cenário improvável se Super Admin existir)
            $pdo->rollBack();
            header('Location: manage_subadmins.php?status=error_reassign');
            exit;
        }
    }

    // 3. Excluir o Gerente (Admin ou Sub-Admin)
    $stmt_delete_admin = $pdo->prepare("DELETE FROM sub_administradores WHERE id = ?");
    $stmt_delete_admin->execute([$id_admin_delete]);
    
    $pdo->commit();
    log_acao("Gerente " . $nome_gerente . " (ID " . $id_admin_delete . ") removido com sucesso.");

    header('Location: manage_subadmins.php?status=deleted');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    log_acao("Erro PDO ao apagar gerente ID " . $id_admin_delete . ": " . $e->getMessage());
    error_log("Erro de exclusão de gerente: " . $e->getMessage());
    header('Location: manage_subadmins.php?status=error_db_delete');
    exit;
}
?>