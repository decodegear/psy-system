<?php
require_once 'cpf_email_validation.php';

include '../includes/db_connect.php';

// Sanitização e validação de entrada
$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
$senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
$telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);
$cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$role = filter_var($_POST['role'], FILTER_SANITIZE_STRING); // Adicionando o campo 'role'

try {
    // Inserir o usuário com o campo 'role'
    $sql = "INSERT INTO usuarios (nome, senha, telefone, cpf, email, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $senha, $telefone, $cpf, $email, $role]);
echo "isert_user";
    header("Location: ../views/visualizar_usuarios.php?status=success");
} catch (PDOException $e) {
    error_log("Erro ao inserir usuário: " . $e->getMessage());
    header("Location: ../pages/cadastro_usuario.php?status=error");
}
?>
