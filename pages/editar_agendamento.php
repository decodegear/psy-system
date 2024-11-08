<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Buscar dados do agendamento para edição
    $sql = "SELECT nome_paciente, data_agendamento, hora_agendamento, observacoes FROM agendamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$agendamento) {
        die("Agendamento não encontrado.");
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do agendamento: " . $e->getMessage());
    echo "Erro ao carregar dados do agendamento.";
}
?>

<h1>Editar Agendamento</h1>

<form action="/actions/update_agendamento.php" method="post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- Nome do Paciente -->
    <label for="nome_paciente">Nome do Paciente:</label>
    <input type="text" id="nome_paciente" name="nome_paciente" value="<?= htmlspecialchars($agendamento['nome_paciente']) ?>" required>
    
    <!-- Data do Agendamento -->
    <label for="data_agendamento">Data do Agendamento:</label>
    <input type="date" id="data_agendamento" name="data_agendamento" value="<?= htmlspecialchars($agendamento['data_agendamento']) ?>" required>
    
    <!-- Hora do Agendamento -->
    <label for="hora_agendamento">Hora do Agendamento:</label>
    <input type="time" id="hora_agendamento" name="hora_agendamento" value="<?= htmlspecialchars($agendamento['hora_agendamento']) ?>" required>

    <!-- Observações -->
    <label for="observacoes">Observações:</label>
    <textarea id="observacoes" name="observacoes"><?= htmlspecialchars($agendamento['observacoes']) ?></textarea>

    <!-- Botão de Submissão -->
    <input type="submit" value="Atualizar Agendamento">
</form>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>

