<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas Gerentes (Super e Sub) podem salvar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}
$id_logado = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $report_name = trim($_POST['report_name'] ?? '');
    $date_start = $_POST['date_start'] ?? null;
    $date_end = $_POST['date_end'] ?? null;
    $admin_id = $_POST['admin_id'] ?? null;
    $user_ids = $_POST['user_ids'] ?? [];

    if (empty($report_name) || empty($date_start) || empty($date_end)) {
        header('Location: reports.php?status=error_save_fields');
        exit;
    }
    
    // Converte arrays de IDs para JSON para armazenamento
    $user_ids_json = json_encode($user_ids);
    
    try {
        // Insere o novo filtro de relatório salvo
        $stmt_insert = $pdo->prepare("
            INSERT INTO relatorios_salvos (id_admin, nome, date_start, date_end, admin_id, user_ids)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt_insert->execute([
            $id_logado,
            $report_name,
            $date_start,
            $date_end,
            $admin_id,
            $user_ids_json
        ]);

        log_acao("Filtro de relatório salvo por " . $_SESSION['username'] . ": " . htmlspecialchars($report_name));

        // Redireciona com sucesso
        header('Location: reports.php?status=saved');
        exit;

    } catch (PDOException $e) {
        log_acao("Erro PDO ao salvar relatório: " . $e->getMessage());
        header('Location: reports.php?status=error_db_save');
        exit;
    }

} else {
    // Acesso direto, redireciona
    header('Location: reports.php');
    exit;
}
?>