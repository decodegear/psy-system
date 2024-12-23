<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
include '../includes/db_connect.php';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'receita';
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Buscar dados da receita para edição
    $sql = "SELECT nome, descricao, valor, data_vencimento, situacao, parcelado, qtd_parcelas, categoria_id, conta_id 
            FROM transacoes 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $receita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$receita) {
        die("receita não encontrada.");
    }

    // Buscar categorias de receitas
    $sql_categorias = "SELECT id, nome FROM categorias WHERE tipo = 'receita'";
    $result_categorias = $conn->query($sql_categorias);

    // Buscar contas
    $sql_contas = "SELECT id, nome FROM contas";
    $result_contas = $conn->query($sql_contas);

} catch (PDOException $e) {
    error_log("Erro ao buscar dados da receita: " . $e->getMessage());
    echo "Erro ao carregar dados da receita.";
}
?>

<h1>Editar receita</h1>

<form action="../actions/update_receita.php" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- Nome -->
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($receita['nome']) ?>" required>

    <!-- Descrição -->
    <label for="descricao">Descrição:</label>
    <textarea id="descricao" name="descricao" required><?= htmlspecialchars($receita['descricao']) ?></textarea>

    <!-- Valor -->
    <label for="valor">Valor:</label>
    <input type="number" id="valor" name="valor" step="0.01" value="<?= htmlspecialchars($receita['valor']) ?>" required>

    <!-- Data para Vencimento -->
    <label for="data_vencimento">Data para Vencimento:</label>
    <input type="date" id="data_vencimento" name="data_vencimento" value="<?= htmlspecialchars($receita['data_vencimento']) ?>" required>

    <!-- Situação -->
    <label for="situacao">Situação:</label>
    <select id="situacao" name="situacao" required>
        <option value="Pago" <?= $receita['situacao'] == 'Pago' ? 'selected' : '' ?>>Pago</option>
        <option value="A pagar" <?= $receita['situacao'] == 'A pagar' ? 'selected' : '' ?>>A pagar</option>
    </select>

    <!-- Categoria -->
    <label for="categoria_id">Categoria:</label>
    <select id="categoria_id" name="categoria_id" required>
        <?php while ($row_categoria = $result_categorias->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $row_categoria['id']; ?>" <?= $receita['categoria_id'] == $row_categoria['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row_categoria['nome']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- Conta -->
    <label for="conta_id">Conta:</label>
    <select id="conta_id" name="conta_id" required>
        <?php while ($row_conta = $result_contas->fetch(PDO::FETCH_ASSOC)): ?>
            <option value="<?= $row_conta['id']; ?>" <?= $receita['conta_id'] == $row_conta['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row_conta['nome']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- Parcelamento -->
    <label for="parcelado">Parcelado:</label>
    <input type="checkbox" id="parcelado" name="parcelado" value="1" <?= $receita['parcelado'] ? 'checked' : '' ?>>

    <!-- Quantidade de Parcelas (aparece somente se "Parcelado" estiver marcado) -->
    <div id="parcelas-container" style="<?= $receita['parcelado'] ? 'display:block;' : 'display:none;' ?>">
        <label for="qtd_parcelas">Quantidade de Parcelas:</label>
        <input type="number" id="qtd_parcelas" name="qtd_parcelas" min="1" value="<?= htmlspecialchars($receita['qtd_parcelas']) ?>">
    </div>

    <!-- Botão de Submissão -->
    <input type="submit" value="Atualizar <?= $tipo; ?>">
</form>

<script>
    // Mostrar/esconder o campo de quantidade de parcelas com base na checkbox "Parcelado"
    document.getElementById('parcelado').addEventListener('change', function() {
        var parcelasContainer = document.getElementById('parcelas-container');
        if (this.checked) {
            parcelasContainer.style.display = 'block';
        } else {
            parcelasContainer.style.display = 'none';
        }
    });
</script>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
