<?php
function validateNotEmpty($value) {
    return !empty(trim($value));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateCPF($cpf) {
    // Implementar validação de CPF
    return preg_match('/^[0-9]{11}$/', $cpf);
}
?>
