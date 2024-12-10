<?php session_start(); $isLoggedIn = isset($_SESSION['admin_id']); ?>
<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
// Iniciar a sessão e verificar se o usuário é administrador
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Conectar ao banco de dados
include '../includes/db_connect.php';

$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8');
}

try {
    // Consultar todas as pessoas com todos os campos relevantes
    $sql = "SELECT id, nome, genero, idade, peso, altura, etnia, rg, cpf, cnh, data_nasc, email, telefone FROM pessoas";
    if (!empty($searchTerm)) {
        $sql .= " WHERE nome LIKE :searchTerm OR email LIKE :searchTerm OR telefone LIKE :searchTerm";
    }
    $stmt = $conn->prepare($sql);
    if (!empty($searchTerm)) {
        $stmt->bindValue(':searchTerm', "%$searchTerm%");
    }
    $stmt->execute();
    $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao consultar pessoas: " . $e->getMessage());
    echo "Erro ao carregar a lista de pessoas.";
}
?>

<div class="container my-4">
    <h1>Visualizar Pessoas</h1>

    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, email ou telefone" value="<?= $searchTerm ?>">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>Gênero</th>
                <th>Idade</th>
                <th>Peso (kg)</th>
                <th>Altura (m)</th>
                <th>Etnia</th>
                <th>RG</th>
                <th>CPF</th>
                <th>CNH</th>
                <th>Data de Nascimento</th>
                <th>Email</th>
                <th>Telefone</th>
                <!-- Exibir a coluna Ações somente para administradores -->
                <?php if ($isAdmin): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pessoas)): ?>
                <?php foreach ($pessoas as $pessoa): ?>
                    <tr>
                        <td><?= htmlspecialchars($pessoa['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['genero'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['idade'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['peso'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['altura'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['etnia'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['rg'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['cpf'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['cnh'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= !empty($pessoa['data_nasc']) ? htmlspecialchars((new DateTime($pessoa['data_nasc']))->format('d/m/Y')) : '' ?></td>
                        <td><?= htmlspecialchars($pessoa['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($pessoa['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <?php if ($isAdmin): ?>
                            <td>
                                <a href="../pages/editar_pessoa.php?id=<?= $pessoa['id'] ?>" class="btn btn-warning btn-sm">Alterar</a>
                                <a href="../actions/excluir_pessoa.php?id=<?= $pessoa['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14" class="text-center">Nenhuma pessoa cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
