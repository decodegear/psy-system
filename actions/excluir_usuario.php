<?php
include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Excluir usuário do banco de dados
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ../pages/visualizar_usuarios.php?status=deleted");
} catch (PDOException $e) {
    error_log("Erro ao excluir usuário: " . $e->getMessage());
    header("Location: ../pages/visualizar_usuarios.php?status=error");
}
?>
