<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas operadores (usuario) podem editar suas transações.
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'usuario') {
    header('Location: login.php');
    exit;
}
$id_usuario_logado = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Dados obrigatórios
    $id_relatorio = $_POST['id_relatorio'] ?? null;
    $data_transacao = $_POST['data_transacao'] ?? null;
    $hora_transacao = $_POST['hora_transacao'] ?? null;

    // Novos Valores
    $valor_deposito = (float)($_POST['valor_deposito'] ?? 0);
    $valor_saque = (float)($_POST['valor_saque'] ?? 0);
    $valor_bau = (float)($_POST['valor_bau'] ?? 0);

    // Valores calculados pelo frontend (Usados para validação, o cálculo real é feito aqui)
    $lucro_bruto_form = (float)($_POST['lucro_bruto_calculado'] ?? 0);
    $comissao_usuario_form = (float)($_POST['comissao_usuario_calculada'] ?? 0);


    // Validação básica
    if (empty($id_relatorio) || empty($data_transacao) || empty($hora_transacao)) {
        log_acao("Erro: Campos de data/hora/ID ausentes na edição de transação.");
        header('Location: dashboard_usuario.php?status=error');
        exit;
    }
    
    $data_completa = $data_transacao . ' ' . $hora_transacao . ':00';
    
    // --- RE-CÁLCULO (Servidor) ---
    try {
        // 1. Buscar a taxa de comissão do usuário e o manager_id original
        $stmt_check = $pdo->prepare("SELECT u.id, u.manager_id, u.percentual_comissao 
                                     FROM relatorios r 
                                     JOIN usuarios u ON r.id_usuario = u.id
                                     WHERE r.id_relatorio = ? AND r.id_usuario = ?");
        $stmt_check->execute([$id_relatorio, $id_usuario_logado]);
        $data_original = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if (!$data_original) {
            log_acao("Erro de segurança: Tentativa de editar transação de ID inválido ou não pertencente ao usuário ID: " . $id_usuario_logado);
            header('Location: dashboard_usuario.php?status=error_security');
            exit;
        }

        $user_rate = (float)$data_original['percentual_comissao'] / 100;
        $manager_id = $data_original['manager_id'];
        
        // Lucro bruto (cálculo real)
        $lucro_bruto_real = ($valor_deposito + $valor_bau) - $valor_saque;

        // Se o lucro for negativo ou zero, as comissões são zero.
        if ($lucro_bruto_real <= 0) {
            $comissao_usuario_real = 0.00;
            $comissao_sub_adm_real = 0.00;
            $comissao_admin_real = 0.00;
        } else {
            // Calcular comissão do Usuário (Operador) - 40%
            $comissao_usuario_real = $lucro_bruto_real * $user_rate;
            
            // O restante do lucro
            $lucro_restante = $lucro_bruto_real - $comissao_usuario_real;
            
            // A comissão do Manager (Admin/Sub-Admin) é 10% do lucro restante
            $manager_rate = 0.10; 
            $comissao_sub_adm_real = $lucro_restante * $manager_rate;
            
            // O lucro do Super Admin é o restante (Lucro Restante - Comissão do Manager)
            $comissao_admin_real = $lucro_restante - $comissao_sub_adm_real;
        }
        
        // 2. Atualizar o relatório na tabela `relatorios`
        $stmt_update = $pdo->prepare("
            UPDATE relatorios SET
                valor_deposito = ?, 
                valor_saque = ?, 
                valor_bau = ?, 
                lucro_diario = ?, 
                comissao_usuario = ?, 
                comissao_sub_adm = ?, 
                comissao_admin = ?,
                data = ?
            WHERE id_relatorio = ?
        ");
        
        $stmt_update->execute([
            $valor_deposito,
            $valor_saque,
            $valor_bau,
            $lucro_bruto_real,
            $comissao_usuario_real,
            $comissao_sub_adm_real,
            $comissao_admin_real,
            $data_completa,
            $id_relatorio
        ]);

        // Registrar a ação
        log_acao("Transação ID " . $id_relatorio . " editada pelo usuário ID " . $id_usuario_logado . ". Novo Lucro Bruto: R$ " . number_format($lucro_bruto_real, 2, ',', '.'));

        // Redireciona com sucesso
        header('Location: dashboard_usuario.php?status=success_updated');
        exit;

    } catch (PDOException $e) {
        log_acao("Erro PDO ao editar transação ID " . $id_relatorio . ": " . $e->getMessage());
        header('Location: edit_report_entry.php?id=' . $id_relatorio . '&status=error_update');
        exit;
    }

} else {
    // Acesso direto, redireciona
    header('Location: dashboard_usuario.php');
    exit;
}
?>