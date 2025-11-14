<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Inclui o logger

// Apenas Gerentes (todos os níveis) podem apagar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

// 1. Validar ID do relatório
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reports.php?status=error_invalid_id');
    exit;
}
$id_relatorio = (int)$_GET['id'];

try {
    // 2. Buscar o relatório e verificar permissão
    $stmt_check = $pdo->prepare("
        SELECT r.id_usuario, u.nome AS nome_usuario, u.id_sub_adm 
        FROM relatorios r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.id_relatorio = ?
    ");
    $stmt_check->execute([$id_relatorio]);
    $report_data = $stmt_check->fetch();

    if (!$report_data) {
        throw new Exception("Relatório (ID: $id_relatorio) não encontrado.");
    }

    // Se não for Super Admin, verifica se o relatório pertence a ele
    if ($role != 'super_adm' && $report_data['id_sub_adm'] != $id_logado) {
        throw new Exception("Permissão negada para apagar este relatório (ID: $id_relatorio).");
    }

    // 3. Apagar o registro do relatório
    $stmt_delete = $pdo->prepare("DELETE FROM relatorios WHERE id_relatorio = ?");
    $stmt_delete->execute([$id_relatorio]);

    // 4. Registrar no Log
    $nome_usuario_relatorio = $report_data['nome_usuario'] ?? 'ID ' . $report_data['id_usuario'];
    log_action($pdo, 'REPORT_ENTRY_DELETE', "Linha de relatório (ID: $id_relatorio) do usuário '{$nome_usuario_relatorio}' foi apagada.");

    // 5. Redirecionar de volta para a página de relatórios com sucesso
    header('Location: reports.php?status=report_deleted');
    exit;

} catch (Exception $e) {
    log_action($pdo, 'ERROR_REPORT_DELETE', "Falha ao apagar linha de relatório (ID: $id_relatorio): " . $e->getMessage());
    header('Location: reports.php?status=error_delete');
    exit;
}
?>