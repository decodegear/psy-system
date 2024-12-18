<?php
ob_start(); // Iniciar o buffer de saída
session_start();
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
include '../includes/db_connect.php'; // Certifique-se de que esta linha inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação de upload de imagem
    $dest_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $allowedTypes = ['image/jpeg', 'image/png'];
        
        // Definir diretório de upload e usar caminho absoluto
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                // Se mkdir falhar, registrar o erro e mostrar mensagem apropriada
                error_log("Falha ao criar o diretório de upload: $uploadDir");
                echo "Erro ao criar diretório de upload.";
                include '../includes/footer.php';
                exit;
            } else {
                // Caso tenha sido criado, definir permissões adequadas
                chmod($uploadDir, 0777);
            }
        }
        /* if (in_array($fileType, $allowedTypes) && $fileSize < 2 * 1024 * 1024) {
            $dest_path = '../uploads/' . $fileName;
            move_uploaded_file($fileTmpPath, $dest_path);
            echo "Foto enviada com sucesso!";
        } else {
            echo "Formato de arquivo inválido ou arquivo muito grande.";
            exit;
        } */
    }

    // Preparar e executar a inserção no banco de dados
    try {
        // Definir valores padrão para os campos que podem estar vazios
        $idade = !empty($_POST['idade']) && is_numeric($_POST['idade']) ? $_POST['idade'] : null;
        $peso = !empty($_POST['peso']) && is_numeric($_POST['peso']) ? $_POST['peso'] : null;
        $altura = !empty($_POST['altura']) && is_numeric($_POST['altura']) ? $_POST['altura'] : null;

        // Garantir que os outros campos de texto estejam sempre definidos
        $nome = $_POST['nome'] ?? '';
        $genero = $_POST['genero'] ?? '';
        $etnia = $_POST['etnia'] ?? '';
        $rg = $_POST['rg'] ?? '';
        $cpf = $_POST['cpf'] ?? '';
        $cnh = $_POST['cnh'] ?? '';
        $data_nasc = !empty($_POST['nasc']) ? $_POST['nasc'] : null;

        $sql = "INSERT INTO pessoas (nome, genero, idade, peso, altura, etnia, rg, cpf, cnh, data_nasc, foto) 
                VALUES (:nome, :genero, :idade, :peso, :altura, :etnia, :rg, :cpf, :cnh, :data_nasc, :foto)";
        $stmt = $conn->prepare($sql);
        
        // Passar os parâmetros
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':genero', $genero);
        
        // Tratar valores nulos e numéricos
        if ($idade === null) {
            $stmt->bindValue(':idade', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':idade', $idade, PDO::PARAM_INT);
        }

        if ($peso === null) {
            $stmt->bindValue(':peso', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':peso', $peso);
        }

        if ($altura === null) {
            $stmt->bindValue(':altura', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':altura', $altura);
        }

        $stmt->bindValue(':etnia', $etnia);
        $stmt->bindValue(':rg', $rg);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':cnh', $cnh);
        if ($data_nasc === null) {
            $stmt->bindValue(':data_nasc', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':data_nasc', $data_nasc);
        }
        $stmt->bindValue(':foto', $dest_path);

        // Executar a query
        $stmt->execute();

        // Redirecionar para a página de visualização após o cadastro bem-sucedido
        header("Location: visualizar_pessoa.php");
        exit;
        
    } catch (PDOException $e) {
        echo "Erro ao cadastrar pessoa: " . $e->getMessage();
    }
}
?>
<h1>Cadastro de Paciente</h1>
<form action="cadastro_pessoa.php" method="post" enctype="multipart/form-data" class="form-group">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" class="form-control" required>
    
    <!-- Gênero -->
    <label for="genero">Gênero:</label>
    <select id="genero" name="genero" required>
        <option value="Masculino">Masculino</option>
        <option value="Feminino">Feminino</option>
        <option value="Outro">Outro</option>
    </select>
    
    <!-- Idade -->
    <label for="idade">Idade:</label>
    <input type="number" id="idade" name="idade">

    <!-- Peso -->
    <label for="peso">Peso (kg):</label>
    <input type="number" id="peso" name="peso" step="0.1">

    <!-- Altura -->
    <label for="altura">Altura (cm):</label>
    <input type="number" id="altura" name="altura">

    <!-- Etnia -->
    <label for="etnia">Etnia:</label>
    <input type="text" id="etnia" name="etnia">

    <!-- RG -->
    <label for="rg">RG:</label>
    <input type="text" id="rg" name="rg">

    <!-- CPF -->
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf">

    <!-- CNH -->
    <label for="cnh">CNH:</label>
    <input type="text" id="cnh" name="cnh">

    <!-- Data de Nascimento -->
    <label for="nasc">Data de Nascimento:</label>
    <input type="date" id="nasc" name="nasc" required>

    <!-- Foto -->
    <label for="foto">Foto:</label>
    <input type="file" id="foto" name="foto" class="form-control-file" accept="image/jpeg, image/png">
    
    <!-- Botão de Submissão -->
    <button type="submit" class="btn btn-primary mt-3">Cadastrar</button>
</form>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
ob_end_flush(); // Encerra o buffer de saída e envia tudo ao navegador
?>
