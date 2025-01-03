
<?php
// Arquivo: cpf_validation.php

require_once '../includes/db_connect.php';

function isCpfDuplicate($cpf, $table) {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM $table WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $cpf);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['count'] > 0;
}
?>
