<?php
session_start();
// Verificar se o administrador está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

include '../includes/db_connect.php';

$pacientes = [];
try {
    if ($conn instanceof PDO) {
        $stmt = $conn->prepare('SELECT nome FROM pessoas ORDER BY nome');
        $stmt->execute();
        $pacientes = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
} catch (Throwable $e) {
    $pacientes = [];
}

// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php';
?>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'success'): ?>
        <div class="alert-card">Agendamento criado com sucesso.</div>
    <?php elseif ($_GET['status'] === 'calendar_error'): ?>
        <div class="alert-card">Nao foi possivel criar o evento no Google Calendar. Reautorize e tente novamente.</div>
    <?php elseif ($_GET['status'] === 'error'): ?>
        <div class="alert-card">Erro ao salvar o agendamento. Tente novamente.</div>
    <?php endif; ?>
<?php endif; ?>

<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Agenda</p>
            <h1>Agendar Paciente</h1>
            <p class="page-subtitle">Crie atendimentos e sincronize com o Google Calendar.</p>
        </div>
    </section>

    <section class="table-card">
        <?php if (empty($pacientes)): ?>
            <div class="alert-card">Nenhum paciente cadastrado. Você pode digitar o nome manualmente.</div>
        <?php endif; ?>
        <h2>Criar Novo Agendamento</h2>
        <form action="../actions/agendar_paciente.php" method="post" class="form-grid">
            <div class="form-field">
                <label for="nome_paciente">Nome do Paciente</label>
                <input type="text" id="nome_paciente" name="nome_paciente" list="pacientes-list" required>
                <datalist id="pacientes-list">
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?= htmlspecialchars($paciente, ENT_QUOTES, 'UTF-8') ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-field">
                <label for="data_agendamento">Data do Agendamento</label>
                <input type="date" id="data_agendamento" name="data_agendamento" required>
            </div>

            <div class="form-field">
                <label for="hora_agendamento">Hora do Agendamento</label>
                <input type="time" id="hora_agendamento" name="hora_agendamento" required>
            </div>

            <input type="hidden" id="timezone_offset" name="timezone_offset" value="0">

            <div class="form-field form-field--full">
                <label for="descricao">Observações</label>
                <textarea id="descricao" name="descricao"></textarea>
            </div>

            <div class="form-field form-field--full">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </section>
</main>

<?php
// Inclui o rodapé
include '../includes/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tzField = document.getElementById('timezone_offset');
        if (tzField) {
            tzField.value = String(new Date().getTimezoneOffset());
        }
    });
</script>