
<?php
// Arquivo: cpf_email_validation.php

require_once '../includes/db_connect.php';

function isDuplicate($value, $field, $table) {
    global $conn;
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $field = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $value);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['count'] > 0;
}
?>
