<?php
include '../includes/db_connect.php';

// Captura segura dos valores enviados por POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING);
$idade = filter_input(INPUT_POST, 'idade', FILTER_VALIDATE_INT);
$peso = filter_input(INPUT_POST, 'peso', FILTER_VALIDATE_FLOAT);
$altura = filter_input(INPUT_POST, 'altura', FILTER_VALIDATE_INT);
$etnia = filter_input(INPUT_POST, 'etnia', FILTER_SANITIZE_STRING);
$rg = filter_input(INPUT_POST, 'rg', FILTER_SANITIZE_STRING);
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
$cnh = filter_input(INPUT_POST, 'cnh', FILTER_SANITIZE_STRING);
$nasc = filter_input(INPUT_POST, 'nasc', FILTER_SANITIZE_STRING);

if ($id === false) {
    // Verificar se o ID é inválido
    header("Location: ../pages/visualizar_pessoa.php?status=invalid_id");
    exit;
}

try {
    // Preparar a consulta para atualizar os dados da pessoa
    $sql = "UPDATE pessoas 
            SET nome = :nome, genero = :genero, idade = :idade, peso = :peso, altura = :altura, etnia = :etnia, rg = :rg, cpf = :cpf, cnh = :cnh, data_nasc = :data_nasc 
            WHERE id = :id";

    $stmt = $conn->prepare($sql);

    // Executar a query, passando os parâmetros corretamente
    $stmt->execute([
        ':nome' => $nome ?? '',
        ':genero' => $genero ?? '',
        ':idade' => $idade !== false ? $idade : null,
        ':peso' => $peso !== false ? $peso : null,
        ':altura' => $altura !== false ? $altura : null,
        ':etnia' => $etnia ?? '',
        ':rg' => $rg ?? '',
        ':cpf' => $cpf ?? '',
        ':cnh' => $cnh ?? '',
        ':data_nasc' => !empty($nasc) ? $nasc : null,
        ':id' => $id
    ]);

    // Redirecionar para a página de visualização após o cadastro bem-sucedido
    header("Location: ../pages/visualizar_pessoa.php?status=updated");
    exit;

} catch (PDOException $e) {
    error_log("Erro ao atualizar pessoa: " . $e->getMessage());
    // Redirecionar para a página de edição com uma mensagem de erro
    header("Location: ../pages/editar_pessoa.php?id=$id&status=error");
    exit;
}
?>
