<?php
// Configuração de banco de dados SQLite local
$db_file = __DIR__ . '/../database/psy_system.db';
$db_dir = dirname($db_file);

// Criar diretório do banco se não existir
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0755, true);
}

try {
    // Conexão usando PDO SQLite
    $conn = new PDO("sqlite:" . $db_file);
    // Configurar o modo de erro do PDO para exceções
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Habilitar chaves estrangeiras no SQLite
    $conn->exec('PRAGMA foreign_keys = ON;');
    // Melhorar performance
    $conn->exec('PRAGMA journal_mode = WAL;');
} catch (PDOException $e) {
    // Tratamento de erro mais seguro e flexível
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
    die("Erro ao conectar-se ao banco de dados. Por favor, tente novamente mais tarde.");
}
