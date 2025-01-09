<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'despesa';

$stmt = $conn->prepare("SELECT * FROM transacoes WHERE tipo = :tipo ORDER BY data_vencimento DESC");
$stmt->execute([':tipo' => $tipo]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<body>
    <div class="wrapper">
        <div class="montserrat-text main-content">
            <h1>Visualizar <?= htmlspecialchars($tipo) ?></h1>

            <?php if ($isAdmin): ?>
                <a href="../pages/cadastro_transacao.php?tipo=<?= htmlspecialchars($tipo); ?>" class="btn btn-primary mb-3">Nova <?= htmlspecialchars($tipo); ?></a>
            <?php endif; ?>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Data de Vencimento</th>
                        <th>Data de Inclusão</th>
                        <th>Situação</th>
                        <th>Parcelado</th>
                        <th>Parcelas</th>
                        <?php if ($isAdmin): ?>
                            <th>Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resultados)): ?>
                        <?php foreach ($resultados as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['tipo']) ?></td>
                                <td><?= htmlspecialchars($item['nome']) ?></td>
                                <td><?= htmlspecialchars($item['descricao']) ?></td>
                                <td><?= htmlspecialchars(number_format($item['valor'], 2, ',', '.')) ?></td>
                                <td><?= htmlspecialchars($item['data_vencimento']) ?></td>
                                <td><?= htmlspecialchars($item['data_inclusao']) ?></td>
                                <td><?= htmlspecialchars($item['situacao']) ?></td>
                                <td><?= $item['parcelado'] ? 'Sim' : 'Não' ?></td>
                                <td><?= $item['parcelado'] ? htmlspecialchars($item['qtd_parcelas']) : 'N/A' ?></td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <a href="../actions/altera_transacao.php?id=<?= $item['id']; ?>&tipo=<?= urlencode($tipo); ?>" class="btn btn-warning btn-sm">Alterar</a>
                                        <a href="../actions/excluir_transacao.php?id=<?= $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Nenhum item cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php include '../includes/footer.php'; ?>
    </div>
</body>