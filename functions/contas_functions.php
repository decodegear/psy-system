<?php
include 'db_functions.php';

function getAllContas($conn)
{
    $sql = "SELECT * FROM contas";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getContaById($conn, $id)
{
    $sql = "SELECT * FROM contas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveConta($conn, $nome, $saldo_inicial, $id = null)
{
    if ($id) {
        $sql = "UPDATE contas SET nome = ?, saldo_inicial = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nome, $saldo_inicial, $id]);
    } else {
        $sql = "INSERT INTO contas (nome, saldo_inicial) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nome, $saldo_inicial]);
    }
}

function deleteConta($conn, $id)
{
    $sql = "DELETE FROM contas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}
?>