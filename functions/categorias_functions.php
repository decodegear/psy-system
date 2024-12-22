// categorias_functions.php
<?php
include 'db_functions.php';

function getAllCategorias($conn)
{
    $sql = "SELECT * FROM categorias";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoriaById($conn, $id)
{
    $sql = "SELECT * FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveCategoria($conn, $nome, $tipo, $id = null)
{
    if ($id) {
        $sql = "UPDATE categorias SET nome = ?, tipo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nome, $tipo, $id]);
    } else {
        $sql = "INSERT INTO categorias (nome, tipo) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nome, $tipo]);
    }
}

function deleteCategoria($conn, $id)
{
    $sql = "DELETE FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}
?>