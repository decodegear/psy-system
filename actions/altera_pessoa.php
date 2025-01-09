<?php
session_start();
include '../includes/db_connect.php';

// Verificar se o administrador está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

// Captura do ID da pessoa a ser editada
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    include '../includes/header.php';
    echo "ID inválido.";
    include '../includes/footer.php';
    exit;
}

// Consultar os dados da pessoa no banco de dados
try {
    $sql = "SELECT * FROM pessoas WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pessoa) {
        include '../includes/header.php';
        echo "Pessoa não encontrada.";
        include '../includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    include '../includes/header.php';
    error_log("Erro ao buscar dados da pessoa: " . $e->getMessage());
    echo "Erro ao carregar os dados da pessoa.";
    include '../includes/footer.php';
    exit;
}
// Processar a atualização dos dados quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar os dados do formulário
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $idade = filter_input(INPUT_POST, 'idade', FILTER_VALIDATE_INT);
    $peso = filter_input(INPUT_POST, 'peso', FILTER_VALIDATE_FLOAT);
    $altura = filter_input(INPUT_POST, 'altura', FILTER_VALIDATE_INT);
    $etnia = filter_input(INPUT_POST, 'etnia', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $rg = filter_input(INPUT_POST, 'rg', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cnh = filter_input(INPUT_POST, 'cnh', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nasc = filter_input(INPUT_POST, 'nasc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Verificar se uma nova foto foi enviada
    $dest_path = $pessoa['foto']; // Caminho existente
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $allowedTypes = ['image/jpeg', 'image/png'];

        // Verificar se a pasta uploads/ existe, se não, criar a pasta
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (in_array($fileType, $allowedTypes) && $fileSize < 2 * 1024 * 1024) {
            $dest_path = $uploadDir . basename($fileName);
            if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                include '../includes/header.php';
                echo "Erro ao mover o arquivo para o diretório de destino.";
                include '../includes/footer.php';
                exit;
            }
        } else {
            include '../includes/header.php';
            echo "Tipo de arquivo inválido ou arquivo muito grande.";
            include '../includes/footer.php';
            exit;
        }
    }
    // Atualizar os dados da pessoa no banco
    try {
        $sql = "UPDATE pessoas 
                SET nome = :nome, genero = :genero, idade = :idade, peso = :peso, altura = :altura, etnia = :etnia, rg = :rg, cpf = :cpf, cnh = :cnh, telefone = :telefone, email = :email, data_nasc = :data_nasc, foto = :foto 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':genero', $genero);
        $stmt->bindValue(':idade', $idade !== false ? $idade : null, PDO::PARAM_INT);
        $stmt->bindValue(':peso', $peso !== false ? $peso : null);
        $stmt->bindValue(':altura', $altura !== false ? $altura : null, PDO::PARAM_INT);
        $stmt->bindValue(':etnia', $etnia);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':cnh', $cnh);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':data_nasc', $nasc);
        $stmt->bindValue(':foto', $dest_path);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        // Redirecionar para a página de visualização após a atualização bem-sucedida
        header("Location: ../views/view_pessoa.php?id=$id&status=updated");
        exit;
    } catch (PDOException $e) {
        error_log("Erro ao atualizar pessoa: " . $e->getMessage());
        include '../includes/header.php';
        echo "Erro ao atualizar a pessoa.";
        include '../includes/footer.php';
        exit;
    }
}

include '../includes/header.php';
?>
<div class="container my-4">
    <h2 class="montserrat-text text-center mb-4">Editar Pessoa</h2>
    <form action="altera_pessoa.php?id=<?= $pessoa['id'] ?>" method="post" enctype="multipart/form-data">
        <div class="row g-4">

            <!-- Foto e campo para alterar -->

            <div class="col-md-4 text-center">
                <div class="position-relative">
                    <img id="previewImage" src="<?= htmlspecialchars($pessoa['foto'] ?? '../uploads/default.png') ?>" alt="Foto de <?= htmlspecialchars($pessoa['nome'] ?? '') ?>" class="img-thumbnail mb-3" style="width: 100%; max-width: 250px; height: auto; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="opacity: 0; transition: opacity 0.3s; background: rgba(0, 0, 0, 0.5);" id="alterar-overlay">
                        <span class="text-white fw-bold">Alterar Foto</span>
                    </div>
                </div>
                <input type="file" id="foto" name="foto" class="form-control mt-2" style="display: none;" accept="image/jpeg, image/png">
            </div>

            <!-- Campos do formulário -->
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($pessoa['nome'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="genero" class="form-label">Gênero</label>
                        <select id="genero" name="genero" class="form-select" required>
                            <option value="Masculino" <?= ($pessoa['genero'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino" <?= ($pessoa['genero'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                            <option value="Outro" <?= ($pessoa['genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="idade" class="form-label">Idade</label>
                        <input type="number" id="idade" name="idade" class="form-control" value="<?= htmlspecialchars($pessoa['idade'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="peso" class="form-label">Peso (kg)</label>
                        <input type="number" id="peso" name="peso" class="form-control" step="0.1" value="<?= htmlspecialchars($pessoa['peso'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="altura" class="form-label">Altura (cm)</label>
                        <input type="number" id="altura" name="altura" class="form-control" value="<?= htmlspecialchars($pessoa['altura'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="etnia" class="form-label">Etnia</label>
                        <input type="text" id="etnia" name="etnia" class="form-control" value="<?= htmlspecialchars($pessoa['etnia'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-control" value="<?= htmlspecialchars($pessoa['telefone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($pessoa['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" id="cpf" name="cpf" class="form-control" value="<?= htmlspecialchars($pessoa['cpf'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="nasc" class="form-label">Data de Nascimento</label>
                        <input type="date" id="nasc" name="nasc" class="form-control" value="<?= htmlspecialchars($pessoa['data_nasc'] ?? '') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão de submissão -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>

<script>
    document.querySelector(".position-relative").addEventListener("mouseover", function() {
        document.getElementById("alterar-overlay").style.opacity = "1";
    });

    document.querySelector(".position-relative").addEventListener("mouseout", function() {
        document.getElementById("alterar-overlay").style.opacity = "0";
    });

    document.getElementById("alterar-overlay").addEventListener("click", function() {
        document.getElementById("foto").click();
    });

    // Pré-visualização da imagem ao selecionar um novo arquivo
    document.getElementById("foto").addEventListener("change", function(event) {
        if (event.target.files && event.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("previewImage").src = e.target.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
<?php include '../includes/footer.php'; ?>