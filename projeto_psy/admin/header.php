<?php
include 'db_connect.php'; 
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão apenas se ela não estiver ativa
}

// Inclui o arquivo de configuração

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Financeiro</title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link para o estilo CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">Sistema Financeiro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Cadastro</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../pages/cadastro_pessoa.php">Cadastro Pessoa</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastro_usuario.php">Cadastro Usuário</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastro_transacao.php?tipo=despesa">Cadastro Despesa</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastro_transacao.php?tipo=receita">Cadastro Receita</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">Visualizar</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="../pages/visualizar_transacao.php?tipo=receita">Visualizar Receitas</a></li>
                            <li><a class="dropdown-item" href="../pages/visualizar_transacao.php?tipo=despesa">Visualizar Despesas</a></li>
                            <li><a class="dropdown-item" href="../pages/visualizar_pessoas.php">Visualizar Pessoas</a></li>
                            <li><a class="dropdown-item" href="../pages/visualizar_usuarios.php">Visualizar Usuários</a></li>
                            
                        </ul>
                    </li>
                </ul>

                <!-- Exibir o nome do usuário logado no canto direito -->
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['admin_nome'])): ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../admin/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
