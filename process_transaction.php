<?php
session_start();
include('config/db.php');
date_default_timezone_set('America/Sao_Paulo'); 
include('config/logger.php');

// Verificação de segurança: Apenas operadores (usuario) podem processar transações
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'usuario') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Dados obrigatórios
    $id_usuario = $_POST['id_usuario'] ?? null;
    $data_transacao = $_POST['data_transacao'] ?? null;
    $hora_transacao = $_POST['hora_transacao'] ?? null;

    // Valores (já convertidos do frontend, mas verificaremos a integridade)
    $valor_deposito = (float)($_POST['valor_deposito'] ?? 0);
    $valor_saque = (float)($_POST['valor_saque'] ?? 0);
    $valor_bau = (float)($_POST['valor_bau'] ?? 0);

    // Valores calculados (recalculados no backend para segurança)
    $lucro_bruto_form = (float)($_POST['lucro_bruto_calculado'] ?? 0);
    $comissao_usuario_form = (float)($_POST['comissao_usuario_calculada'] ?? 0);

    // Validação básica
    if (empty($id_usuario) || empty($data_transacao) || empty($hora_transacao)) {
        log_acao("Erro: Campos de data/hora/usuário ausentes no registro de transação.");
        header('Location: dashboard_usuario.php?status=error');
        exit;
    }
    
    // Combina data e hora
    $data_completa = $data_transacao . ' ' . $hora_transacao . ':00';
    
    // --- LÓGICA PRINCIPAL DE CÁLCULO (Servidor) ---
    try {
        // 1. Obter o usuário e seu manager
        $stmt_user = $pdo->prepare("SELECT id, manager_id, percentual_comissao FROM usuarios WHERE id = ? AND role = 'usuario'");
        $stmt_user->execute([$id_usuario]);
        $user_data = $stmt_user->fetch();

        if (!$user_data) {
            log_acao("Erro de segurança: Tentativa de registrar transação para ID de usuário inválido: " . $id_usuario);
            header('Location: dashboard_usuario.php?status=error');
            exit;
        }

        $user_rate = (float)$user_data['percentual_comissao'] / 100;
        $manager_id = $user_data['manager_id'];
        
        // 2. Obter a comissão do Manager (Admin ou Sub-Admin)
        $manager_rate = 0.10; // Taxa padrão para o Manager (10%)
        $admin_rate = 0.50; // Taxa padrão para o Super Admin (50%)
        
        // Lucro bruto (cálculo de integridade)
        $lucro_bruto_real = ($valor_deposito + $valor_bau) - $valor_saque;

        // Se o lucro for negativo ou zero, as comissões são zero.
        if ($lucro_bruto_real <= 0) {
            $comissao_usuario_real = 0.00;
            $comissao_sub_adm_real = 0.00;
            $comissao_admin_real = 0.00;
        } else {
            // Calcular comissão do Usuário (Operador) - 40%
            $comissao_usuario_real = $lucro_bruto_real * $user_rate;
            
            // O restante do lucro é Lucro Bruto - Comissão do Operador
            $lucro_restante = $lucro_bruto_real - $comissao_usuario_real;
            
            // A comissão do Manager (Admin/Sub-Admin) é 10% do lucro restante
            $comissao_sub_adm_real = $lucro_restante * $manager_rate;
            
            // O lucro do Super Admin é o restante (Lucro Restante - Comissão do Manager)
            $comissao_admin_real = $lucro_restante - $comissao_sub_adm_real;
            
            // Verifica se a taxa do Super Admin é realmente 50% do lucro restante (para fins de integridade)
            // if (abs($comissao_admin_real - ($lucro_restante * $admin_rate)) > 0.01) {
            //    // Logar alerta de discrepância ou ajustar a lógica.
            // }

        }

        // 3. Inserir o relatório na tabela `relatorios`
        $stmt_insert = $pdo->prepare("
            INSERT INTO relatorios (
                id_usuario, 
                id_sub_adm, 
                valor_deposito, 
                valor_saque, 
                valor_bau, 
                lucro_diario, 
                comissao_usuario, 
                comissao_sub_adm, 
                comissao_admin,
                data
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");
        
        $stmt_insert->execute([
            $id_usuario,
            $manager_id, // manager_id do usuário, que é o ID do Admin/Sub-Admin responsável
            $valor_deposito,
            $valor_saque,
            $valor_bau,
            $lucro_bruto_real,
            $comissao_usuario_real,
            $comissao_sub_adm_real,
            $comissao_admin_real,
            $data_completa
        ]);

        // Registrar a ação
        log_acao("Transação registrada para o usuário ID " . $id_usuario . ". Lucro Bruto: R$ " . number_format($lucro_bruto_real, 2, ',', '.'));

        // Redireciona com sucesso
        header('Location: dashboard_usuario.php?status=success');
        exit;

    } catch (PDOException $e) {
        log_acao("Erro PDO ao registrar transação: " . $e->getMessage());
        header('Location: dashboard_usuario.php?status=error');
        exit;
    }

} else {
    // Acesso direto, redireciona
    header('Location: dashboard_usuario.php');
    exit;
}
?>