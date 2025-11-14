<?php
// Configurações do Banco de Dados (PDO - MySQL)
$host = 'localhost'; // Host do banco de dados
$db   = 'u864690811_sistema_novo1'; // Nome do banco de dados
$user = 'u864690811_sistema_novo1'; // Usuário do banco de dados
$pass = '&vOhKV9B4R'; // Senha do banco de dados
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
    // Em um ambiente de produção, registre o erro em um log em vez de exibi-lo
    // Exibição amigável para o usuário final
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
    // Se quiser ver o erro detalhado para debug: die($e->getMessage());
}
?>