<?php session_start();
$isLoggedIn = isset($_SESSION['admin_id']); ?>
<?php
include '../includes/db_connect.php';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
// Captura do ID, se houver
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$searchTerm = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$pessoas = [];
$pessoa = null;

try {
    if ($id) {
        // Consulta para buscar uma pessoa específica
        $sql = "SELECT * FROM pessoas WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Consulta para listar todas as pessoas
        if ($searchTerm) {
            // Buscar pessoas com base no termo de pesquisa
            $sql = "SELECT id, nome, idade, telefone FROM pessoas WHERE nome LIKE :search";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
        } else {
            // Buscar todas as pessoas
            $sql = "SELECT id, nome, idade, telefone FROM pessoas";
            $stmt = $conn->prepare($sql);
        }
        $stmt->execute();
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar pessoas: " . $e->getMessage());
    echo "Erro ao carregar a lista de pessoas.";
    include '../includes/footer.php';
    exit;
}
include '../includes/header.php'; // Incluindo cabeçalho
?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Campo de Pesquisa -->
            <form class="d-flex mb-4" method="get" action="view_pessoa.php">
                <input class="form-control me-2" type="search" name="search" placeholder="Pesquisar por nome" value="<?= htmlspecialchars($searchTerm ?? '') ?>" aria-label="Pesquisar">
                <button class="btn btn-outline-success" type="submit">Pesquisar</button>
            </form>
            <?php if ($isAdmin): ?>
                <a href="<?= BASE_URL ?>/pages/cadastro_pessoa.php" class="btn btn-primary mb-3">Adicionar Novo Paciente</a><?php ?>
            <?php endif; ?>

            <?php if (!empty($pessoas)): ?>
                <div class="list-group mb-4">
                    <?php foreach ($pessoas as $pessoaItem): ?>
                        <a href="view_pessoa.php?id=<?= $pessoaItem['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Nome:</strong> <?= htmlspecialchars($pessoaItem['nome'] ?? '') ?><br>
                                <strong>Idade:</strong> <?= htmlspecialchars($pessoaItem['idade'] ?? 'Não informado') ?> anos<br>
                                <strong>Telefone:</strong> <?= htmlspecialchars($pessoaItem['telefone'] ?? 'Não informado') ?>
                            </div>
                            <i class="bi bi-arrow-right-circle"></i> <!-- Ícone para indicar mais informações -->
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">Nenhuma pessoa encontrada.</p>
            <?php endif; ?>

            <?php if ($pessoa): ?>
                <!-- Informações detalhadas da pessoa selecionada -->
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <!-- Verificar e Exibir a Imagem da Pessoa -->
                        <?php
                        $fotoPath = !empty($pessoa['foto']) ? htmlspecialchars($pessoa['foto']) : '<?= BASE_URL ?>/uploads/default.png';
                        ?>
                        <img src="<?= $fotoPath ?>" alt="Foto de <?= htmlspecialchars($pessoa['nome'] ?? '') ?>" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="dropdown">
                        <!-- Menu Sanduíche -->
                        <?php if ($isAdmin): ?>
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-list"></i> <!-- Ícone de Menu Sanduíche do Bootstrap Icons -->
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="../actions/altera_pessoa.php?id=<?= $pessoa['id'] ?>">Alterar</a></li>
                                <li><a class="dropdown-item" href="#">Imprimir</a></li>
                                <li><a class="dropdown-item" href="#">Compartilhar</a></li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <h2 class="card-title"><?= htmlspecialchars($pessoa['nome'] ?? '') ?></h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Gênero:</strong> <?= htmlspecialchars($pessoa['genero'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Idade:</strong> <?= htmlspecialchars($pessoa['idade'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Peso:</strong> <?= htmlspecialchars($pessoa['peso'] ?? 'Não informado') ?> kg
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Altura:</strong> <?= htmlspecialchars($pessoa['altura'] ?? 'Não informado') ?> cm
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Etnia:</strong> <?= htmlspecialchars($pessoa['etnia'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>RG:</strong> <?= htmlspecialchars($pessoa['rg'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>CPF:</strong> <?= htmlspecialchars($pessoa['cpf'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>CNH:</strong> <?= htmlspecialchars($pessoa['cnh'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Telefone:</strong> <?= htmlspecialchars($pessoa['telefone'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong> <?= htmlspecialchars($pessoa['email'] ?? 'Não informado') ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Data de Nascimento:</strong> <?= !empty($pessoa['data_nasc']) ? htmlspecialchars((new DateTime($pessoa['data_nasc']))->format('d/m/Y')) : 'Não informado' ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; // Incluindo o rodapé 
?>