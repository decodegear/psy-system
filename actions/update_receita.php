<?php
include '../includes/db_connect.php';

$id = $_POST['id'];
$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
$descricao = filter_var($_POST['descricao'], FILTER_SANITIZE_STRING);
$valor = (float)$_POST['valor'];
$data_vencimento = $_POST['data_vencimento'];
$situacao = $_POST['situacao'];
$parcelado = isset($_POST['parcelado']) ? true : false;
$qtd_parcelas = $parcelado ? (int)$_POST['qtd_parcelas'] : 1;

try {
    // Atualizar receita principal
    $sql = "UPDATE receitas SET nome = ?, descricao = ?, valor = ?, data_vencimento = ?, situacao = ?, parcelado = ?, qtd_parcelas = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $descricao, $valor, $data_vencimento, $situacao, $parcelado, $qtd_parcelas, $id]);

    // Opcional: Atualizar parcelas se necessário...

    header("Location: ..//pages/visualizar_receitas.php?status=updated");
} catch (PDOException $e) {
    error_log("Erro ao atualizar receita: " . $e->getMessage());
    header("Location: ..//pages/editar_receita.php?id=$id&status=error");
}
?>

