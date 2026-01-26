<?php
include '../includes/db_connect.php';

// Sanitização e validação de entrada
$nome_paciente = trim(filter_input(INPUT_POST, 'nome_paciente', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$data_agendamento = trim(filter_input(INPUT_POST, 'data_agendamento', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$hora_agendamento = trim(filter_input(INPUT_POST, 'hora_agendamento', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$observacoes = trim(filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');

if ($nome_paciente === '' || $data_agendamento === '' || $hora_agendamento === '') {
    header("Location:../pages/agendar_pacientes.php?status=invalid");
    exit;
}

try {
    // Inserir agendamento
    $sql = "INSERT INTO agendamentos (nome_paciente, data_agendamento, hora_agendamento, observacoes) VALUES (?, ?, ?, ?)";
    if (!($conn instanceof PDO)) {
        throw new RuntimeException('Conexão com o banco de dados não inicializada.');
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome_paciente, $data_agendamento, $hora_agendamento, $observacoes]);

    header("Location:../pages/visualizar_agendamentos.php?status=success");
    exit;
} catch (Throwable $e) {
    error_log("Erro ao inserir agendamento: " . $e->getMessage());
    header("Location:../pages/agendar_pacientes.php?status=error");
    exit;
}
