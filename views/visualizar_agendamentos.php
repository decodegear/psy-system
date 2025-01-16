<?php session_start();
$isLoggedIn = isset($_SESSION['admin_id']); ?>
<?php

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
include '../includes/header.php';
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<div class="container my-4">
    <h1>Visualizar Agendamentos</h1>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Paciente</th>
                <th>Horário</th>
                <th class="col-observacoes">Observações</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($agendamentos)): ?>
                <?php foreach ($agendamentos as $agendamento): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                        <td><?= htmlspecialchars($agendamento['nome_paciente']) ?></td>
                        <td><?= htmlspecialchars($agendamento['hora_agendamento']) ?></td>
                        <td class="col-observacoes"><?= htmlspecialchars($agendamento['observacoes']) ?></td>
                        <td>
                            <?php if ($isLoggedIn) { ?><a href="../actions/altera_agendamento.php?id=<?= $agendamento['id'] ?>" class="bi bi-pencil-square" style="color: orange;"></a> <?php } ?>
                            <?php if ($isLoggedIn) { ?><a href="../actions/excluir_agendamento.php?id=<?= $agendamento['id'] ?>" class="bi bi-trash3" style="color: red;" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');"></a><?php } ?>
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