<?php
session_start(); 
include('config/db.php');
include('config/logger.php'); 

// **** ADICIONADO: Verificação de Sessão Multi-Tenant ****
if (!isset($_SESSION['id']) || !isset($_SESSION['org_id'])) {
    header('Location: login.php');
    exit;
}
$org_id = $_SESSION['org_id']; // O ID da organização do usuário logado
// **** FIM DA VERIFICAÇÃO ****

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

    $lucro = ($saque + $bau) - $deposito;

    // **** MODIFICADO: Busca o usuário DENTRO da organização ****
    $stmt_user = $pdo->prepare("SELECT nome, percentual_comissao, id_sub_adm FROM usuarios WHERE id_usuario = ? AND org_id = ?");
    $stmt_user->execute([$usuario_id, $org_id]);
    $user = $stmt_user->fetch();

    if ($user) {
        $comissao_usuario = 0;
        if ($user['percentual_comissao'] > 0) {
            $comissao_usuario = $lucro * ($user['percentual_comissao'] / 100);
        }
        
        $comissao_sub_adm = 0; 
        if ($user['id_sub_adm'] != NULL) {
            $comissao_sub_adm = $lucro - $comissao_usuario; 
        }

        try {
            $pdo->beginTransaction();

            // **** MODIFICADO: Adiciona org_id ****
            $stmt_dep = $pdo->prepare("INSERT INTO transacoes (org_id, id_usuario, tipo_transacao, valor, data) VALUES (?, ?, 'deposito', ?, ?)");
            $stmt_dep->execute([$org_id, $usuario_id, $deposito, $data_atual]);
            
            $stmt_saq = $pdo->prepare("INSERT INTO transacoes (org_id, id_usuario, tipo_transacao, valor, data) VALUES (?, ?, 'saque', ?, ?)");
            $stmt_saq->execute([$org_id, $usuario_id, $saque, $data_atual]);

            $stmt_bau = $pdo->prepare("INSERT INTO transacoes (org_id, id_usuario, tipo_transacao, valor, data) VALUES (?, ?, 'bau', ?, ?)");
            $stmt_bau->execute([$org_id, $usuario_id, $bau, $data_atual]);

            // **** MODIFICADO: Adiciona org_id ****
            $stmt_saldo = $pdo->prepare("UPDATE usuarios SET saldo = saldo + ? WHERE id_usuario = ? AND org_id = ?");
            $stmt_saldo->execute([$lucro, $usuario_id, $org_id]);

            // **** MODIFICADO: Adiciona org_id ****
            $stmt_relatorio = $pdo->prepare("
                INSERT INTO relatorios 
                (org_id, id_usuario, lucro_diario, comissao_usuario, comissao_sub_adm, valor_deposito, valor_saque, valor_bau, data) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_relatorio->execute([$org_id, $usuario_id, $lucro, $comissao_usuario, $comissao_sub_adm, $deposito, $saque, $bau, $data_atual]);

            $pdo->commit();

            log_action($pdo, 'RELATORIO_ENVIO', "Relatório ($log_tipo_relatorio) enviado para '{$user['nome']}' (ID: $usuario_id). Lucro: $lucro.");

            // Redirecionamento (lógica existente)
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
        // (lógica de redirecionamento existente)
        if ($_SESSION['role'] == 'super_adm') $redirect_url = 'dashboard_superadmin.php';
        elseif ($_SESSION['role'] == 'admin') $redirect_url = 'dashboard_admin.php';
        elseif ($_SESSION['role'] == 'sub_adm') $redirect_url = 'dashboard_subadmin.php';
        elseif ($_SESSION['role'] == 'usuario') $redirect_url = 'dashboard_usuario.php';
    }
    header('Location: ' . $redirect_url . '?status=error_invalid_input'); 
    exit;
}
?>