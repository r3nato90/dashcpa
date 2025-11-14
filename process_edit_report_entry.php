<?php
session_start();
include('config/db.php');
include('config/logger.php'); // Inclui o logger

// Apenas Gerentes (todos os níveis) podem processar
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_adm', 'admin', 'sub_adm'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$id_logado = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Coletar dados do formulário
    $id_relatorio = $_POST['id_relatorio'];
    $deposito = $_POST['valor_deposito'];
    $saque = $_POST['valor_saque'];
    $bau = $_POST['valor_bau'];

    try {
        // 2. Buscar o relatório e verificar permissão (NOVAMENTE, por segurança)
        $stmt_check = $pdo->prepare("
            SELECT r.id_usuario, u.id_sub_adm
            FROM relatorios r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            WHERE r.id_relatorio = ?
        ");
        $stmt_check->execute([$id_relatorio]);
        $report_data = $stmt_check->fetch();

        if (!$report_data) {
            throw new Exception("Relatório não encontrado.");
        }

        // Se não for Super Admin, verifica se o relatório pertence a ele
        if ($role != 'super_adm' && $report_data['id_sub_adm'] != $id_logado) {
            throw new Exception("Permissão negada para editar este relatório.");
        }

        // 3. Recalcular Lucro e Comissões (Regras fixas: Operador: 40%, Gerente: 10%, Administrador: 50%)
        $lucro = ($saque + $bau) - $deposito;
        
        // Comissão do Usuário (40% fixo do lucro bruto)
        $comissao_usuario = $lucro * 0.40;

        // Comissão do Gerente (Sub-Adm/Admin) (10% fixo do lucro bruto)
        $comissao_sub_adm = 0;
        if ($report_data['id_sub_adm'] != NULL) {
            $comissao_sub_adm = $lucro * 0.10;
        }

        // Comissão do Administrador (Super-Adm/Dono) (50% fixo do lucro bruto)
        $comissao_admin = $lucro * 0.50;

        // 4. Atualizar o banco de dados (ADICIONANDO comissao_admin)
        $stmt_update = $pdo->prepare("
            UPDATE relatorios 
            SET 
                valor_deposito = ?, 
                valor_saque = ?, 
                valor_bau = ?, 
                lucro_diario = ?, 
                comissao_usuario = ?, 
                comissao_sub_adm = ?,
                comissao_admin = ?
            WHERE 
                id_relatorio = ?
        ");
        $stmt_update->execute([
            $deposito, 
            $saque, 
            $bau, 
            $lucro, 
            $comissao_usuario, 
            $comissao_sub_adm, 
            $comissao_admin,
            $id_relatorio
        ]);

        // 5. Registrar no Log
        log_action($pdo, 'REPORT_EDIT', "Relatório (ID: $id_relatorio) foi corrigido. Novo Lucro: $lucro. Com. Admin: $comissao_admin.");

        // 6. Redirecionar de volta para a página de relatórios com sucesso
        header('Location: reports.php?status=report_updated');
        exit;

    } catch (Exception $e) {
        log_action($pdo, 'ERROR_REPORT_EDIT', "Falha ao corrigir relatório (ID: $id_relatorio): " . $e->getMessage());
        header('Location: reports.php?status=error_update');
        exit;
    }
} else {
    header('Location: reports.php');
    exit;
}
?>