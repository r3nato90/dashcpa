<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Incluído para log

// Apenas Gerentes (todos os níveis) podem apagar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'sub_adm', 'super_adm'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: saved_reports.php');
    exit;
}

$id_report_salvo = (int)$_GET['id'];
$id_logado = $_SESSION['id'];
$role = $_SESSION['role'];

try {
    // --- **** CORREÇÃO NA QUERY DE EXCLUSÃO **** ---
    $query = "DELETE FROM saved_reports WHERE id_report_salvo = ?";
    $params = [$id_report_salvo];

    // Se for sub-adm ou admin (NÃO Super Admin), verifica se ele é o dono
    if ($role == 'admin' || $role == 'sub_adm') {
        $query .= " AND id_salvo_por = ?";
        $params[] = $id_logado;
    }
    // Se for Super Admin, o DELETE não tem restrição de dono.

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    // --- **** FIM DA CORREÇÃO **** ---

    // Verifica se alguma linha foi realmente apagada (útil para logs)
    if ($stmt->rowCount() > 0) {
        log_action($pdo, 'REPORT_DELETE', "Relatório salvo (ID: $id_report_salvo) foi apagado.");
    } else {
        // Isso pode acontecer se um Admin tentou apagar um relatório de outro
        log_action($pdo, 'ERROR_DELETE', "Tentativa de apagar relatório salvo (ID: $id_report_salvo) falhou (Permissão ou inexistente).");
    }

    header('Location: saved_reports.php?status=deleted');
    exit;

} catch (PDOException $e) {
    log_action($pdo, 'ERROR_REPORT', "Falha ao apagar relatório salvo (ID: $id_report_salvo): " . $e->getMessage());
    echo "Erro ao apagar relatório: " . $e->getMessage();
}
?>