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
    <div class="montserrat-text main-content">
        <h1>Visualizar <?= htmlspecialchars(ucwords($tipo)) ?></h1>
        <?php if ($isAdmin): ?>
            <a href="../pages/cadastro_transacao.php?tipo=<?= htmlspecialchars($tipo); ?>" class="btn btn-primary mb-3">Nova <?= htmlspecialchars($tipo); ?></a>
        <?php endif; ?>
        <div class="list-group mb-4">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <!-- <th class="col-tipo">Tipo</th> -->
                        <th class="col-data-vencimento">Data de Vencimento</th>
                        <th class="col-nome">Nome</th>
                        <th class="col-descricao">Descrição</th>
                        <th class="col-valor">Valor</th>
                        <!-- <th class="col-data-inclusao">Data de Inclusão</th> -->
                        <th class="col-situacao">Situação</th>
                        <th class="col-parcelado">Parcelado</th>
                        <th class="col-parcelas">Parcelas</th>
                        <?php if ($isAdmin): ?>
                            <th class="col-acoes">Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resultados)): ?>
                        <?php foreach ($resultados as $item): ?>
                            <tr>
                                <td class="col-data-vencimento"><strong><?php echo date('d/m/Y', strtotime($item['data_vencimento'])); ?></strong></td>
                                <td class="col-nome"><?= htmlspecialchars($item['nome']) ?></td>
                                <td class="col-descricao"><?= htmlspecialchars($item['descricao']) ?></td>
                                <td class="col-valor"><?= htmlspecialchars(number_format($item['valor'], 2, ',', '.')) ?></td>
                                <!-- <td class="col-data-inclusao"><?= htmlspecialchars($item['data_inclusao']) ?></td> -->
                                <td class="col-situacao"><?= htmlspecialchars($item['situacao']) ?></td>
                                <td class="col-parcelado"><?= $item['parcelado'] ? 'Sim' : 'Não' ?></td>
                                <td class="col-parcelas"><?= $item['parcelado'] ? htmlspecialchars($item['qtd_parcelas']) : 'N/A' ?></td>
                                <?php if ($isAdmin): ?>
                                    <td class="col-acoes">
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
    </div>
    <?php include '../includes/footer.php'; ?>
</body>