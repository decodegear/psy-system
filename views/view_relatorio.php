<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

include '../includes/header.php';

// Definir o mês atual por padrão
$data_inicio_padrao = date('Y-m-01');  // Primeiro dia do mês atual
$data_fim_padrao = date('Y-m-t');  // Último dia do mês atual
?>

<h1>Relatório de Receitas e Despesas por Período</h1>

<form method="post" action="view_relatorio.php">
    <!-- Selecionar data de início -->
    <label for="data_inicio">Data de Início:</label>
    <input type="date" id="data_inicio" name="data_inicio" value="<?= isset($_POST['data_inicio']) ? $_POST['data_inicio'] : $data_inicio_padrao; ?>" required>

    <!-- Selecionar data de fim -->
    <label for="data_fim">Data de Fim:</label>
    <input type="date" id="data_fim" name="data_fim" value="<?= isset($_POST['data_fim']) ? $_POST['data_fim'] : $data_fim_padrao; ?>" required>

    <!-- Seleção de situação -->
    <label for="situacao">Situação:</label>
    <select id="situacao" name="situacao">
        <option value="todas" <?= (isset($_POST['situacao']) && $_POST['situacao'] == 'todas') ? 'selected' : ''; ?>>Todas</option>
        <option value="Pago" <?= (isset($_POST['situacao']) && $_POST['situacao'] == 'Pago') ? 'selected' : ''; ?>>Pagas</option>
        <option value="A pagar" <?= (isset($_POST['situacao']) && $_POST['situacao'] == 'A pagar') ? 'selected' : ''; ?>>A Pagar</option>
    </select>

    <!-- Caixas de seleção para receitas/despesas -->
    <label><input type="checkbox" name="tipo[]" value="Receita" <?= (isset($_POST['tipo']) && in_array('Receita', $_POST['tipo'])) ? 'checked' : ''; ?>> Receitas</label>
    <label><input type="checkbox" name="tipo[]" value="Despesa" <?= (isset($_POST['tipo']) && in_array('Despesa', $_POST['tipo'])) ? 'checked' : ''; ?>> Despesas</label>

    <!-- Botão para gerar o relatório -->
    <input type="submit" value="Gerar Relatório">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar as datas selecionadas
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $situacao = $_POST['situacao']; // Situação: Pagas / A Pagar / Todas

    // Verificar os tipos selecionados
    $tipos = isset($_POST['tipo']) ? $_POST['tipo'] : [];

    // Validar as datas
    if (empty($data_inicio) || empty($data_fim)) {
        echo "Por favor, selecione um período válido.";
        exit;
    }

    // Validar os tipos (Receita, Despesa, ou ambos)
    if (empty($tipos)) {
        echo "Por favor, selecione pelo menos um tipo (Receitas, Despesas ou ambos).";
        exit;
    }

    // Incluir a conexão com o banco de dados
    include '../includes/db_connect.php';

    // Arrays para armazenar receitas e despesas
    $receitas = [];
    $despesas = [];

    // Filtro de situação
    $filtro_situacao = "";
    if ($situacao == 'Pago') {
        $filtro_situacao = " AND situacao = 'Pago'";
    } elseif ($situacao == 'A pagar') {
        $filtro_situacao = " AND situacao = 'A Pagar'";
    }

    // Exibir o filtro para depuração
    //echo "<pre>Filtro de Situação: $filtro_situacao</pre>";

    // Se o usuário selecionou "Receitas"
    if (in_array('Receita', $tipos)) {
        // Buscar as receitas no período
        $sql_receitas = "SELECT tipo, nome, descricao, valor, data_vencimento, situacao FROM transacoes WHERE data_vencimento BETWEEN ? AND ? AND tipo = 'Receita'" . $filtro_situacao;
        $stmt_receitas = $conn->prepare($sql_receitas);
        $stmt_receitas->execute([$data_inicio, $data_fim]);
        $receitas = $stmt_receitas->fetchAll(PDO::FETCH_ASSOC);
    }

    // Se o usuário selecionou "Despesas"
    if (in_array('Despesa', $tipos)) {
        // Buscar as despesas no período
        $sql_despesas = "SELECT tipo, nome, descricao, valor, data_vencimento, situacao FROM transacoes WHERE data_vencimento BETWEEN ? AND ? AND tipo = 'Despesa'" . $filtro_situacao;
        $stmt_despesas = $conn->prepare($sql_despesas);
        $stmt_despesas->execute([$data_inicio, $data_fim]);
        $despesas = $stmt_despesas->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calcular os totais de receitas e despesas
    $total_receitas = 0;
    $total_despesas = 0;

    foreach ($receitas as $receita) {
        $total_receitas += $receita['valor'];
    }

    foreach ($despesas as $despesa) {
        $total_despesas += $despesa['valor'];
    }

    // Calcular o saldo final (receitas - despesas)
    $saldo = $total_receitas - $total_despesas;
?>

    <!-- Exibir o relatório -->
    <h2>Relatório de <?= date("d/m/Y", strtotime($data_inicio)) ?> a <?= date("d/m/Y", strtotime($data_fim)) ?></h2>

    <?php if (!empty($receitas)): ?>
        <h3>Receitas</h3>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Data de Vencimento</th>
                    <th>Situação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receitas as $receita): ?>
                    <tr>
                        <td><?= htmlspecialchars($receita['tipo']); ?></td>
                        <td><?= htmlspecialchars($receita['nome']); ?></td>
                        <td><?= htmlspecialchars($receita['descricao']); ?></td>
                        <td>R$ <?= number_format($receita['valor'], 2, ',', '.'); ?></td>
                        <td><?= date("d/m/Y", strtotime($receita['data_vencimento'])); ?></td>
                        <td><?= htmlspecialchars($receita['situacao']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($despesas)): ?>
        <h3>Despesas</h3>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Data de Vencimento</th>
                    <th>Situação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($despesas as $despesa): ?>
                    <tr>
                        <td><?= htmlspecialchars($despesa['tipo']); ?></td>
                        <td><?= htmlspecialchars($despesa['nome']); ?></td>
                        <td><?= htmlspecialchars($despesa['descricao']); ?></td>
                        <td>R$ <?= number_format($despesa['valor'], 2, ',', '.'); ?></td>
                        <td><?= date("d/m/Y", strtotime($despesa['data_vencimento'])); ?></td>
                        <td><?= htmlspecialchars($despesa['situacao']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>Resumo</h3>
    <p>Total de Receitas: <strong>R$ <?= number_format($total_receitas, 2, ',', '.'); ?></strong></p>
    <p>Total de Despesas: <strong>R$ <?= number_format($total_despesas, 2, ',', '.'); ?></strong></p>
    <p>Saldo Final: <strong>R$ <?= number_format($saldo, 2, ',', '.'); ?></strong></p>

<?php
}
?>

<?php
include '../includes/footer.php';
?>