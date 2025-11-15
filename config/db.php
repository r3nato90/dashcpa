<?php
// **** INÍCIO DA CORREÇÃO: LEITOR MANUAL DO .ENV ****

// Define o caminho para o arquivo .env (assumindo que ele está na raiz, um nível acima de /config/)
$envFile = __DIR__ . '/.env'; 

if (file_exists($envFile)) {
    // Lê o arquivo .env
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignora linhas que são comentários (começam com #)
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Divide a linha em chave=valor
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Remove aspas do valor (ex: "valor" vira valor)
        if (substr($value, 0, 1) == '"' && substr($value, -1) == '"') {
            $value = substr($value, 1, -1);
        }

        // Define a variável de ambiente para que getenv() possa usá-la
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}
// **** FIM DA CORREÇÃO ****


// Carregar variáveis de ambiente (agora o getenv() funcionará)
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_DATABASE') ?: 'u864690811_sistema_novo1';
$username = getenv('DB_USERNAME') ?: 'u864690811_sistema_novo1';
$password = getenv('DB_PASSWORD') ?: ';nyA/YUkH7';

// Carrega a URL do site (AGORA DEVE FUNCIONAR)
// Usamos $_ENV como uma garantia extra caso putenv() não seja suficiente
$site_url = $_ENV['SITE_URL'] ?? getenv('SITE_URL') ?: 'https://sistema.dashcpa.com.br';

// Conexão ao banco de dados
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}
?>