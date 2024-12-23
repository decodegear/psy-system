<?php
include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null; // Capturar o tipo da transação

if (!$id) {
    die("ID inválido.");
}

try {
    // Verificar se o ID e tipo correspondem a uma transação existente
    $sql = "SELECT tipo FROM transacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $transacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transacao) {
        die("Transação não encontrada.");
    }

    // Garantir que o tipo corresponde ao tipo da transação
    $tipo = $transacao['tipo'] ?? $tipo;

    // Excluir transação do banco de dados
    $sql = "DELETE FROM transacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    // Redirecionar para a página correta com o tipo especificado
    header("Location: ../views/visualizar_transacao.php?tipo=" . urlencode($tipo) . "&status=deleted");
    exit;

} catch (PDOException $e) {
    error_log("Erro ao excluir transação: " . $e->getMessage());
    header("Location: ../views/visualizar_transacao.php?tipo=" . urlencode($tipo) . "&status=error");
    exit;
}
?>
