<?php
include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Excluir agendamento do banco de dados
    $sql = "DELETE FROM agendamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    header("Location: ..//pages/visualizar_agendamentos.php?status=deleted");
} catch (PDOException $e) {
    error_log("Erro ao excluir agendamento: " . $e->getMessage());
    header("Location: ..//pages/visualizar_agendamentos.php?status=error");
}
?>
