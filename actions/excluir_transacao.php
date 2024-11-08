<?php
include($_SERVER['DOCUMENT_ROOT'] . '../includes/db_connect.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Excluir despesa do banco de dados
    $sql = "DELETE FROM despesas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ..//pages/visualizar_transacao.php?status=deleted");
} catch (PDOException $e) {
    error_log("Erro ao excluir despesa: " . $e->getMessage());
    header("Location: ..//pages/visualizar_transacao.php?status=error");
}
?>
