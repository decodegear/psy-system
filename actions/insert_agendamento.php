<?php
include '../includes/db_connect.php';

// Sanitização e validação de entrada
$nome_paciente = filter_var($_POST['nome_paciente'], FILTER_SANITIZE_STRING);
$data_agendamento = $_POST['data_agendamento'];
$hora_agendamento = $_POST['hora_agendamento'];
$observacoes = filter_var($_POST['observacoes'], FILTER_SANITIZE_STRING);

try {
    // Inserir agendamento
    $sql = "INSERT INTO agendamentos (nome_paciente, data_agendamento, hora_agendamento, observacoes) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome_paciente, $data_agendamento, $hora_agendamento, $observacoes]);

    header("Location: ..//pages/visualizar_agendadmentos.php?status=success");
} catch (PDOException $e) {
    error_log("Erro ao inserir agendamento: " . $e->getMessage());
    header("Location: ..//pages/agendar_pacientes.php?status=error");
}
?>

