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
<meta name="viewport" content="width=device-width, initial-scale=1">
<div class="container my-4">
    <h1>Visualizar Usuários</h1>
    <!-- Botão para adicionar novo usuário (somente visível para administradores) -->
    <?php if ($isAdmin): ?>
        <a href="../pages/cadastrar_usuario.php" class="btn btn-primary mb-3">Adicionar Novo Usuário</a><?php ?>
    <?php endif; ?>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th class="col-cpf">CPF</th>
                <th>Email</th>
                <th class="col-permissoes">Permissões</th>
                <!-- Exibir a coluna Ações somente para administradores -->
                <?php if ($isAdmin): ?>
                    <th>Ações</th>
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
                        <!-- Exibir os botões de ação somente para administradores -->
                        <?php if ($isAdmin): ?>
                            <td>
                                <a href="../actions/altera_usuario.php?id=<?= $usuario['id'] ?>" class="bi bi-pencil-square" style="color: orange;"></a><?php ?>
                                <a href="../actions/excluir_usuario.php?id=<?= $usuario['id'] ?>" class="bi bi-trash3" style="color: red" onclick="return confirm('Tem certeza que deseja excluir este usuário?');"></a><?php ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $isAdmin ? 6 : 5 ?>" class="text-center">Nenhum usuário cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Inclui o rodapé
include '../includes/footer.php';
?>