<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Buscar dados da pessoa para edição
    $sql = "SELECT nome, genero, idade, peso, altura, etnia, rg, cpf, cnh, data_nasc FROM pessoas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pessoa) {
        die("Pessoa não encontrada.");
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar dados da pessoa: " . $e->getMessage());
    echo "Erro ao carregar dados da pessoa.";
}
?>

<h1>Editar Pessoa</h1>

<form action="../actions/update_pessoa.php" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- Nome -->
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($pessoa['nome']) ?>" required>
    
    <!-- Gênero -->
    <label for="genero">Gênero:</label>
    <select id="genero" name="genero" required>
        <option value="Masculino" <?= $pessoa['genero'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
        <option value="Feminino" <?= $pessoa['genero'] == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
        <option value="Outro" <?= $pessoa['genero'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
    </select>
    
    <!-- Outros campos (Idade, Peso, Altura, etc.) continuam da mesma forma -->
    <!-- Idade -->
    <label for="idade">Idade:</label>
    <input type="number" id="idade" name="idade" value="<?= htmlspecialchars($pessoa['idade']) ?>" required>
    
    <!-- Peso -->
    <label for="peso">Peso (kg):</label>
    <input type="number" id="peso" name="peso" step="0.1" value="<?= htmlspecialchars($pessoa['peso']) ?>" required>
    
    <!-- Altura -->
    <label for="altura">Altura (cm):</label>
    <input type="number" id="altura" name="altura" value="<?= htmlspecialchars($pessoa['altura']) ?>" required>
    
    <!-- Etnia -->
    <label for="etnia">Etnia:</label>
    <input type="text" id="etnia" name="etnia" value="<?= htmlspecialchars($pessoa['etnia']) ?>" required>
    
    <!-- RG -->
    <label for="rg">RG:</label>
    <input type="text" id="rg" name="rg" value="<?= htmlspecialchars($pessoa['rg']) ?>" required>
    
    <!-- CPF -->
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($pessoa['cpf']) ?>" required>
    
    <!-- CNH -->
    <label for="cnh">CNH:</label>
    <input type="text" id="cnh" name="cnh" value="<?= htmlspecialchars($pessoa['cnh']) ?>">

    <!-- Data de Nascimento -->
    <label for="nasc">Data de Nascimento:</label>
    <input type="date" id="nasc" name="nasc" value="<?= htmlspecialchars($pessoa['data_nasc']) ?>" required>

    <!-- Botão de Submissão -->
    <input type="submit" value="Atualizar Pessoa">
</form>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
