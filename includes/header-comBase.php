<?php include 'head.php' ?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="../index.php">Psicologia </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Visualizar</a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="../views/visualizar_transacao.php?tipo=receita">Visualizar Receitas</a></li>
                    <li><a class="dropdown-item" href="../views/visualizar_transacao.php?tipo=despesa">Visualizar Despesas</a></li>
                    <li><a class="dropdown-item" href="../views/pessoa/view_pessoa.php">Visualizar Pacientes</a></li>
                    <li><a class="dropdown-item" href="../views/visualizar_usuarios.php">Visualizar Usuários</a></li>
                </ul>
            </li>
         <?php if (isset($_SESSION['admin_id'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">+NOVO</a>
                <ul class="dropdown-menu">
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
                <ul class="dropdown-menu">
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
