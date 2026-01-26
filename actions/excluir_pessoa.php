<?php
include '../includes/db_connect.php';

if (!isset($conn) || $conn === null) {
    die("Erro: Conexão com o banco de dados não estabelecida.");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Excluir pessoa do banco de dados
    $sql = "DELETE FROM pessoas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ../views/view_pessoa.php?status=deleted");
} catch (PDOException $e) {
    error_log("Erro ao excluir pessoa: " . $e->getMessage());
    header("Location: ../views/view_pessoa.php?status=error");
}
