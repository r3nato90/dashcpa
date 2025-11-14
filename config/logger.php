<?php
// Certifique-se que o fuso horário está definido
date_default_timezone_set('America/Sao_Paulo');

/**
 * Registra uma ação no banco de dados.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $acao_tipo Tipo da ação (ex: 'LOGIN_FAIL', 'USER_CREATE', 'ERROR').
 * @param string $descricao Descrição detalhada da ação.
 */
function log_action($pdo, $acao_tipo, $descricao) {
    try {
        // Tenta iniciar a sessão se ela não existir (para pegar dados do usuário)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario = null;
        $nome_usuario = 'Sistema'; // Default
        $role_usuario = 'SYSTEM'; // Default

        // Verifica se o usuário está logado e pega seus dados
        if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
            $id_usuario = $_SESSION['id'];
            $role_usuario = $_SESSION['role'];

            // Busca o nome do usuário (de tabelas diferentes dependendo da role)
            if (in_array($role_usuario, ['super_adm', 'admin', 'sub_adm'])) {
                $stmt_nome = $pdo->prepare("SELECT nome FROM sub_administradores WHERE id_sub_adm = ?");
            } else {
                $stmt_nome = $pdo->prepare("SELECT nome FROM usuarios WHERE id_usuario = ?");
            }
            $stmt_nome->execute([$id_usuario]);
            $user_nome = $stmt_nome->fetch();
            
            if ($user_nome) {
                $nome_usuario = $user_nome['nome'];
            }
        }

        // Insere o log
        $stmt_log = $pdo->prepare("
            INSERT INTO logs (id_usuario_acao, nome_usuario_acao, role_usuario_acao, acao_tipo, descricao)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt_log->execute([$id_usuario, $nome_usuario, $role_usuario, $acao_tipo, $descricao]);

    } catch (Exception $e) {
        // Se o logger falhar, não quebre a aplicação.
        // error_log("Falha ao registrar log: " . $e->getMessage());
    }
}
?>