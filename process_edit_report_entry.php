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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_relatorio = $_POST['id_relatorio'];
    $deposito = $_POST['valor_deposito'];
    $saque = $_POST['valor_saque'];
    $bau = $_POST['valor_bau'];

    try {
        // **** MODIFICADO: Busca DENTRO da organização ****
        $stmt_check = $pdo->prepare("
            SELECT r.id_usuario, u.percentual_comissao AS user_comissao_pct, u.id_sub_adm
            FROM relatorios r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            WHERE r.id_relatorio = ? AND r.org_id = ?
        ");
        $stmt_check->execute([$id_relatorio, $org_id]);
        $report_data = $stmt_check->fetch();

        if (!$report_data) throw new Exception("Relatório não encontrado.");
        if ($role != 'super_adm' && $report_data['id_sub_adm'] != $id_logado) {
            throw new Exception("Permissão negada.");
        }

        $lucro = ($saque + $bau) - $deposito;
        $comissao_usuario = $lucro * ($report_data['user_comissao_pct'] / 100);
        $comissao_sub_adm = 0;
        if ($report_data['id_sub_adm'] != NULL) {
            $comissao_sub_adm = $lucro - $comissao_usuario; // Lógica "o que sobra"
        }

        // **** MODIFICADO: Atualiza DENTRO da organização ****
        $stmt_update = $pdo->prepare("
            UPDATE relatorios SET 
                valor_deposito = ?, valor_saque = ?, valor_bau = ?, 
                lucro_diario = ?, comissao_usuario = ?, comissao_sub_adm = ?
            WHERE id_relatorio = ? AND org_id = ?
        ");
        $stmt_update->execute([
            $deposito, $saque, $bau, $lucro, 
            $comissao_usuario, $comissao_sub_adm, 
            $id_relatorio, $org_id
        ]);

        log_action($pdo, 'REPORT_EDIT', "Relatório (ID: $id_relatorio) foi corrigido. Novo Lucro: $lucro.");
        header('Location: reports.php?status=report_updated');
        exit;
    } catch (Exception $e) {
        log_action($pdo, 'ERROR_REPORT_EDIT', "Falha (ID: $id_relatorio): " . $e->getMessage());
        header('Location: reports.php?status=error_update');
        exit;
    }
} else {
    header('Location: reports.php');
    exit;
}
?>