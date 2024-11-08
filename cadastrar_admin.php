<?php
include 'includes/db_connect.php';

// Defina os dados do primeiro administrador
$nome = 'daniel';
$email = 'email@email.com';
$senha = '0389';  // A senha pode ser criptografada
$telefone = '123';
$cpf = '123';
$role = 'admin';


// Hash da senha (utilize sempre hashing para senhas)
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Verificar se já existe um administrador cadastrado
    $sql_verifica = "SELECT * FROM usuarios WHERE role = 'admin'";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->execute();
    
    /* if ($stmt_verifica->rowCount() > 0) {
        echo "Já existe um administrador cadastrado. Remova este arquivo por questões de segurança.";
        exit;
    } */

    // Inserir o administrador
    $sql = "INSERT INTO usuarios (nome, cpf, telefone, email, senha, role) VALUES (?, ?, ?, ?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $cpf, $telefone, $email, $senha_hash, $role]);

    echo "Usuário cadastrado com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao cadastrar Usuário: " . $e->getMessage();
}

// Fechar a conexão
$conn = null;
?>
