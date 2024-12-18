<?php

// Incluir dependências necessárias
include '../includes/header.php';
include '../includes/db_connect.php';
include '../functions/validation.php';


// Inicialização de variáveis
$action = $_GET['action'] ?? 'view'; // Determina a ação: 'insert', 'edit', 'update', 'delete'
$tipo = $_GET['tipo'] ?? 'despesa'; // Tipo padrão: despesa
$id = $_GET['id'] ?? null;

// Token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF token inválido.");
}

// Operações baseadas na ação
if ($action === 'insert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inserção de nova transação
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $data_vencimento = $_POST['data_vencimento'];
    $situacao = $_POST['situacao'];
    $categoria_id = $_POST['categoria_id'];
    $conta_id = $_POST['conta_id'];
    $descricao = $_POST['descricao'];
    $parcelado = isset($_POST['parcelado']) ? 1 : 0;
    $qtd_parcelas = $parcelado ? ($_POST['qtd_parcelas'] ?? 1) : null;
    $data_inclusao = date('Y-m-d');

   
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
    header("Location: /pages/visualizar_transacao.php?tipo=$tipo");
    exit;
 
} if ($action === 'edit' && $id) {
    try {
        $sql = "SELECT * FROM transacoes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $transacao = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$transacao) {
            die("Transação não encontrada.");
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados da transação: " . $e->getMessage());
        die("Erro ao carregar a transação.");
    }
}

 elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    
    // Atualizar transação existente
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];
    $data_vencimento = $_POST['data_vencimento'];
    $situacao = $_POST['situacao'];
    $categoria_id = $_POST['categoria_id'];
    $conta_id = $_POST['conta_id'];
    $descricao = $_POST['descricao'];
    $parcelado = isset($_POST['parcelado']) ? 1 : 0;
    $qtd_parcelas = $parcelado ? $_POST['qtd_parcelas'] : 1;

    $sql = "UPDATE transacoes SET nome = ?, valor = ?, data_vencimento = ?, situacao = ?, categoria_id = ?, conta_id = ?, descricao = ?, parcelado = ?, qtd_parcelas = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome, $valor, $data_vencimento, $situacao, $categoria_id, $conta_id, $descricao, $parcelado, $qtd_parcelas, $id]);
    header("Location: /pages/visualizar_transacao.php?tipo=$tipo");
    exit;

} elseif ($action === 'delete' && $id) {
    
    // Exclusão de transação
    $sql = "DELETE FROM transacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    header("Location: /pages/visualizar_transacao.php?tipo=$tipo&status=deleted");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Transação</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">

    

    <?php if ($action === 'edit' || $action === 'insert'): ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="id" value="<?= $id ?? ''; ?>">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo); ?>">

            <!-- Nome -->
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?= $transacao['nome'] ?? ''; ?>" class="form-control" required>

            <!-- Valor -->
            <label for="valor">Valor:</label>
            <input type="number" name="valor" id="valor" value="<?= $transacao['valor'] ?? ''; ?>" class="form-control" step="0.01" required>

            <!-- Data de Vencimento -->
            <label for="data_vencimento">Data de Vencimento:</label>
            <input type="date" name="data_vencimento" id="data_vencimento" value="<?= $transacao['data_vencimento'] ?? ''; ?>" class="form-control" required>

            <!-- Situação -->
            <label for="situacao">Situação:</label>
            <select name="situacao" id="situacao" class="form-select" required>
                <option value="Pago" <?= ($transacao['situacao'] ?? '') === 'Pago' ? 'selected' : ''; ?>>Pago</option>
                <option value="A pagar" <?= ($transacao['situacao'] ?? '') === 'A pagar' ? 'selected' : ''; ?>>A pagar</option>
            </select>

            <!-- Categoria -->
            <label for="categoria_id">Categoria:</label>
            <input type="number" name="categoria_id" id="categoria_id" value="<?= $transacao['categoria_id'] ?? ''; ?>" class="form-control" required>

            <!-- Conta -->
            <label for="conta_id">Conta:</label>
            <input type="number" name="conta_id" id="conta_id" value="<?= $transacao['conta_id'] ?? ''; ?>" class="form-control" required>

            <!-- Descrição -->
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" class="form-control"><?= $transacao['descricao'] ?? ''; ?></textarea>

            <!-- Parcelamento -->
            <label for="parcelado">Parcelado:</label>
            <input type="checkbox" name="parcelado" id="parcelado" value="1" <?= ($transacao['parcelado'] ?? 0) ? 'checked' : ''; ?>>
            <label for="qtd_parcelas">Quantidade de Parcelas:</label>
            <input type="number" name="qtd_parcelas" id="qtd_parcelas" value="<?= $transacao['qtd_parcelas'] ?? 1; ?>" class="form-control">

            <button type="submit" class="btn btn-primary mt-3">Salvar <?=$tipo;?></button>
        </form>
    <?php endif; ?>
</body>
</html>
