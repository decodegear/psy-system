<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
include '../includes/db_connect.php';

$id = null;
$nome = '';
$tipo = 'receita';

$filtro_receita = true;
$filtro_despesa = true;

$mensagem = ""; // Variável para armazenar a mensagem de sucesso/erro

// Verificar se há solicitação de edição
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($categoria) {
        $nome = $categoria['nome'];
        $tipo = $categoria['tipo'];
    } else {
        $mensagem = "Categoria não encontrada.";
    }
}

// Verificar se o formulário foi submetido para criar/editar categoria
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $id = $_POST['id'] ?? null;

    // Verificar duplicidade antes de inserir ou atualizar
    $sql_check = "SELECT COUNT(*) FROM categorias WHERE nome = ? AND tipo = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$nome, $tipo]);
    $existe_categoria = $stmt_check->fetchColumn();

    if ($existe_categoria > 0 && !$id) {
        // Caso seja uma duplicação no cadastro de uma nova categoria
        $mensagem = "Esta categoria já está cadastrada!";
    } elseif ($id) {
        // Atualizar categoria existente
        $sql = "UPDATE categorias SET nome = ?, tipo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $tipo, $id]);
        $mensagem = "Categoria atualizada com sucesso!";
    } else {
        // Inserir nova categoria
        $sql = "INSERT INTO categorias (nome, tipo) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $tipo]);
        $mensagem = "Categoria cadastrada com sucesso!";
    }
}

// Verificar se há solicitação de exclusão
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql_check = "SELECT COUNT(*) AS total FROM transacoes WHERE categoria_id = ? UNION ALL SELECT COUNT(*) AS total FROM transacoes WHERE categoria_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$id, $id]);
    $usos = $stmt_check->fetchAll(PDO::FETCH_COLUMN);

    $usos_receita = isset($usos[0]) ? $usos[0] : 0;
    $usos_despesa = isset($usos[1]) ? $usos[1] : 0;

    if ($usos_receita > 0 || $usos_despesa > 0) {
        $mensagem = "Não é possível excluir a categoria, pois está sendo utilizada.";
    } else {
        $sql = "DELETE FROM categorias WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $mensagem = "Categoria excluída com sucesso!";
    }
}

// Verificar filtros
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['filtro_receita']) || isset($_POST['filtro_despesa']))) {
    $filtro_receita = isset($_POST['filtro_receita']);
    $filtro_despesa = isset($_POST['filtro_despesa']);
}

$filtro = '';
if ($filtro_receita && !$filtro_despesa) {
    $filtro = "WHERE tipo = 'receita'";
} elseif (!$filtro_receita && $filtro_despesa) {
    $filtro = "WHERE tipo = 'despesa'";
} elseif (!$filtro_receita && !$filtro_despesa) {
    $filtro = "WHERE 1=0";
}

$sql = "SELECT * FROM categorias $filtro ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
include '../includes/header.php';
?>

<h1>Gerenciar Categorias</h1>

<form method="post" action="cadastrar_categoria.php">
    <label for="nome">Nome da Categoria:</label>
    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome ?? ''); ?>" required>

    <label for="tipo">Tipo:</label>
    <select id="tipo" name="tipo">
        <option value="receita" <?= $tipo == 'receita' ? 'selected' : ''; ?>>receita</option>
        <option value="despesa" <?= $tipo == 'despesa' ? 'selected' : ''; ?>>despesa</option>
    </select>

    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? ''); ?>">
    <input type="submit" value="Salvar Categoria">
</form>

<h2>Filtrar Categorias</h2>
<form method="post" action="cadastrar_categoria.php">
    <label><input type="checkbox" name="filtro_receita" <?= $filtro_receita ? 'checked' : ''; ?>> Receitas</label>
    <label><input type="checkbox" name="filtro_despesa" <?= $filtro_despesa ? 'checked' : ''; ?>> Despesas</label>
    <input type="submit" value="Filtrar">
</form>

<h2>Categorias Cadastradas</h2>
<?php if (!empty($result)): ?>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']); ?></td>
                    <td><?= htmlspecialchars($row['tipo']); ?></td>
                    <td>
                        <a class="bi bi-pencil-square" style="color: orange" href="cadastrar_categoria.php?edit=<?= $row['id']; ?>"></a>
                        <a class="bi bi-trash3" style="color: red;" href="cadastrar_categoria.php?delete=<?= $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta categoria?');"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhuma categoria encontrada.</p>
<?php endif; ?>

<?php if ($mensagem): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("<?= $mensagem; ?>");
            window.location.href = "cadastrar_categoria.php";
        });
    </script>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>