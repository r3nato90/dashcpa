<?php
session_start();
include('config/db.php');
include('config/logger.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];
$org_id = $_SESSION['org_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_relatorio = $_POST['id_relatorio'];
    $deposito = $_POST['valor_deposito'];
    $saque = $_POST['valor_saque'];
    $bau = $_POST['valor_bau'];

    try {
        // --- **** INÍCIO DA LÓGICA CORRIGIDA (SOBRA TOTAL PARA N1) **** ---
        
        // 1. Busca o relatório e a hierarquia
        // (Não precisamos mais do pct_admin (N1) pois ele é a sobra)
        $stmt_check = $pdo->prepare("
            SELECT 
                r.id_usuario,
                u.id_sub_adm,
                u.percentual_comissao AS pct_usuario, 
                s.percentual_comissao AS pct_sub_adm,
                s.parent_admin_id
            FROM relatorios r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            LEFT JOIN sub_administradores s ON u.id_sub_adm = s.id_sub_adm AND s.org_id = u.org_id
            WHERE r.id_relatorio = ? AND r.org_id = ?
        ");
        $stmt_check->execute([$id_relatorio, $org_id]);
        $report_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$report_data) throw new Exception("Relatório não encontrado.");

        // 2. Verifica Permissão (a lógica anterior de permissão estava correta)
        if ($role == 'admin') {
            $stmt_check_permission = $pdo->prepare("
                SELECT 1 FROM sub_administradores 
                WHERE id_sub_adm = ? AND (parent_admin_id = ? OR id_sub_adm = ?)
            ");
            $stmt_check_permission->execute([$report_data['id_sub_adm'], $id_logado, $id_logado]);
            if (!$stmt_check_permission->fetch()) {
                 if($report_data['id_sub_adm'] != $id_logado) {
                    throw new Exception("Permissão negada (Admin).");
                 }
            }
        } elseif ($role == 'sub_adm') {
            if ($report_data['id_sub_adm'] != $id_logado) {
                throw new Exception("Permissão negada (Sub-Adm).");
            }
        }
        // Super-Admin (Dono) pode editar

        $lucro_total = ($saque + $bau) - $deposito;

        // 3. Recalcula as comissões (Sobra Total)
        $comissao_usuario_pct = $report_data['pct_usuario'] ?? 0;
        $comissao_usuario = $lucro_total * ($comissao_usuario_pct / 100);

        $comissao_sub_adm_pct = $report_data['pct_sub_adm'] ?? 0;
        $comissao_sub_adm = $lucro_total * ($comissao_sub_adm_pct / 100);

        $comissao_admin = $lucro_total - $comissao_usuario - $comissao_sub_adm;
        
        // --- **** FIM DA CORREÇÃO **** ---

        // Atualiza o relatório com os 3 níveis de comissão
        $stmt_update = $pdo->prepare("
            UPDATE relatorios SET 
                valor_deposito = ?, valor_saque = ?, valor_bau = ?, 
                lucro_diario = ?, comissao_usuario = ?, comissao_sub_adm = ?, comissao_admin = ?
            WHERE id_relatorio = ? AND org_id = ?
        ");
        $stmt_update->execute([
            $deposito, $saque, $bau, $lucro_total, 
            $comissao_usuario, $comissao_sub_adm, $comissao_admin,
            $id_relatorio, $org_id
        ]);

        log_action($pdo, 'REPORT_EDIT', "Relatório (ID: $id_relatorio) foi corrigido. Novo Lucro: $lucro_total.");
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