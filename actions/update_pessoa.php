<?php
include '../includes/db_connect.php';

$id = $_POST['id'];
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
    // Atualizar pessoa
    $sql = "UPDATE pessoas SET nome = ?, genero = ?, idade = ?, peso = ?, altura = ?, etnia = ?, rg = ?, cpf = ?, cnh = ?, data_nasc = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $genero, $idade, $peso, $altura, $etnia, $rg, $cpf, $cnh, $nasc, $id]);

    header("Location: ../pages/visualizar_pessoas.php?status=updated");
} catch (PDOException $e) {
    error_log("Erro ao atualizar pessoa: " . $e->getMessage());
    header("Location: ../pages/editar_pessoa.php?id=$id&status=error");
}
?>
