<?php
include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Excluir pessoa do banco de dados
    $sql = "DELETE FROM pessoas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ../pages/visualizar_pessoas.php?status=deleted");
} catch (PDOException $e) {
    error_log("Erro ao excluir pessoa: " . $e->getMessage());
    header("Location: ../pages/visualizar_pessoas.php?status=error");
}
?>
