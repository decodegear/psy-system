<?php
// Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); //TODO Inicia a sessão apenas se ela não estiver ativa 
}

// Inclui o arquivo de configuração
include 'db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Financeiro</title>
    <!-- Link para o Bootstrap -->
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Link para o estilo CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Link para o Manifesto do PWA -->
    <link rel="manifest" href="../manifest.json">
</head>
<body class="container">
<header>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="../index.php">Psicologia </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
 <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Visualizar</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="../pages/visualizar_transacao.php?tipo=receita">Visualizar Receitas</a></li>
                            <li><a class="dropdown-item" href="../pages/visualizar_transacao.php?tipo=despesa">Visualizar Despesas</a></li>
                            <li><a class="dropdown-item" href="../views/view_pessoa.php">Visualizar Pacientes</a></li>
                            <li><a class="dropdown-item" href="../pages/visualizar_usuarios.php">Visualizar Usuários</a></li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">+NOVO</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="../pages/cadastro_transacao.php?tipo=receita">Entradas</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastro_transacao.php?tipo=despesa">Saidas</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastro_pessoa.php">Pacientes</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastrar_usuario.php">Usuários</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastrar_conta.php">Bancos</a></li>
                            <li><a class="dropdown-item" href="../pages/cadastrar_categoria.php">Categorias</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Agenda</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="../pages/agendar_pacientes.php">Agendar Paciente</a></li>
                            <li><a class="dropdown-item" href="../pages/visualizar_agendamentos.php">Ver Agenda</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/dashboard.php">
                            <?php if (!isset($_SESSION['admin_id'])): ?>
                                Login
                            <?php else: ?>
                                Painel
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/logout.php">Sair</a>
                    </li>
                    <?php endif; ?>
                </ul>
                </div>
</nav>
</header>

<script>
  // JavaScript para garantir que o menu mobile funcione corretamente
  document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.querySelector('.navbar-toggler');
    mobileMenuButton.addEventListener('click', function() {
      const menu = document.querySelector('#navbarNav');
      menu.classList.toggle('show');
    });
  });
</script>