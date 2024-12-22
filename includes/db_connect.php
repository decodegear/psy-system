<?php
// Configuração de banco de dados usando variáveis de ambiente ou valores padrão
$servername = getenv('DB_SERVER') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'u599484558_psy';
$password = getenv('DB_PASSWORD') ?: 'A1L9I6C0E@psy';
$dbname = getenv('DB_NAME') ?: 'u599484558_psy';

try {
    // Conexão usando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Configurar o modo de erro do PDO para exceções
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Tratamento de erro mais seguro e flexível
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
    die("Erro ao conectar-se ao banco de dados. Por favor, tente novamente mais tarde.");
}

// Define o base URL para links no navegador
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    //$script_name = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = rtrim($protocol . "://" . $host, '/');
    define('BASE_URL', $base_url);
}

// Define o caminho absoluto no servidor para includes
if (!defined('BASE_PATH')) {
    $base_path = rtrim(realpath(dirname(__FILE__)), '/');
    define('BASE_PATH', $base_path);
}




?>
