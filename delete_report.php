<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas Gerentes (Super e Sub) podem acessar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$id_logado = $_SESSION['user_id'];
$id_report_delete = $_GET['id'] ?? null;

if (!$id_report_delete) {
    header('Location: saved_reports.php');
    exit;
}

try {
    // 1. Verifica se o relatório pertence ao usuário logado (segurança)
    $stmt_check = $pdo->prepare("SELECT nome FROM relatorios_salvos WHERE id = ? AND id_admin = ?");
    $stmt_check->execute([$id_report_delete, $id_logado]);
    $report_data = $stmt_check->fetch(PDO::FETCH_ASSOC);
    $nome_report = $report_data['nome'] ?? 'ID ' . $id_report_delete;

    if ($report_data) {
        // 2. Excluir o relatório salvo
        $stmt_delete = $pdo->prepare("DELETE FROM relatorios_salvos WHERE id = ?");
        $stmt_delete->execute([$id_report_delete]);
        
        log_acao("Relatório salvo '" . $nome_report . "' (ID " . $id_report_delete . ") apagado por usuário ID: " . $id_logado);
        header('Location: saved_reports.php?status=deleted');
        exit;
    } else {
        log_acao("Erro de segurança: Tentativa de apagar relatório salvo de ID inválido ou não pertencente ao usuário ID: " . $id_logado);
        header('Location: saved_reports.php?status=error_delete');
        exit;
    }
} catch (PDOException $e) {
    log_acao("Erro PDO ao apagar relatório salvo ID " . $id_report_delete . ": " . $e->getMessage());
    header('Location: saved_reports.php?status=error_delete');
    exit;
}
?>