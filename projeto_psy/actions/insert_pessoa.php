<?php
include '../includes/db_connect.php';

// Sanitização e validação de entrada
$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
$genero = $_POST['genero'];
$idade = (int)$_POST['idade'];
$peso = (float)$_POST['peso'];
$altura = (int)$_POST['altura'];
$etnia = filter_var($_POST['etnia'], FILTER_SANITIZE_STRING);
$rg = filter_var($_POST['rg'], FILTER_SANITIZE_STRING);
$cpf = filter_var($_POST['cpf'], FILTER_SANITIZE_STRING);
$cnh = filter_var($_POST['cnh'], FILTER_SANITIZE_STRING);
$nasc = $_POST['nasc'];

try {
    // Inserir pessoa
    $sql = "INSERT INTO pessoas (nome, genero, idade, peso, altura, etnia, rg, cpf, cnh, data_nasc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $genero, $idade, $peso, $altura, $etnia, $rg, $cpf, $cnh, $nasc]);

    header("Location: ../pages/visualizar_pessoas.php?status=success");
} catch (PDOException $e) {
    error_log("Erro ao inserir pessoa: " . $e->getMessage());
    header("Location: ../pages/cadastro_pessoa.php?status=error");
}

?>
