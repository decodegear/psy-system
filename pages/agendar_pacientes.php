<?php
session_start();
// Verificar se o administrador está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php';
?>

<h1 class="form-title">Agendar Paciente</h1>
<form action="../actions/agendar_paciente.php" method="post" class="standard-form">
    <!-- Nome do Paciente -->
    <div class="form-group">
        <label for="nome_paciente">Nome do Paciente</label>
        <input type="text" id="nome_paciente" name="nome_paciente" class="form-control" required>
    </div>

    <!-- Data do Agendamento -->
    <div class="form-group">
        <label for="data_agendamento">Data do Agendamento</label>
        <input type="date" id="data_agendamento" name="data_agendamento" class="form-control" required>
    </div>

    <!-- Hora do Agendamento -->
    <div class="form-group">
        <label for="hora_agendamento">Hora do Agendamento</label>
        <input type="time" id="hora_agendamento" name="hora_agendamento" class="form-control" required>
    </div>

    <!-- Observações -->
    <div class="form-group">
        <label for="descricao">Observações</label>
        <textarea id="descricao" name="descricao" class="form-control" rows="4" style="resize: none;"></textarea>
    </div>

    <!-- Botão de Submissão -->
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </div>
</form>

<?php
// Inclui o rodapé
include '../includes/footer.php';
?>