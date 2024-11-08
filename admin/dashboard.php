<?php 
session_start();
include '../includes/header.php'; 




// Verificar se o administrador está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

 

// Definir o tempo limite da sessão em 10 minutos (600 segundos)
$session_timeout = 600; 

// Verificar se a última atividade foi registrada
if (isset($_SESSION['last_activity'])) {
    // Calcular o tempo de inatividade
    $inatividade = time() - $_SESSION['last_activity'];

    // Se o tempo de inatividade exceder o limite, destruir a sessão
    if ($inatividade > $session_timeout) {
        session_unset();  // Limpa as variáveis de sessão
        session_destroy();  // Destroi a sessão
        header("Location: ../index.php");  // Redireciona para a página inicial
        exit;
    }
}

// Atualiza o timestamp da última atividade
$_SESSION['last_activity'] = time();


?>

<div class="container mt-5">
    <h1 class="text-left">Olá, <?= htmlspecialchars($_SESSION['admin_nome']) ?></h1>
    
    <div class="row mt-4">
        <!-- Coluna para os cadastros -->
        <div class="col-md-4">
            <h2 class="text-black">Cadastrar / Alterar</h2>
            <ul class="dashboard-list">
                <li><a href="../pages/cadastrar_categoria.php">Cad Categorias</a></li>
                <li><a href="../pages/cadastrar_conta.php">Cad Contas</a></li>
                <li><a href="../pages/cadastrar_usuario.php">Cad Usuários</a></li>
                <li><a href="../pages/cadastro_transacao.php?tipo=despesa">Cadastro Despesas</a></li>
                <li><a href="../pages/cadastro_transacao.php?tipo=receitas">Cadastro Receitas</a></li>
            </ul>
        </div>

        <!-- Coluna para relatórios -->
        <div class="col-md-4">
            <h2 class="text-black">Relatórios</h2>
            <ul class="dashboard-list">
                <li><a href="relatorio.php">Relatório Financeiro</a></li>
            </ul>
        </div>
        <!-- Coluna para relatórios -->
        <div class="col-md-4">
            <h2 class="text-black">Visualizar</h2>
            <ul class="dashboard-list">
                <li><a href="relatorio.php">Relatório Financeiro</a></li>
            </ul>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php'; 
?>
