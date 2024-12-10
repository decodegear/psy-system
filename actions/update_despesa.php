<?php
// Inclui a conexão com o banco de dados
include '../includes/db_connect.php';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'despesa';
// Coletar os dados do formulário
$id = $_POST['id'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$data_vencimento = $_POST['data_vencimento'];
$situacao = $_POST['situacao'];
$parcelado = isset($_POST['parcelado']) ? 1 : 0;
$qtd_parcelas = $parcelado ? $_POST['qtd_parcelas'] : null;
$categoria_id = $_POST['categoria_id'];
$conta_id = $_POST['conta_id'];

try {
    // Atualizar a despesa no banco de dados
    $sql = "UPDATE transacoes 
            SET nome = ?, descricao = ?, valor = ?, data_vencimento = ?, situacao = ?, parcelado = ?, qtd_parcelas = ?, categoria_id = ?, conta_id = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $descricao, $valor, $data_vencimento, $situacao, $parcelado, $qtd_parcelas, $categoria_id, $conta_id, $id]);

    echo "Despesa atualizada com sucesso!";
    // Redirecionar ou exibir mensagem de sucesso
    header("Location: ../views/visualizar_transacao.php?tipo=$tipo");
    exit;
} catch (PDOException $e) {
    error_log("Erro ao atualizar despesa: " . $e->getMessage());
    echo "Erro ao atualizar despesa.";
}

// Fechar a conexão com o banco de dados
$conn = null;
?>
