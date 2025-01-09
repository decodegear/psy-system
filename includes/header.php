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
    <link rel="icon" href="/assets/icons/favicon.ico" type="image/x-icon">

    <title>Alexandre Batista</title>

    <link rel="stylesheet" type="text/css" href="/assets/css/style.css?v=<?php echo time(); ?>">
    <!-- Link para o Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Link para o Manifesto do PWA -->
    <link rel="manifest" href="../manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap" rel="stylesheet">
    <script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/service-worker.js")
                .then(registration => {
                    console.log("Service Worker registrado:", registration.scope);

                    registration.addEventListener("updatefound", () => {
                        const newSW = registration.installing;
                        newSW.addEventListener("statechange", () => {
                            if (newSW.state === "installed" && navigator.serviceWorker.controller) {
                                let updateButton = document.createElement("button");
                                updateButton.textContent = "Nova versão disponível! Clique para atualizar.";
                                updateButton.style.position = "fixed";
                                updateButton.style.bottom = "10px";
                                updateButton.style.right = "10px";
                                updateButton.style.backgroundColor = "#007bff";
                                updateButton.style.color = "#fff";
                                updateButton.style.padding = "10px";
                                updateButton.style.border = "none";
                                updateButton.style.cursor = "pointer";

                                updateButton.addEventListener("click", () => {
                                    location.reload();
                                });

                                document.body.appendChild(updateButton);
                            }
                        });
                    });
                })
                .catch(error => console.error("Erro ao registrar o Service Worker:", error));
        }
    </script>

</head>
<header>
    <!-- Esse -->
    <div class="container">

    </div>

    <body class="container">
        <nav class="navbar navbar-expand-lg bg">
            <a class="montserrat-text navbar-brand " href="../index.php">Alexandre Batista </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Visualizar</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="../views/visualizar_transacao.php?tipo=receita">Visualizar Receitas</a></li>
                            <li><a class="dropdown-item" href="../views/visualizar_transacao.php?tipo=despesa">Visualizar Despesas</a></li>
                            <li><a class="dropdown-item" href="../views/view_pessoa.php">Visualizar Pacientes</a></li>
                            <li><a class="dropdown-item" href="../views/visualizar_usuarios.php">Visualizar Usuários</a></li>
                            <li><a class="dropdown-item" href="../views/view_relatorio.php">Relatório Financeiro</a></li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Novo</a>
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
                                <li><a class="dropdown-item" href="../views/visualizar_agendamentos.php">Ver Agenda</a></li>
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