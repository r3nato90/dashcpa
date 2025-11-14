<?php
// Certifique-se de que a conexão com o banco de dados ($pdo) foi incluída antes de chamar este arquivo.

/**
 * Registra uma ação no sistema de logs.
 *
 * @param string $acao A descrição da ação realizada.
 */
function log_acao($acao) {
    global $pdo; // Usa a conexão PDO global
    
    // Pega o ID e o papel do usuário logado, se houver
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'visitante';
    $timestamp = date('Y-m-d H:i:s');
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Prepara e executa a inserção no banco de dados
    $sql = "INSERT INTO logs (timestamp, user_id, user_role, ip_address, user_agent, acao) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $timestamp,
            $user_id,
            $user_role,
            $ip_address,
            $user_agent,
            $acao
        ]);
    } catch (\PDOException $e) {
        // Em caso de erro ao registrar o log, imprime uma mensagem de erro
        error_log("ERRO AO REGISTRAR LOG: " . $e->getMessage());
    }
}
?>