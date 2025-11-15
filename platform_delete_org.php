<?php
session_start();
include('config/db.php');
include('config/logger.php');

// 1. Segurança: Apenas Platform Owner pode excluir
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'platform_owner') {
    header('Location: login.php');
    exit;
}

// 2. Validar o ID recebido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: platform_manage_orgs.php?status=error_no_id');
    exit;
}
$org_id_para_apagar = (int)$_GET['id'];
$owner_org_id = $_SESSION['org_id']; // O ID da organização do próprio dono

// 3. Trava de Segurança Crítica:
// Impede que o Dono da Plataforma apague a si mesmo (geralmente org_id = 1)
if ($org_id_para_apagar == $owner_org_id) {
    log_action($pdo, 'ERROR_DELETE_ORG', "Falha: Tentativa de apagar a própria organização (ID: $org_id_para_apagar). Ação bloqueada.");
    header('Location: platform_manage_orgs.php?status=error_self_delete');
    exit;
}

try {
    // 4. Busca o nome para o Log (antes de apagar)
    $stmt_name = $pdo->prepare("SELECT org_name FROM organizations WHERE org_id = ?");
    $stmt_name->execute([$org_id_para_apagar]);
    $org_name = $stmt_name->fetchColumn() ?? 'ID ' . $org_id_para_apagar;

    // 5. Executa a Exclusão
    // O 'ON DELETE CASCADE' no banco de dados fará o resto do trabalho.
    $stmt_delete = $pdo->prepare("DELETE FROM organizations WHERE org_id = ?");
    $stmt_delete->execute([$org_id_para_apagar]);

    log_action($pdo, 'ORG_DELETE', "Organização '{$org_name}' (ID: $org_id_para_apagar) foi permanentemente apagada. Todos os dados associados (usuários, relatórios, etc.) foram excluídos.");
    header('Location: platform_manage_orgs.php?status=org_deleted');
    exit;

} catch (PDOException $e) {
    log_action($pdo, 'ERROR_DELETE_ORG', "Falha ao apagar organização (ID: $org_id_para_apagar): " . $e->getMessage());
    header('Location: platform_manage_orgs.php?status=error_delete_failed');
    exit;
}
?>