<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
include '../includes/db_connect.php';

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $role = $_POST['role'];
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);



    // Inserir novo usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, cpf, telefone, senha, role) VALUES (?, ?, ?, ?, ? ,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $email, $cpf, $telefone, $senha_hash, $role]);

    echo "Usuário cadastrado com sucesso!";
    header("Location: ../views/visualizar_usuarios.php?status=updated");
}
include '../includes/header.php';
?>

<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Equipe</p>
            <h1>Cadastrar Usu&aacute;rio</h1>
            <p class="page-subtitle">Defina dados e permiss&otilde;es de acesso.</p>
        </div>
    </section>

    <section class="table-card">
        <h2>Novo usu&aacute;rio</h2>
        <form method="post" action="../actions/insert_usuario.php" class="form-grid">
            <div class="form-field">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-field">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required>
            </div>

            <div class="form-field">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" required>
            </div>

            <div class="form-field">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>

            <div class="form-field">
                <label for="role">Fun&ccedil;&atilde;o:</label>
                <select id="role" name="role">
                    <option value="admin">Administrador</option>
                    <option value="user">Usu&aacute;rio Comum</option>
                </select>
            </div>

            <div class="form-field form-field--full">
                <button type="submit" class="btn btn-primary">Cadastrar Usu&aacute;rio</button>
            </div>
        </form>
    </section>
</main>

<?php
include '../includes/footer.php';
?>