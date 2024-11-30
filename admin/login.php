<?php 
session_start();

include '../includes/header.php';
include '../includes/db_connect.php';
// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consultar o banco de dados para verificar se o administrador existe
    $sql = "SELECT id, nome, senha, role FROM usuarios WHERE email = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o usuário foi encontrado e se a senha está correta
    // Depuração no login.php
if ($usuario && password_verify($senha, $usuario['senha'])) {
    // Autenticação bem-sucedida
    $_SESSION['admin_id'] = $usuario['id'];
    $_SESSION['admin_nome'] = $usuario['nome'];
    $_SESSION['role'] = $usuario['role']; // Adicionar o role na sessão
    echo "Usuário logado com sucesso!";
    header("Location: dashboard.php");
    exit;
} else {
    // Definir mensagem de erro e redirecionar de volta para a página de login
    $_SESSION['erro'] = "Email ou senha incorretos, ou você não tem permissão de administrador.";
    header("Location: login.php");
    exit;
}

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="..//assets/css/style.css"> 
</head>
<body style="background-color: #333; color: #fff;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Login Administrador</h2>

                <!-- Exibir a mensagem de erro, se existir -->
                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($_SESSION['erro']) ?>
                    </div>
                    <?php unset($_SESSION['erro']); // Limpar a mensagem de erro após exibi-la ?>
                <?php endif; ?>

                <!-- Formulário de Login -->
                <form method="post" action="login.php" class="bg-dark p-4 rounded">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <input type="submit" value="Login" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include "./includes/footer.php" ?>