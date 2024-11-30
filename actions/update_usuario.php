<?php
include '../includes/db_connect.php';

$id = $_POST['id'];
$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
$telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);
$cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'];

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Atualizar usuário
    
    $sql = "UPDATE usuarios SET nome = ?, telefone = ?, cpf = ?, email = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $telefone, $cpf, $email, $senha_hash, $id]);

    header("Location: ../pages/visualizar_usuarios.php?status=updated");
} catch (PDOException $e) {
    error_log("Erro ao atualizar usuário: " . $e->getMessage());
    header("Location: ../pages/editar_usuario.php?id=$id&status=error");
}
?>
