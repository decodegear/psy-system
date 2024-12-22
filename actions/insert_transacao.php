<?php
include '../functions/auth.php';
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    if (!validate_csrf($_POST['csrf_token'])) {
        die("CSRF token inválido.");
    }

    // Obtenha os dados do formulário
    $stmt = $conn->prepare("INSERT INTO transacoes (tipo, nome, valor, data_vencimento, situacao, categoria_id, conta_id, descricao, parcelado, qtd_parcelas, data_inclusao) 
                            VALUES (:tipo, :nome, :valor, :data_vencimento, :situacao, :categoria_id, :conta_id, :descricao, :parcelado, :qtd_parcelas, :data_inclusao)");
    $stmt->execute([
        ':tipo' => $_POST['tipo'],
        ':nome' => $_POST['nome'],
        ':valor' => $_POST['valor'],
        ':data_vencimento' => $_POST['data_vencimento'],
        ':situacao' => $_POST['situacao'],
        ':categoria_id' => $_POST['categoria_id'],
        ':conta_id' => $_POST['conta_id'],
        ':descricao' => $_POST['descricao'],
        ':parcelado' => isset($_POST['parcelado']) ? 1 : 0,
        ':qtd_parcelas' => isset($_POST['parcelado']) ? $_POST['qtd_parcelas'] : null,
        ':data_inclusao' => date('Y-m-d')
    ]);

    header("Location: ../views/visualizar_transacao.php?tipo=" . urlencode($_POST['tipo']));
    exit;
}
?>