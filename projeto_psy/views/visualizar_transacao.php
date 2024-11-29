<?php
session_start();
include '../includes/header.php';
include '../includes/db_connect.php';

$tipo = $_GET['tipo'] ?? 'despesa';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

try {
    $sql = "SELECT id, nome, descricao, valor, data_vencimento, data_inclusao, situacao, parcelado, qtd_parcelas, tipo 
            FROM transacoes 
            WHERE tipo = :tipo
            ORDER BY data_vencimento DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':tipo' => $tipo]);
    $transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao consultar: " . $e->getMessage());
    echo "Erro ao carregar as transações.";
}
?>

<div class="container my-4">
    <h1>Visualizar <?= ucfirst($tipo) ?></h1>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Data de Vencimento</th>
                <th>Situação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transacoes as $transacao): ?>
                <tr>
                    <td><?= htmlspecialchars($transacao['nome']) ?></td>
                    <td><?= htmlspecialchars($transacao['descricao']) ?></td>
                    <td><?= htmlspecialchars(number_format($transacao['valor'], 2, ',', '.')) ?></td>
                    <td><?= htmlspecialchars($transacao['data_vencimento']) ?></td>
                    <td><?= htmlspecialchars($transacao['situacao']) ?></td>
                    <td>
                        <a href="../actions/altera_transacao.php?action=edit&id=<?= $transacao['id'] ?>&tipo=<?= $transacao['tipo'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="../actions/altera_transacao.php?action=delete&id=<?= $transacao['id'] ?>&tipo=<?= $transacao['tipo'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
