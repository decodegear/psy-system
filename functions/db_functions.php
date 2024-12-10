<?php

function fetchAll($table) {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM $table");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchById($table, $id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insert($table, $data) {
    global $pdo;
    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}
function update($table, $id, $data) {
    global $pdo; // Assegura que a conexão ao banco de dados está acessível

    // Verifica se $data é um array antes de usar o foreach
    if (!is_array($data)) {
        die('Os dados fornecidos não são um array. Verifique a chamada da função update.');
    }

    $setClause = '';
    $params = [];

    // Constrói a cláusula SET para a consulta SQL
    foreach ($data as $column => $value) {
        $setClause .= "$column = ?, ";
        $params[] = $value;
    }

    // Remove a vírgula extra e o espaço no final da cláusula SET
    $setClause = rtrim($setClause, ', ');

    // Adiciona o ID aos parâmetros
    $params[] = $id;

    // Prepara a instrução SQL para atualização
    $sql = "UPDATE $table SET $setClause WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    //return $stmt->execute($data);
    
    /* if ($stmt === false) {
        die('Erro ao preparar a consulta: ' . $pdo->error);
    }
 */
    // Executa a consulta preparada
    return $stmt->execute($params);

  /*   if ($stmt->error) {
        die('Erro na execução da consulta: ' . $pdo->error);
    }

    $stmt->close(); */
}

function delete($table, $id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}
function deleteById($table, $id) {
    global $pdo; // Certifique-se de que a conexão PDO está configurada corretamente

    try {
        $query = "DELETE FROM $table WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        echo "Erro ao excluir: " . $e->getMessage();
        return false;
    }
}

?>
