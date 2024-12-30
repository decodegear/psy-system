<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gerar o token CSRF, se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Captura de parâmetros
$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null; // Capturar o tipo da transação
$action = $_GET['action'] ?? 'edit';

if (!$id) {
    die("ID inválido.");
}
    include '../includes/db_connect.php';
    include '../functions/validation.php';

try {
    // Buscar transação
    $sql = "SELECT * FROM transacoes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $transacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transacao) {
        die("Transação não encontrada.");
    }

    // Atualizar o tipo com base nos dados encontrados
    $tipo = $transacao['tipo'] ?? $tipo;

    // Buscar categorias
    $sqlCategorias = "SELECT id, nome FROM categorias";
    $categorias = $conn->query($sqlCategorias)->fetchAll(PDO::FETCH_ASSOC);

    // Buscar contas
    $sqlContas = "SELECT id, nome FROM contas";
    $contas = $conn->query($sqlContas)->fetchAll(PDO::FETCH_ASSOC);

    if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar token CSRF
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            die("CSRF token inválido ou ausente.");
        }

        // Captura de dados do formulário
        $nome = $_POST['nome'] ?? null;
        $valor = $_POST['valor'] ?? null;
        $data_vencimento = $_POST['data_vencimento'] ?? null;
        $situacao = $_POST['situacao'] ?? null;
        $categoria_id = $_POST['categoria_id'] ?? null;
        $conta_id = $_POST['conta_id'] ?? null;
        $descricao = $_POST['descricao'] ?? null;
        $parcelado = isset($_POST['parcelado']) ? 1 : 0;
        $qtd_parcelas = $parcelado ? ($_POST['qtd_parcelas'] ?? 1) : 1;

        // Verificar campos obrigatórios
        if (!$nome || !$valor || !$data_vencimento || !$situacao || !$categoria_id || !$conta_id) {
            die("Por favor, preencha todos os campos obrigatórios.");
        }
    
        // Atualizar no banco
        $sql = "UPDATE transacoes 
                SET nome = ?, valor = ?, data_vencimento = ?, situacao = ?, categoria_id = ?, conta_id = ?, descricao = ?, parcelado = ?, qtd_parcelas = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $valor, $data_vencimento, $situacao, $categoria_id, $conta_id, $descricao, $parcelado, $qtd_parcelas, $id]);

        // Redirecionar após a atualização
        header("Location: ../views/visualizar_transacao.php?tipo=" . urlencode($tipo) . "&status=updated");
        //exit;
       exit;
    }
} catch (PDOException $e) {
    error_log("Erro ao processar transação: " . $e->getMessage());
    echo "Erro ao processar transação: " . htmlspecialchars($e->getMessage());
    exit;
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Transação</title>
    
</head>
<body class="container">
    <h1>Alterar <?= $tipo ?></h1>
    <?php if ($action === 'edit' && isset($transacao)): ?>
        <div class="registration-form form-group">
            <form method="post" action="altera_transacao.php?action=update&id=<?= htmlspecialchars($id ?? ''); ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($transacao['nome'] ?? ''); ?>" class="" required>

                <label for="valor">Valor:</label>
                <input type="number" name="valor" id="valor" value="<?= htmlspecialchars($transacao['valor'] ?? ''); ?>" class="" step="0.01" required>

                <label for="data_vencimento">Data de Vencimento:</label>
                <input type="date" name="data_vencimento" id="data_vencimento" value="<?= htmlspecialchars($transacao['data_vencimento'] ?? ''); ?>" class="" required>

                <label for="situacao">Situação:</label>
                <select name="situacao" id="situacao" class="select " required>
                    <option value="Pago" <?= ($transacao['situacao'] ?? '') === 'Pago' ? 'selected' : ''; ?>>Pago</option>
                    <option value="A pagar" <?= ($transacao['situacao'] ?? '') === 'A pagar' ? 'selected' : ''; ?>>A pagar</option>
                </select>

                <label for="categoria_id">Categoria:</label>
                <select name="categoria_id" id="categoria_id" class="select " required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['id']); ?>" <?= ($transacao['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($categoria['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="conta_id">Conta:</label>
                <select name="conta_id" id="conta_id" class="select " required>
                    <?php foreach ($contas as $conta): ?>
                        <option value="<?= htmlspecialchars($conta['id']); ?>" <?= ($transacao['conta_id'] ?? '') == $conta['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($conta['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" class=" full-width textarea-group"><?= htmlspecialchars($transacao['descricao'] ?? ''); ?></textarea>

                <label for="parcelado">Parcelado:</label>
                <input type="checkbox" name="parcelado" id="parcelado" value="1" <?= ($transacao['parcelado'] ?? 0) ? 'checked' : ''; ?>>

                <label for="qtd_parcelas">Quantidade de Parcelas:</label>
                <input type="number" name="qtd_parcelas" id="qtd_parcelas" value="<?= htmlspecialchars($transacao['qtd_parcelas'] ?? '1'); ?>" class="form-control item ">

                <button type="submit" class="btn-primary mt-3">Salvar Alteração em <?= $tipo ?></button>
        </div>
        </form>

    <?php else: ?>
        <p>Transação não encontrada ou ação inválida.</p>
    <?php endif; ?>

</body>

</html>