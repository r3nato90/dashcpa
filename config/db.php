<?php
// Carregar variáveis de ambiente
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_DATABASE') ?: 'u864690811_sistema_novo1';
$username = getenv('DB_USERNAME') ?: 'u864690811_sistema_novo1';
$password = getenv('DB_PASSWORD') ?: '&vOhKV9B4R';

// **** NOVAS CONSTANTES DE COMISSÃO (HIERARQUIA DE 3 NÍVEIS) ****
define('ADMIN_PRINCIPAL_ID', 1); // ID do "Douglas" (ou seu admin principal, `role` = 'admin')
define('COMISSAO_ADMIN_PCT', 0.50);     // 50% para o Administrador
define('COMISSAO_GERENTE_PCT', 0.10);   // 10% para o Gerente (sub_adm)
define('COMISSAO_OPERADOR_PCT', 0.40);  // 40% para o Operador (usuario)

// URL Base do site (para links de registro)
$site_url = "https://dashcpa.com.br"; // Substitua pela sua URL real

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}
?>