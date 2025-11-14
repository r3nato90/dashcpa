<?php
session_start(); // Inicia a sessão para o logger
include('config/db.php');
include('config/logger.php'); // Inclui o logger

// Verifica se o formulário foi enviado E se o usuario_id não está vazio
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['usuario_id'])) {
    
    // Recebendo dados do formulário
    $usuario_id = $_POST['usuario_id'];
    $deposito = $_POST['deposito'];
    $saque = $_POST['saque'];
    $bau = $_POST['bau'];

    // --- Lógica de Data ---
    $data_atual = date('Y-m-d H:i:s'); // Padrão: Agora
    $hoje = date('Y-m-d');
    $log_tipo_relatorio = "Padrão"; // Para o Log

    if (!empty($_POST['data_relatorio'])) {
        $data_selecionada = $_POST['data_relatorio'];
        // Mantém a hora atual se a data for hoje, ou define 12:00 para datas passadas
        $data_atual = $data_selecionada . ' ' . ($data_selecionada == $hoje ? date('H:i:s') : '12:00:00'); 
        $log_tipo_relatorio = ($data_selecionada < $hoje) ? "Retroativo ($data_selecionada)" : "Data Manual ($data_selecionada)";
    }
    // --- FIM DA LÓGICA DE DATA ---


    // 1. Calcular o lucro bruto
    $lucro = ($saque + $bau) - $deposito;

    // 2. Buscar dados do usuário (precisamos do id_sub_adm para o gerente)
    $stmt_user = $pdo->prepare("SELECT nome, id_sub_adm FROM usuarios WHERE id_usuario = ?"); 
    $stmt_user->execute([$usuario_id]);
    $user = $stmt_user->fetch();

    if ($user) {
        
        // --- **** NOVA LÓGICA DE CÁLCULO (HIERARQUIA 3 NÍVEIS: 40% OPERADOR, 10% GERENTE, 50% ADMINISTRADOR) **** ---
        
        // 3. Comissão do Operador (40% fixo do lucro bruto)
        // Nota: Se houver despesas, a lógica de dedução DEVE ser feita no dashboard (JS) ou no relatório (daily_control.php) para refletir o lucro líquido real. Aqui calculamos o BRUTO.
        $comissao_usuario = $lucro * 0.40;
        
        // 4. Comissão do Gerente (Sub-Adm/Admin) (10% fixo do lucro bruto)
        // Só é paga se o operador tiver um gerente vinculado
        $comissao_sub_adm = 0; 
        if ($user['id_sub_adm'] != NULL) {
            $comissao_sub_adm = $lucro * 0.10;
        }

        // 5. Comissão do Administrador (Super-Adm/Dono) (50% fixo do lucro bruto)
        // Esta comissão é sempre calculada
        $comissao_admin = $lucro * 0.50; 
        
        // --- **** FIM DA NOVA LÓGICA **** ---


        // 6. Inserir as transações e o relatório no banco de dados
        try {
            $pdo->beginTransaction();

            // Insere transações com a data processada
            $stmt_dep = $pdo->prepare("INSERT INTO transacoes (id_usuario, tipo_transacao, valor, data) VALUES (?, 'deposito', ?, ?)");
            $stmt_dep->execute([$usuario_id, $deposito, $data_atual]);
            $stmt_saq = $pdo->prepare("INSERT INTO transacoes (id_usuario, tipo_transacao, valor, data) VALUES (?, 'saque', ?, ?)");
            $stmt_saq->execute([$usuario_id, $saque, $data_atual]);
            $stmt_bau = $pdo->prepare("INSERT INTO transacoes (id_usuario, tipo_transacao, valor, data) VALUES (?, 'bau', ?, ?)");
            $stmt_bau->execute([$usuario_id, $bau, $data_atual]);

            // Atualiza saldo (se relevante, usamos o lucro bruto)
            $stmt_saldo = $pdo->prepare("UPDATE usuarios SET saldo = saldo + ? WHERE id_usuario = ?");
            $stmt_saldo->execute([$lucro, $usuario_id]);

            // Cria o relatório com a data processada (ADICIONANDO comissao_admin)
            $stmt_relatorio = $pdo->prepare("
                INSERT INTO relatorios 
                (id_usuario, lucro_diario, comissao_usuario, comissao_sub_adm, comissao_admin, valor_deposito, valor_saque, valor_bau, data) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_relatorio->execute([$usuario_id, $lucro, $comissao_usuario, $comissao_sub_adm, $comissao_admin, $deposito, $saque, $bau, $data_atual]);

            $pdo->commit();

            // Log de sucesso (ADICIONANDO Com. Admin)
            log_action($pdo, 'RELATORIO_ENVIO', "Relatório ($log_tipo_relatorio) enviado para '{$user['nome']}' (ID: $usuario_id). Lucro: $lucro. Com. User: $comissao_usuario. Com. Gerente: $comissao_sub_adm. Com. Admin: $comissao_admin.");

            // 7. Redirecionar de volta
            $redirect_url = 'index.php'; // Fallback
            if (isset($_SESSION['role'])) {
                if ($_SESSION['role'] == 'super_adm') $redirect_url = 'dashboard_superadmin.php';
                elseif ($role == 'admin') $redirect_url = 'dashboard_admin.php';
                elseif ($role == 'sub_adm') $redirect_url = 'dashboard_subadmin.php';
                elseif ($role == 'usuario') $redirect_url = 'dashboard_usuario.php';
            }
            header('Location: ' . $redirect_url . '?status=success');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            log_action($pdo, 'ERROR_TRANSACAO', "Falha ao processar transação para ID $usuario_id: " . $e->getMessage());
            echo "Erro ao registrar transação: " . $e->getMessage();
        }

    } else {
        log_action($pdo, 'ERROR_TRANSACAO', "Tentativa de enviar relatório para ID de usuário inválido: $usuario_id.");
        echo "Erro: Usuário não encontrado!";
    }
} else {
    // Log de erro (dados inválidos ou ID de usuário vazio)
    $received_user_id = $_POST['usuario_id'] ?? 'N/A';
    log_action($pdo, 'ERROR_TRANSACAO', "Tentativa de envio de relatório falhou. ID de usuário não fornecido ou vazio (Recebido: $received_user_id).");
    
    // Redireciona de volta para o painel com uma mensagem de erro
    $redirect_url = 'index.php';
    if (isset($_SESSION['role'])) {
        $role = $_SESSION['role'];
        if ($role == 'super_adm') $redirect_url = 'dashboard_superadmin.php';
        elseif ($role == 'admin') $redirect_url = 'dashboard_admin.php';
        elseif ($role == 'sub_adm') $redirect_url = 'dashboard_subadmin.php';
        elseif ($role == 'usuario') $redirect_url = 'dashboard_usuario.php';
    }
    header('Location: ' . $redirect_url . '?status=error_invalid_input'); 
    exit;
}
?>