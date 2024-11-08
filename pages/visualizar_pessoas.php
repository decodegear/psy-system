<?php session_start(); $isLoggedIn = isset($_SESSION['admin_id']); ?>
<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
// Iniciar a sessão e verificar se o usuário é administrador
//session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Conectar ao banco de dados
include '../includes/db_connect.php';

try {
    // Consultar todas as pessoas com todos os campos relevantes
    $sql = "SELECT id, nome, genero, idade, peso, altura, etnia, rg, cpf, cnh, data_nasc FROM pessoas";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao consultar pessoas: " . $e->getMessage());
    echo "Erro ao carregar a lista de pessoas.";
}
?>

<div class="container my-4">
    <h1>Visualizar Pessoas</h1>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
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
                        <td><?= htmlspecialchars($pessoa['nome']) ?></td>
                        <td><?= htmlspecialchars($pessoa['genero']) ?></td>
                        <td><?= htmlspecialchars($pessoa['idade']) ?></td>
                        <td><?= htmlspecialchars($pessoa['peso']) ?></td>
                        <td><?= htmlspecialchars($pessoa['altura']) ?></td>
                        <td><?= htmlspecialchars($pessoa['etnia']) ?></td>
                        <td><?= htmlspecialchars($pessoa['rg']) ?></td>
                        <td><?= htmlspecialchars($pessoa['cpf']) ?></td>
                        <td><?= htmlspecialchars($pessoa['cnh']) ?></td>
                        <td><?= htmlspecialchars((new DateTime($pessoa['data_nasc']))->format('d/m/Y')) ?></td>
                        <?php if ($isAdmin): ?>
                            <td>
                            <a href="../pages/editar_pessoa.php?id=<?= $pessoa['id'] ?>" class="btn btn-warning btn-sm">Alterar</a><?php ?>
                            <a href="../actions/excluir_pessoa.php?id=<?= $pessoa['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a><?php ?>
                            </td>
                        <?php endif; ?>
                        
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">Nenhuma pessoa cadastrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
