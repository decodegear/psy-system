
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/db_connect.php';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'despesa';

if (!$conn) {
    die("Erro: Falha na conexão com o banco de dados.");
}

$stmt = $conn->prepare("SELECT * FROM transacoes WHERE tipo = :tipo ORDER BY data_vencimento DESC");
$stmt->execute([':tipo' => $tipo]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Finan&ccedil;as</p>
            <h1>Visualizar <?= htmlspecialchars(ucwords($tipo)) ?></h1>
            <p class="page-subtitle">Acompanhe suas <?= htmlspecialchars($tipo) ?> registradas no sistema.</p>
        </div>
        <?php if ($isAdmin): ?>
            <a href="../pages/cadastro_transacao.php?tipo=<?= htmlspecialchars($tipo); ?>" class="btn btn-primary page-cta">Nova <?= htmlspecialchars($tipo); ?></a>
        <?php endif; ?>
    </section>

    <section class="table-card">
        <div class="table-wrapper">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th class="col-data-vencimento">Data de Vencimento</th>
                        <th class="col-nome">Nome</th>
                        <th class="col-descricao">Descri&ccedil;&atilde;o</th>
                        <th class="col-valor">Valor</th>
                        <th class="col-situacao">Situa&ccedil;&atilde;o</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resultados)): ?>
                        <?php foreach ($resultados as $item): ?>
                            <tr>
                                <td class="col-data-vencimento"><strong><?php echo date('d/m/Y', strtotime($item['data_vencimento'])); ?></strong></td>
                                <td class="col-nome">
                                    <a href="../actions/altera_transacao.php?id=<?= $item['id']; ?>&tipo=<?= urlencode($tipo); ?>" class="nome-link">
                                        <?= htmlspecialchars($item['nome']) ?>
                                    </a>
                                </td>
                                <td class="col-descricao"><?= htmlspecialchars($item['descricao']) ?></td>
                                <td class="col-valor"><?= htmlspecialchars(number_format($item['valor'], 2, ',', '.')) ?></td>
                                <td class="col-situacao"><?= htmlspecialchars($item['situacao']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Nenhum item cadastrado. Clique em "Nova <?= htmlspecialchars($tipo); ?>" para adicionar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

