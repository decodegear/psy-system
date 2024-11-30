<?php 
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

include '../includes/header.php'; 
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
}
?>

<h1>Cadastrar Usuário</h1>
<form method="post" action="cadastrar_usuario.php">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" required>

    <label for="senha">Senha:</label>
    <input type="password" id="senha" name="senha" required>

    <label for="role">Função:</label>
    <select id="role" name="role">
        <option value="admin">Administrador</option>
        <option value="user">Usuário Comum</option>
    </select>

    <input type="submit" value="Cadastrar Usuário">
</form>

<?php 
include '../includes/footer.php'; 
?>
