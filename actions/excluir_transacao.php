<?php
include($_SERVER['DOCUMENT_ROOT'] . '../includes/db_connect.php');

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Excluir despesa do banco de dados
    $sql = "DELETE FROM transacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    header("Location: ../views/visualizar_transacao.php?tipo=$tipo");
    //header("Location: ../views/visualizar_transacao.php?tipo=.urlencode($tipo)");
} catch (PDOException $e) {
    error_log("Erro ao excluir despesa: " . $e->getMessage());
    header("Location: ../views/visualizar_transacao.php?tipo=$tipo");
    //header("Location: ../views/visualizar_transacao.php?tipo=".urlencode($tipo));
}
?>
