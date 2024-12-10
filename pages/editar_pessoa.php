<?php 
ob_start(); // Iniciar o buffer de saída
session_start();
include '../includes/db_connect.php';
include '../includes/header.php'; // Incluindo cabeçalho

// Captura do ID da pessoa a ser editada
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
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
        echo "Pessoa não encontrada.";
        include '../includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
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
                echo "Erro ao mover o arquivo para o diretório de destino.";
                include '../includes/footer.php';
                exit;
            }
        } else {
            echo "Formato de arquivo inválido ou arquivo muito grande.";
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
        echo "Erro ao atualizar a pessoa.";
        include '../includes/footer.php';
        exit;
    }
}

ob_end_flush(); // Encerra o buffer de saída e envia tudo ao navegador
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-start">
        <h2>Editar Pessoa</h2>
        
        <!-- <label for="foto">Foto:</label>
        <input type="file" id="foto" name="foto" class="form-control-file" accept="image/jpeg, image/png">
            <input type="file" id="foto" name="foto" class="form-control-file mt-2" accept="image/jpeg, image/png" style="display: none;"> -->
    </div>

    <form action="editar_pessoa.php?id=<?= $pessoa['id'] ?>" method="post" enctype="multipart/form-data" class="form-group mt-4">
    
    <!-- Foto no canto superior direito com interatividade -->
     
        <div class="position-relative ms-4" style="width: 150px;">
            <img id="previewImage" src="<?= htmlspecialchars($pessoa['foto'] ?? '../uploads/default.png') ?>" alt="Foto de <?= htmlspecialchars($pessoa['nome'] ?? '') ?>" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="opacity: 0; transition: opacity 0.3s; background: rgba(0, 0, 0, 0.5);" id="alterar-overlay">
                <span class="text-white fw-bold">Alterar Foto</span>
            </div>
         </div>    
         <input type="file" id="foto" name="foto" class="form-control-file" accept="image/jpeg, image/png" style="display: none;">
        <label for="nome">Nome:</label>
        <!-- <input type="text" id="nome" name="nome" class="form-control" value="<?= $pessoa['nome'] ?? '' ?>" required> -->
        <input type="text" id="nome" name="nome" class="form-control" required>
        <!-- Gênero -->
        <label for="genero">Gênero:</label>
        <select id="genero" name="genero" class="form-control" required>
            <option value="Masculino" <?= ($pessoa['genero'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
            <option value="Feminino" <?= ($pessoa['genero'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
            <option value="Outro" <?= ($pessoa['genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
        </select>

        <!-- Idade -->
        <label for="idade">Idade:</label>
        <input type="number" id="idade" name="idade" class="form-control" value="<?= htmlspecialchars($pessoa['idade'] ?? '') ?>">

        <!-- Peso -->
        <label for="peso">Peso (kg):</label>
        <input type="number" id="peso" name="peso" class="form-control" step="0.1" value="<?= htmlspecialchars($pessoa['peso'] ?? '') ?>">

        <!-- Altura -->
        <label for="altura">Altura (cm):</label>
        <input type="number" id="altura" name="altura" class="form-control" value="<?= htmlspecialchars($pessoa['altura'] ?? '') ?>">

        <!-- Etnia -->
        <label for="etnia">Etnia:</label>
        <input type="text" id="etnia" name="etnia" class="form-control" value="<?= htmlspecialchars($pessoa['etnia'] ?? '') ?>">

        <!-- RG -->
        <label for="rg">RG:</label>
        <input type="text" id="rg" name="rg" class="form-control" value="<?= htmlspecialchars($pessoa['rg'] ?? '') ?>">

        <!-- CPF -->
        <label for="cpf">CPF:</label>
        <input type="text" id="cpf" name="cpf" class="form-control" value="<?= htmlspecialchars($pessoa['cpf'] ?? '') ?>">

        <!-- CNH -->
        <label for="cnh">CNH:</label>
        <input type="text" id="cnh" name="cnh" class="form-control" value="<?= htmlspecialchars($pessoa['cnh'] ?? '') ?>">

        <!-- Telefone -->
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" class="form-control" value="<?= htmlspecialchars($pessoa['telefone'] ?? '') ?>">

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($pessoa['email'] ?? '') ?>">

        <!-- Data de Nascimento -->
        <label for="nasc">Data de Nascimento:</label>
        <input type="date" id="nasc" name="nasc" class="form-control" value="<?= htmlspecialchars($pessoa['data_nasc'] ?? '') ?>" required>

       

        <!-- Botão de Submissão -->
        <button type="submit" class="btn btn-primary mt-3">Salvar Alterações</button>
    </form>
</div>

<script>
    document.querySelector(".position-relative").addEventListener("mouseover", function () {
        document.getElementById("alterar-overlay").style.opacity = "1";
    });

    document.querySelector(".position-relative").addEventListener("mouseout", function () {
        document.getElementById("alterar-overlay").style.opacity = "0";
    });

    document.getElementById("alterar-overlay").addEventListener("click", function () {
        document.getElementById("foto").click();
    });

    // Pré-visualização da imagem ao selecionar um novo arquivo
    document.getElementById("foto").addEventListener("change", function (event) {
        if (event.target.files && event.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("previewImage").src = e.target.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
