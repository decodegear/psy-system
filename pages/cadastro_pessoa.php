
<?php
session_start();
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
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
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
        $stmt->bindValue(':foto', $foto, PDO::PARAM_LOB);
        $stmt->execute();
        
        //echo "Pessoa cadastrada com sucesso!";
        header("Location: ../views/visualizar_pessoa.php?status=updated");
    } catch (PDOException $e) {
        echo "Erro ao cadastrar pessoa: " . $e->getMessage();
    }
}
include '../includes/header.php'; // Incluindo cabeçalho
?>

<h1>Cadastro de Paciente</h1>
<form action="cadastro_pessoa.php" method="post" enctype="multipart/form-data" class="form-group">
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" class="form-control" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" class="form-control">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" class="form-control">

    <label for="genero">Gênero:</label>
    <select id="genero" name="genero" class="form-control" required>
        <option value="Masculino">Masculino</option>
        <option value="Feminino">Feminino</option>
        <option value="Outro">Outro</option>
    </select>

    <label for="idade">Idade:</label>
    <input type="number" id="idade" name="idade" class="form-control">

    <label for="peso">Peso (kg):</label>
    <input type="number" id="peso" name="peso" step="0.1" class="form-control">

    <label for="altura">Altura (cm):</label>
    <input type="number" id="altura" name="altura" class="form-control">

    <label for="etnia">Etnia:</label>
    <input type="text" id="etnia" name="etnia" class="form-control">

    <label for="rg">RG:</label>
    <input type="text" id="rg" name="rg" class="form-control">

    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" class="form-control">

    <label for="cnh">CNH:</label>
    <input type="text" id="cnh" name="cnh" class="form-control">

    <label for="nasc">Data de Nascimento:</label>
    <input type="date" id="nasc" name="nasc" class="form-control" required>

    <label for="foto">Foto:</label>
    <input type="file" id="foto" name="foto" class="form-control-file" accept="image/jpeg, image/png">
    
    <button type="submit" class="btn btn-primary mt-3">Cadastrar</button>
</form>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
