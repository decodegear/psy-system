<?php session_start();
$isLoggedIn = isset($_SESSION['admin_id']);
// Iniciar a sessão e verificar se o usuário é administrador
//session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Conectar ao banco de dados
include '../includes/db_connect.php';

try {
    // Consultar todos os usuários e incluir o campo role
    $sql = "SELECT id, nome, telefone, cpf, email, role FROM usuarios WHERE role IN ('admin', 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao consultar usuários: " . $e->getMessage());
    echo "Erro ao carregar a lista de usuários.";
}
include '../includes/header.php';
?>
<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Equipe</p>
            <h1>Visualizar Usu&aacute;rios</h1>
            <p class="page-subtitle">Gerencie acessos e permiss&otilde;es da equipe.</p>
        </div>
        <?php if ($isAdmin): ?>
            <a href="../pages/cadastrar_usuario.php" class="btn btn-primary page-cta">Adicionar Novo Usu&aacute;rio</a>
        <?php endif; ?>
    </section>

    <section class="table-card">
        <div class="table-wrapper">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th class="col-cpf">CPF</th>
                        <th>Email</th>
                        <th class="col-permissoes">Permiss&otilde;es</th>
                        <?php if ($isAdmin): ?>
                            <th>A&ccedil;&otilde;es</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                <td><?= htmlspecialchars($usuario['telefone']) ?></td>
                                <td class="col-cpf"><?= htmlspecialchars($usuario['cpf']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td class="col-permissoes"><?= htmlspecialchars($usuario['role']) ?></td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <a href="../actions/altera_usuario.php?id=<?= $usuario['id'] ?>" class="bi bi-pencil-square" style="color: orange;"></a>
                                        <a href="../actions/excluir_usuario.php?id=<?= $usuario['id'] ?>" class="bi bi-trash3" style="color: red" onclick="return confirm('Tem certeza que deseja excluir este usu&aacute;rio?');"></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? 6 : 5 ?>" class="text-center">Nenhum usu&aacute;rio cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php
include '../includes/footer.php';
?>

