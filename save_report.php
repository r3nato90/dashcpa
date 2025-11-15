<?php
session_start();
include('config/db.php');
include('config/logger.php'); 

// **** VERIFICAÇÃO MULTI-TENANT ****
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'sub_adm', 'super_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];
// **** FIM DA VERIFICAÇÃO ****

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nome_relatorio']) && !empty($_POST['filtros_json'])) {
    
    $nome_relatorio = $_POST['nome_relatorio'];
    $filtros_json = $_POST['filtros_json'];
    $data_criacao = date('Y-m-d H:i:s'); 

    // **** MODIFICADO: Busca DENTRO da organização ****
    $stmt_user = $pdo->prepare("SELECT nome FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ?");
    $stmt_user->execute([$id_logado, $org_id]);
    $user = $stmt_user->fetch();
    $nome_salvo_por = ($user) ? $user['nome'] : 'Desconhecido';

    try {
        // **** MODIFICADO: Insere com org_id ****
        $stmt_insert = $pdo->prepare("
            INSERT INTO saved_reports (org_id, nome_relatorio, id_salvo_por, nome_salvo_por, filtros, data_criacao)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt_insert->execute([$org_id, $nome_relatorio, $id_logado, $nome_salvo_por, $filtros_json, $data_criacao]);
        
        log_action($pdo, 'REPORT_SAVE', "Relatório salvo: {$nome_relatorio}.");
        header('Location: saved_reports.php?status=saved');
        exit;
    } catch (PDOException $e) {
        log_action($pdo, 'ERROR_REPORT', "Falha ao salvar: " . $e->getMessage());
        echo "Erro: " . $e->getMessage();
    }
} else {
    header('Location: reports.php?status=error');
    exit;
}
?>