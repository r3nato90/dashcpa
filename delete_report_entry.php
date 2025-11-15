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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reports.php');
    exit;
}
$id_relatorio = (int)$_GET['id'];

try {
    // **** MODIFICADO: Busca DENTRO da organização ****
    $stmt_check = $pdo->prepare("
        SELECT r.id_usuario, u.nome AS nome_usuario, u.id_sub_adm 
        FROM relatorios r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.id_relatorio = ? AND r.org_id = ?
    ");
    $stmt_check->execute([$id_relatorio, $org_id]);
    $report_data = $stmt_check->fetch();

    if (!$report_data) throw new Exception("Relatório (ID: $id_relatorio) não encontrado.");
    if ($role != 'super_adm' && $report_data['id_sub_adm'] != $id_logado) {
        throw new Exception("Permissão negada (ID: $id_relatorio).");
    }

    // **** MODIFICADO: Apaga DENTRO da organização ****
    $stmt_delete = $pdo->prepare("DELETE FROM relatorios WHERE id_relatorio = ? AND org_id = ?");
    $stmt_delete->execute([$id_relatorio, $org_id]);

    $nome_usuario_relatorio = $report_data['nome_usuario'] ?? 'ID ' . $report_data['id_usuario'];
    log_action($pdo, 'REPORT_ENTRY_DELETE', "Linha de relatório (ID: $id_relatorio) do '{$nome_usuario_relatorio}' foi apagada.");
    header('Location: reports.php?status=report_deleted');
    exit;
} catch (Exception $e) {
    log_action($pdo, 'ERROR_REPORT_DELETE', "Falha ao apagar (ID: $id_relatorio): " . $e->getMessage());
    header('Location: reports.php?status=error_delete');
    exit;
}
?>