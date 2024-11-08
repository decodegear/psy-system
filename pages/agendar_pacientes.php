<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
?>

<h1>Agendar Pacientes</h1>

<form action="..//actions/agendar_paciente.php" method="post">
    <!-- Nome do Paciente -->
    <label for="nome_paciente">Nome do Paciente:</label>
    <input type="text" id="nome_paciente" name="nome_paciente" required>
    
    <!-- Data do Agendamento -->
    <label for="data_agendamento">Data do Agendamento:</label>
    <input type="date" id="data_agendamento" name="data_agendamento" required>
    
    <!-- Hora do Agendamento -->
    <label for="hora_agendamento">Hora do Agendamento:</label>
    <input type="time" id="hora_agendamento" name="hora_agendamento" required>
<br><br>
    <!-- Observações -->
    <label for="observacoes">Observações:<br></label>
    <textarea id="observacoes" style="width: 534px;height: 120px;" name="observacoes"></textarea>

    <!-- Botão de Submissão -->
    <input type="submit" value="Agendar">
</form>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
