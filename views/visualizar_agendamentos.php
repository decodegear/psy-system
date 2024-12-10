<?php session_start(); $isLoggedIn = isset($_SESSION['admin_id']); ?>
<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 

// Conectar ao banco de dados
include '../includes/db_connect.php';

try {
    // Consultar todos os agendamentos
    $sql = "SELECT id, nome_paciente, data_agendamento, hora_agendamento, observacoes FROM agendamentos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao consultar agendamentos: " . $e->getMessage());
    echo "Erro ao carregar a lista de agendamentos.";
}
?>

<div class="container my-4">
    <h1>Visualizar Agendamentos</h1>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nome do Paciente</th>
                <th>Data do Agendamento</th>
                <th>Hora do Agendamento</th>
                <th>Observações</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($agendamentos)): ?>
                <?php foreach ($agendamentos as $agendamento): ?>
                    <tr>
                        <td><?= htmlspecialchars($agendamento['nome_paciente']) ?></td>
                        <td><?= htmlspecialchars($agendamento['data_agendamento']) ?></td>
                        <td><?= htmlspecialchars($agendamento['hora_agendamento']) ?></td>
                        <td><?= htmlspecialchars($agendamento['observacoes']) ?></td>
                        <td>
                            <?php if ($isLoggedIn) { ?><a href="../pages/editar_agendamento.php?id=<?= $agendamento['id'] ?>" class="btn btn-warning btn-sm">Alterar</a><?php } ?>
                            <a href="../actions/excluir_agendamento.php?id=<?= $agendamento['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">Excluir</a><?php ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Nenhum agendamento cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>