<?php
session_start(); 
include('config/db.php');
include('config/logger.php'); 

if (!isset($_SESSION['id']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id']; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['usuario_id'])) {
    
    $usuario_id = $_POST['usuario_id'];
    $deposito = $_POST['deposito'];
    $saque = $_POST['saque'];
    $bau = $_POST['bau'];
    $data_atual = date('Y-m-d H:i:s');
    $hoje = date('Y-m-d');
    $log_tipo_relatorio = "Padrão"; 

    if (!empty($_POST['data_relatorio'])) {
        $data_selecionada = $_POST['data_relatorio'];
        $data_atual = $data_selecionada . ' ' . date('H:i:s'); 
        $log_tipo_relatorio = ($data_selecionada < $hoje) ? "Retroativo ($data_selecionada)" : "Data Manual ($data_selecionada)";
    }

    $lucro_total = ($saque + $bau) - $deposito;

    // --- **** INÍCIO DA LÓGICA CORRIGIDA (SOBRA TOTAL PARA N1) **** ---
    
    $stmt_user = $pdo->prepare("
        SELECT 
            u.nome, 
            u.percentual_comissao AS pct_usuario, 
            u.id_sub_adm,
            s.percentual_comissao AS pct_sub_adm,
            s.parent_admin_id
        FROM usuarios u
        LEFT JOIN sub_administradores s ON u.id_sub_adm = s.id_sub_adm AND s.org_id = u.org_id
        WHERE u.id_usuario = ? AND u.org_id = ?
    ");
    $stmt_user->execute([$usuario_id, $org_id]);
    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        $nome_usuario = $user_data['nome'];

        // 1. Cálculo Comissão Operador (N3) - % do Lucro Total
        $comissao_usuario_pct = $user_data['pct_usuario'] ?? 0;
        $comissao_usuario = $lucro_total * ($comissao_usuario_pct / 100); // Ex: 100 * 0.30 = 30

        // 2. Cálculo Comissão Sub-Admin (N2) - % do Lucro Total
        $comissao_sub_adm_pct = $user_data['pct_sub_adm'] ?? 0;
        $comissao_sub_adm = $lucro_total * ($comissao_sub_adm_pct / 100); // Ex: 100 * 0.10 = 10

        // 3. Cálculo Comissão Admin (N1) - A SOBRA
        // (A % do Admin N1 no cadastro é ignorada, como solicitado)
        $comissao_admin = $lucro_total - $comissao_usuario - $comissao_sub_adm; // Ex: 100 - 30 - 10 = 60
        
        // (Lucro do Super-Admin = 0)

        // --- **** FIM DA LÓGICA CORRIGIDA **** ---

        try {
            $pdo->beginTransaction();

            $stmt_dep = $pdo->prepare("INSERT INTO transacoes (org_id, id_usuario, tipo_transacao, valor, data) VALUES (?, ?, 'deposito', ?, ?)");
            $stmt_dep->execute([$org_id, $usuario_id, $deposito, $data_atual]);
            
            $stmt_saq = $pdo->prepare("INSERT INTO transacoes (org_id, id_usuario, tipo_transacao, valor, data) VALUES (?, ?, 'saque', ?, ?)");
            $stmt_saq->execute([$org_id, $usuario_id, $saque, $data_atual]);

            $stmt_bau = $pdo->prepare("INSERT INTO transacoes (org_id, id_usuario, tipo_transacao, valor, data) VALUES (?, ?, 'bau', ?, ?)");
            $stmt_bau->execute([$org_id, $usuario_id, $bau, $data_atual]);

            $stmt_saldo = $pdo->prepare("UPDATE usuarios SET saldo = saldo + ? WHERE id_usuario = ? AND org_id = ?");
            $stmt_saldo->execute([$lucro_total, $usuario_id, $org_id]);

            // Salva os 3 níveis de comissão no relatório
            $stmt_relatorio = $pdo->prepare("
                INSERT INTO relatorios 
                (org_id, id_usuario, lucro_diario, comissao_usuario, comissao_sub_adm, comissao_admin, valor_deposito, valor_saque, valor_bau, data) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_relatorio->execute([$org_id, $usuario_id, $lucro_total, $comissao_usuario, $comissao_sub_adm, $comissao_admin, $deposito, $saque, $bau, $data_atual]);

            $pdo->commit();

            log_action($pdo, 'RELATORIO_ENVIO', "Relatório ($log_tipo_relatorio) enviado para '{$nome_usuario}' (ID: $usuario_id). Lucro: $lucro_total.");

            // Redirecionamento
            $redirect_url = 'index.php'; 
            if (isset($_SESSION['role'])) {
                if ($_SESSION['role'] == 'super_adm') $redirect_url = 'dashboard_superadmin.php';
                elseif ($_SESSION['role'] == 'admin') $redirect_url = 'dashboard_admin.php';
                elseif ($_SESSION['role'] == 'sub_adm') $redirect_url = 'dashboard_subadmin.php';
                elseif ($_SESSION['role'] == 'usuario') $redirect_url = 'dashboard_usuario.php';
            }
            header('Location: ' . $redirect_url . '?status=success');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            log_action($pdo, 'ERROR_TRANSACAO', "Falha ao processar transação para ID $usuario_id: " . $e->getMessage());
            echo "Erro ao registrar transação: " . $e->getMessage();
        }

    } else {
        log_action($pdo, 'ERROR_TRANSACAO', "Tentativa de enviar relatório para ID de usuário inválido: $usuario_id (Org: $org_id).");
        echo "Erro: Usuário não encontrado!";
    }
} else {
    $received_user_id = $_POST['usuario_id'] ?? 'N/A';
    log_action($pdo, 'ERROR_TRANSACAO', "Tentativa de envio de relatório falhou. ID de usuário não fornecido ou vazio (Recebido: $received_user_id).");
    
    $redirect_url = 'index.php';
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'super_adm') $redirect_url = 'dashboard_superadmin.php';
        elseif ($_SESSION['role'] == 'admin') $redirect_url = 'dashboard_admin.php';
        elseif ($_SESSION['role'] == 'sub_adm') $redirect_url = 'dashboard_subadmin.php';
        elseif ($_SESSION['role'] == 'usuario') $redirect_url = 'dashboard_usuario.php';
    }
    header('Location: ' . $redirect_url . '?status=error_invalid_input'); 
    exit;
}
?>