<?php
require_once 'cpf_email_validation.php';

require_once '../includes/db_connect.php';
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Preparando a query
    $sql = "INSERT INTO pessoas (nome, genero, idade, peso, altura, etnia, rg, cpf, cnh, nasc) 
            VALUES (:nome, :genero, :idade, :peso, :altura, :etnia, :rg, :cpf, :cnh, :nasc)";

    $stmt = $pdo->prepare($sql);

    // Associando os valores com bindParam/bindValue
    $stmt->bindValue(':nome', trim($_POST['nome']), PDO::PARAM_STR);
    $stmt->bindValue(':genero', trim($_POST['genero']), PDO::PARAM_STR);
    $stmt->bindValue(':idade', (int)$_POST['idade'], PDO::PARAM_INT);
    $stmt->bindValue(':peso', (float)$_POST['peso'], PDO::PARAM_STR);
    $stmt->bindValue(':altura', (int)$_POST['altura'], PDO::PARAM_INT);
    $stmt->bindValue(':etnia', trim($_POST['etnia']), PDO::PARAM_STR);
    $stmt->bindValue(':rg', trim($_POST['rg']), PDO::PARAM_STR);
    $stmt->bindValue(':cpf', trim($_POST['cpf']), PDO::PARAM_STR);
    $stmt->bindValue(':cnh', trim($_POST['cnh']), PDO::PARAM_STR);
    $stmt->bindValue(':nasc', trim($_POST['nasc']), PDO::PARAM_STR);

    // Executando a query
    $stmt->execute();

    header("Location:../views/pessoa/visualizar_pessoas.php?status=success");
} catch (PDOException $e) {
    error_log("Erro ao inserir pessoa: " . $e->getMessage());
    header("Location:../pages/cadastro_pessoa.php?status=error");
}

?>
