<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include '../includes/db_connect.php';

// Variáveis para armazenar os valores do formulário
$id = null;
$nome = '';
$saldo_inicial = '';

if (isset($_GET['edit'])) {
    // Capturar o ID da conta a ser editada
    $id = $_GET['edit'];

    // Buscar os dados da conta pelo ID
    $sql = "SELECT * FROM contas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $conta = $stmt->fetch(PDO::FETCH_ASSOC);

    // Preencher o formulário com os dados da conta
    if ($conta) {
        $nome = $conta['nome'];
        $saldo_inicial = $conta['saldo_inicial'];
    } else {
        echo "Conta não encontrada.";
        exit;
    }
}

// Verificar se o formulário foi submetido para criar/editar conta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $saldo_inicial = $_POST['saldo_inicial'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Atualizar conta existente
        $sql = "UPDATE contas SET nome = ?, saldo_inicial = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $saldo_inicial, $id]);
        echo "Conta atualizada com sucesso!";
    } else {
        // Criar nova conta
        $sql = "INSERT INTO contas (nome, saldo_inicial) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $saldo_inicial]);
        echo "Conta cadastrada com sucesso!";
    }
}

// Verificar se há solicitação de exclusão
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM contas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    echo "Conta excluída com sucesso!";
}

// Buscar todas as contas cadastradas
$sql = "SELECT * FROM contas";
$result = $conn->query($sql);
include '../includes/header.php';
?>

<h1>Gerenciar Contas</h1>

<!-- Formulário de Cadastro/Alteração de Conta -->
<form method="post" action="cadastrar_conta.php">
    <label for="nome">Nome da Conta:</label>
    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome); ?>" required>

    <label for="saldo_inicial">Saldo Inicial:</label>
    <input type="number" id="saldo_inicial" name="saldo_inicial" step="0.01" value="<?= htmlspecialchars($saldo_inicial); ?>" required>

    <input type="hidden" name="id" value="<?= $id; ?>">
    <input type="submit" value="Salvar Conta">
</form>

<h2>Contas Cadastradas</h2>
<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Saldo Inicial</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['nome']); ?></td>
                <td><?= htmlspecialchars($row['saldo_inicial']); ?></td>
                <td>
                    <a href="cadastrar_conta.php?edit=<?= $row['id']; ?>" class=" btn btn-warning btn-sm">Editar</a>
                    <a href="cadastrar_conta.php?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta conta?');">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
include '../includes/footer.php';
?>