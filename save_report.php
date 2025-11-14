<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Incluído

// Apenas Admin, Sub-Adm e Super-Adm podem salvar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'sub_adm', 'super_adm'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nome_relatorio']) && !empty($_POST['filtros_json'])) {
    
    $id_logado = $_SESSION['id'];
    $nome_relatorio = $_POST['nome_relatorio'];
    $filtros_json = $_POST['filtros_json'];
    $data_criacao = date('Y-m-d H:i:s'); // Fuso horário

    // Buscar o nome do admin/sub-admin que está salvando
    $stmt_user = $pdo->prepare("SELECT nome FROM sub_administradores WHERE id_sub_adm = ?");
    $stmt_user->execute([$id_logado]);
    $user = $stmt_user->fetch();
    $nome_salvo_por = ($user) ? $user['nome'] : 'Desconhecido';

    try {
        $stmt_insert = $pdo->prepare("
            INSERT INTO saved_reports (nome_relatorio, id_salvo_por, nome_salvo_por, filtros, data_criacao)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt_insert->execute([$nome_relatorio, $id_logado, $nome_salvo_por, $filtros_json, $data_criacao]);
        
        log_action($pdo, 'REPORT_SAVE', "Relatório salvo com o nome: {$nome_relatorio}.");
        header('Location: saved_reports.php?status=saved');
        exit;

    } catch (PDOException $e) {
        log_action($pdo, 'ERROR_REPORT', "Falha ao salvar relatório: " . $e->getMessage());
        echo "Erro ao salvar relatório: " . $e->getMessage();
    }

} else {
    // Se faltar dados, volta para a página de relatórios
    header('Location: reports.php?status=error');
    exit;
}
?>