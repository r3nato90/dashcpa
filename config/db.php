<?php
// /config/db.php
// Garante que o vendor/autoload.php seja encontrado a partir da raiz
require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis de ambiente do .env que está na raiz
// __DIR__ é a pasta atual (/config), '..' sobe para a raiz (/public_html)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); 
$dotenv->load();

$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_DATABASE'];
$user = $_ENV['DB_USERNAME'];
$pass = $_ENV['DB_PASSWORD'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Em caso de falha, loga o erro e exibe uma mensagem genérica
     // Verifique o log de erros do seu servidor para ver a mensagem completa
     error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
     
     // Mensagem de erro genérica para o usuário
     die("Erro 500: Falha interna ao conectar com o servidor. (DB)"); 
}
?>