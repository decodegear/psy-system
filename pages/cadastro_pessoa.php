<?php
session_start();
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação de upload de imagem
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $allowedTypes = ['image/jpeg', 'image/png'];
        
        if (in_array($fileType, $allowedTypes) && $fileSize < 2 * 1024 * 1024) {
            $dest_path = 'uploads/' . $fileName;
            move_uploaded_file($fileTmpPath, $dest_path);
            echo "Foto enviada com sucesso!";
        } else {
            echo "Formato de arquivo inválido ou arquivo muito grande.";
        }
    }
}
?>

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
    <input type="number" id="idade" name="idade" >
    
    <!-- Peso -->
    <label for="peso">Peso (kg):</label>
    <input type="number" id="peso" name="peso" step="0.1" >
    
    <!-- Altura -->
    <label for="altura">Altura (cm):</label>
    <input type="number" id="altura" name="altura" >
    
    <!-- Etnia -->
    <label for="etnia">Etnia:</label>
    <input type="text" id="etnia" name="etnia" >
    
    <!-- RG -->
    <label for="rg">RG:</label>
    <input type="text" id="rg" name="rg" >
    
    <!-- CPF -->
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" >
    
    <!-- CNH -->
    <label for="cnh">CNH:</label>
    <input type="text" id="cnh" name="cnh">
    
    <!-- Data de Nascimento -->
    <label for="nasc">Data de Nascimento:</label>
    <input type="date" id="nasc" name="nasc" required>

    <label for="foto">Foto:</label>
    <input type="file" id="foto" name="foto" class="form-control-file" accept="image/jpeg, image/png">
<!-- Botão de Submissão -->
    <button type="submit" class="btn btn-primary mt-3">Cadastrar</button>
</form>
<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>