<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $idade = filter_input(INPUT_POST, 'idade', FILTER_VALIDATE_INT);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_SPECIAL_CHARS);
    $peso = filter_input(INPUT_POST, 'peso', FILTER_VALIDATE_FLOAT);
    $altura = filter_input(INPUT_POST, 'altura', FILTER_VALIDATE_INT);
    $etnia = filter_input(INPUT_POST, 'etnia', FILTER_SANITIZE_SPECIAL_CHARS);
    $rg = filter_input(INPUT_POST, 'rg', FILTER_SANITIZE_SPECIAL_CHARS);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS);
    $cnh = filter_input(INPUT_POST, 'cnh', FILTER_SANITIZE_SPECIAL_CHARS);
    $nasc = filter_input(INPUT_POST, 'nasc', FILTER_SANITIZE_SPECIAL_CHARS);

    // Tratamento para valores vazios de peso e altura
    $peso = ($peso !== false) ? $peso : null;
    $altura = ($altura !== false) ? $altura : null;
    $idade = ($idade !== false) ? $idade : null;

    // Verificação para upload de foto
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $allowedTypes = ['image/jpeg', 'image/png'];

        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (in_array($fileType, $allowedTypes) && $fileSize < 2 * 1024 * 1024) {
            $dest_path = $uploadDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $foto = $dest_path;
            } else {
                echo "Erro ao mover o arquivo para o diretório de destino.";
                exit;
            }
        } else {
            echo "Tipo de arquivo inválido ou arquivo muito grande.";
            exit;
        }
    }

    // Inserir no banco de dados
    try {
        $sql = "INSERT INTO pessoas (nome, idade, telefone, email, genero, peso, altura, etnia, rg, cpf, cnh, data_nasc, foto) 
                VALUES (:nome, :idade, :telefone, :email, :genero, :peso, :altura, :etnia, :rg, :cpf, :cnh, :nasc, :foto)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':idade', $idade);
        $stmt->bindValue(':telefone', $telefone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':genero', $genero);
        $stmt->bindValue(':peso', $peso, $peso !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':altura', $altura, $altura !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':etnia', $etnia);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':cnh', $cnh);
        $stmt->bindValue(':nasc', $nasc);
        $stmt->bindValue(':foto', $foto);
        $stmt->execute();

        //echo "Pessoa cadastrada com sucesso!";
        header("Location: ../views/view_pessoa.php?status=updated");
    } catch (PDOException $e) {
        echo "Erro ao cadastrar pessoa: " . $e->getMessage();
    }
}
include '../includes/header.php'; // Incluindo cabeçalho
?>

<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Pacientes</p>
            <h1>Cadastro de Paciente</h1>
            <p class="page-subtitle">Registre dados do paciente para acompanhamento.</p>
        </div>
    </section>

    <section class="table-card">
        <h2>Novo cadastro</h2>
        <form action="cadastro_pessoa.php" method="post" enctype="multipart/form-data" class="form-grid">
            <div class="form-field form-field--full">
                <div class="image-upload-card">
                    <div class="image-upload-preview">
                        <img id="previewImage" src="../uploads/default.png" alt="Prévia da foto do paciente">
                    </div>
                    <div class="image-upload-actions">
                        <p class="image-upload-title">Foto do paciente</p>
                        <p class="image-upload-text">Adicione uma imagem para identificação rápida no prontuário.</p>
                        <label class="btn btn-outline-primary image-upload-button" for="foto">Selecionar imagem</label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg, image/png" hidden>
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-field">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone">
            </div>

            <div class="form-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="form-field">
                <label for="genero">G&ecirc;nero:</label>
                <select id="genero" name="genero" required>
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>

            <div class="form-field">
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade">
            </div>

            <div class="form-field">
                <label for="peso">Peso (kg):</label>
                <input type="number" id="peso" name="peso" step="0.1">
            </div>

            <div class="form-field">
                <label for="altura">Altura (cm):</label>
                <input type="number" id="altura" name="altura">
            </div>

            <div class="form-field">
                <label for="etnia">Etnia:</label>
                <input type="text" id="etnia" name="etnia">
            </div>

            <div class="form-field">
                <label for="rg">RG:</label>
                <input type="text" id="rg" name="rg">
            </div>

            <div class="form-field">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf">
            </div>

            <div class="form-field">
                <label for="cnh">CNH:</label>
                <input type="text" id="cnh" name="cnh">
            </div>

            <div class="form-field">
                <label for="nasc">Data de Nascimento:</label>
                <input type="date" id="nasc" name="nasc" required>
            </div>

            <div class="form-field form-field--full">
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>
        </form>
    </section>
</main>

<?php
// Inclui o rodapé
include '../includes/footer.php';
?>

<script>
    const fotoInput = document.getElementById('foto');
    const previewImage = document.getElementById('previewImage');

    if (fotoInput && previewImage) {
        fotoInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }
</script>