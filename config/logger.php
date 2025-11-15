<?php
date_default_timezone_set('America/Sao_Paulo');

function log_action($pdo, $acao_tipo, $descricao) {
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario = null;
        $nome_usuario = 'Sistema'; 
        $role_usuario = 'SYSTEM'; 
        $org_id = null; // **** ADICIONADO ****

        if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
            $id_usuario = $_SESSION['id'];
            $role_usuario = $_SESSION['role'];
            $org_id = $_SESSION['org_id'] ?? null; // **** ADICIONADO ****

            // Busca o nome do usuário
            if (in_array($role_usuario, ['super_adm', 'admin', 'sub_adm'])) {
                $stmt_nome = $pdo->prepare("SELECT nome FROM sub_administradores WHERE id_sub_adm = ? AND org_id = ?");
                $stmt_nome->execute([$id_usuario, $org_id]);
            } else {
                $stmt_nome = $pdo->prepare("SELECT nome FROM usuarios WHERE id_usuario = ? AND org_id = ?");
                $stmt_nome->execute([$id_usuario, $org_id]);
            }
            $user_nome = $stmt_nome->fetch();
            if ($user_nome) {
                $nome_usuario = $user_nome['nome'];
            }
        }

        // **** MODIFICADO: Insere o org_id no log ****
        $stmt_log = $pdo->prepare("
            INSERT INTO logs (org_id, id_usuario_acao, nome_usuario_acao, role_usuario_acao, acao_tipo, descricao)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt_log->execute([$org_id, $id_usuario, $nome_usuario, $role_usuario, $acao_tipo, $descricao]);

    } catch (Exception $e) {
        // Falha silenciosa
    }
}
?>