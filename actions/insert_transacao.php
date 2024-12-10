<?php
include '../includes/db_connect.php';

$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'despesa'; // Modificado para usar POST
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica o token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token inválido.");
    }

    // Obter os dados do formulário
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $data_vencimento = $_POST['data_vencimento'];
    $situacao = $_POST['situacao'];
    $categoria_id = $_POST['categoria_id'];
    $conta_id = $_POST['conta_id'];
    $descricao = $_POST['descricao'];
    $parcelado = isset($_POST['parcelado']) ? 1 : 0;
    $qtd_parcelas = $_POST['qtd_parcelas'];
    $data_inclusao = $_POST['data_inclusao'];

    // Preparar a inserção no banco
    $sql = "INSERT INTO transacoes (tipo, nome, valor, data_vencimento, situacao, categoria_id, conta_id, descricao, parcelado, qtd_parcelas, data_inclusao) 
            VALUES (:tipo, :nome, :valor, :data_vencimento, :situacao, :categoria_id, :conta_id, :descricao, :parcelado, :qtd_parcelas, :data_inclusao)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo,
        ':nome' => $nome,
        ':valor' => $valor,
        ':data_vencimento' => $data_vencimento,
        ':situacao' => $situacao,
        ':categoria_id' => $categoria_id,
        ':conta_id' => $conta_id,
        ':descricao' => $descricao,
        ':parcelado' => $parcelado,
        ':qtd_parcelas' => $qtd_parcelas,
        ':data_inclusao' => $data_inclusao
    ]);

    // Redirecionar ou exibir mensagem de sucesso
    header("Location: ../views/visualizar_transacao.php?tipo=$tipo");
    exit;
}
?>
