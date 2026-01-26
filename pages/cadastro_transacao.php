<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
include '../includes/db_connect.php';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'despesa';

$stmt_categorias = $conn->prepare("SELECT id, nome FROM categorias WHERE tipo = :tipo");
$stmt_categorias->execute([':tipo' => ucfirst($tipo)]);

$stmt_contas = $conn->prepare("SELECT id, nome FROM contas");
$stmt_contas->execute();
include '../functions/auth.php';
include '../includes/header.php';
?>

<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Financeiro</p>
            <h1>Cadastro de <?= ucfirst($tipo); ?></h1>
            <p class="page-subtitle">Organize receitas e despesas com detalhes completos.</p>
        </div>
    </section>

    <section class="table-card">
        <h2>Nova <?= ucfirst($tipo); ?></h2>
        <form action="../actions/insert_transacao.php" method="post" class="form-grid">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo); ?>">

            <div class="form-field">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-field">
                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" required>
            </div>

            <div class="form-field">
                <label for="data_vencimento">Data de Vencimento:</label>
                <input type="date" id="data_vencimento" name="data_vencimento" required>
            </div>

            <div class="form-field">
                <label for="situacao">Situação:</label>
                <select id="situacao" name="situacao" required>
                    <option value="">Selecione a situação</option>
                    <option value="Pago">Pago</option>
                    <option value="A pagar">A pagar</option>
                </select>
            </div>

            <div class="form-field">
                <label for="categoria_id">Categoria:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione a categoria</option>
                    <?php while ($row_categoria = $stmt_categorias->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $row_categoria['id']; ?>"><?= htmlspecialchars($row_categoria['nome']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-field">
                <label for="conta_id">Conta:</label>
                <select id="conta_id" name="conta_id" required>
                    <option value="">Selecione a conta</option>
                    <?php while ($row_conta = $stmt_contas->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $row_conta['id']; ?>"><?= htmlspecialchars($row_conta['nome']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-field form-field--full">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4"></textarea>
            </div>

            <div id="parcelas-container" class="form-field" style="display:none;">
                <label for="qtd_parcelas">Quantidade de Parcelas:</label>
                <input type="number" id="qtd_parcelas" name="qtd_parcelas" min="1" value="1">
            </div>

            <input type="hidden" id="data_inclusao" name="data_inclusao" value="<?= date('Y-m-d'); ?>">

            <div class="form-field form-field--full">
                <button type="submit" class="btn btn-primary">Cadastrar <?= ucfirst($tipo); ?></button>
            </div>
        </form>
    </section>
</main>

<?php
include '../includes/footer.php';
?>