<?php session_start(); $isLoggedIn = isset($_SESSION['admin_id']); ?>
<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
// Iniciar a sessão e verificar se o usuário é administrador
//session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Conectar ao banco de dados
include '../includes/db_connect.php';

// Verifica se o tipo foi passado via GET (URL)
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'despesas'; // Padrão 'despesas' se não for passado

try {
    // Consulta com base no tipo
    if ($tipo === 'despesa') {
        $sql = "SELECT id, nome, descricao, valor, data_vencimento, data_inclusao, situacao, parcelado, qtd_parcelas, 'despesa' AS tipo FROM transacoes WHERE tipo = 'despesa' ORDER BY data_vencimento DESC";
    } elseif ($tipo === 'receita') {
        $sql = "SELECT id, nome, descricao, valor, data_vencimento, data_inclusao, situacao, parcelado, qtd_parcelas, 'receita' AS tipo FROM transacoes WHERE tipo = 'receita' ORDER BY data_vencimento DESC";
    } else { // Caso 'ambos'
        $sql = "SELECT id, nome, descricao, valor, data_vencimento, data_inclusao, situacao, parcelado, qtd_parcelas, tipo FROM transacoes ORDER BY data_vencimento DESC";
    }

    // Executa a consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao consultar: " . $e->getMessage());
    echo "Erro ao carregar a lista.";
}
?>



<div class="container my-4">
    
<h1>Visualizar <?= ucfirst($tipo) ?></h1>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Tipo</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Data de Vencimento</th>
                <th>Data de Inclusão</th>
                <th>Situação</th>
                <th>Parcelado</th>
                <th>Parcelas</th>
                <?php if ($isAdmin): ?>
                <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultados)): ?>
                <?php foreach ($resultados as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['tipo']) ?></td>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td><?= htmlspecialchars($item['descricao']) ?></td>
                        <td><?= htmlspecialchars(number_format($item['valor'], 2, ',', '.')) ?></td>
                        <td><?= htmlspecialchars($item['data_vencimento']) ?></td>
                        <td><?= htmlspecialchars($item['data_inclusao']) ?></td>
                        <td><?= htmlspecialchars($item['situacao']) ?></td>
                        <td><?= $item['parcelado'] ? 'Sim' : 'Não' ?></td>
                        <td><?= $item['parcelado'] ? htmlspecialchars($item['qtd_parcelas']) : 'N/A' ?></td>
                        <?php if ($isAdmin): ?>
                            <td>
                                <a href="../pages/editar_<?= $item['tipo'] ?>.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">Alterar</a><?php ?>
                                <a href="../actions/excluir_<?= $item['tipo'] ?>.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a><?php ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">Nenhum item cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
