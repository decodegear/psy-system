<?php
include($_SERVER['DOCUMENT_ROOT'] . '../includes/db_connect.php');

$id = $_POST['id'];
$nome_paciente = filter_var($_POST['nome_paciente'], FILTER_SANITIZE_STRING);
$data_agendamento = $_POST['data_agendamento'];
$hora_agendamento = $_POST['hora_agendamento'];
$observacoes = filter_var($_POST['observacoes'], FILTER_SANITIZE_STRING);

try {
    // Atualizar agendamento
    $sql = "UPDATE agendamentos SET nome_paciente = ?, data_agendamento = ?, hora_agendamento = ?, observacoes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome_paciente, $data_agendamento, $hora_agendamento, $observacoes, $id]);

    header("Location: ..//pages/visualizar_agendamentos.php?status=updated");
} catch (PDOException $e) {
    error_log("Erro ao atualizar agendamento: " . $e->getMessage());
    header("Location: ..//pages/editar_agendamento.php?id=$id&status=error");
}
?>
