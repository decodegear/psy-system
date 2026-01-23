<?php 
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location:../admin/login.php");
    exit;
}
// Inclui o cabeçalho com o menu de navegação

include '../includes/db_connect.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

try {
    // Buscar dados do usuário para edição
    $sql = "SELECT nome, telefone, cpf, email FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        die("Usuário não encontrado.");
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    echo "Erro ao carregar dados do usuário.";
}
include '../includes/header.php';
?>

<h1>Editar Usu&aacute;rio</h1>

<form action="/actions/update_usuario.php" method="post" class="form-grid">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-field">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
    </div>

    <div class="form-field">
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
    </div>

    <div class="form-field">
        <label for="cpf">CPF:</label>
        <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($usuario['cpf']) ?>" required>
    </div>

    <div class="form-field">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
    </div>

    <div class="form-field form-field--full">
        <input type="submit" value="Atualizar Usu&aacute;rio">
    </div>
</form>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>

