<?php 
include 'includes/header.php'; 
session_start();

?>
<?php if (isset($_SESSION['erro'])): ?>
    <p style="color:red"><?= $_SESSION['erro'] ?></p>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>
<h1>Bem-vindo ao Sistema Financeiro</h1>
<p>Gerencie suas finanças de forma eficiente com nosso sistema completo de controle de receitas, despesas e muito mais.</p>

<div class="dashboard">
    <!-- Exemplo de um painel de controle simples -->
    <section class="panel">
        <h2>Visão Geral</h2>
        <ul>
            <li><a href="..//pages/visualizar_transacao.php?tipo=receita">Visualizar Receitas</a></li>
            <li><a href="..//pages/visualizar_transacao.php?tipo=despesa">Visualizar Despesas</a></li>
            <li><a href="..//pages/cadastro_transacao.php?tipo=receita">Cadastrar Nova Receita</a></li>
            <li><a href="..//pages/cadastro_transacao.php?tipo=despesa">Cadastrar Nova Despesa</a></li>
        </ul>
    </section>

    <section class="panel">
        <h2>Utilitários</h2>
        <ul>
            <li><a href="..//pages/agendar_pacientes.php">Gerenciar Agenda</a></li>
            <li><a href="..//pages/visualizar_agendamentos.php">Visualizar Agendamentos</a></li>
            <li><a href="..//pages/cadastro_usuario.php">Cadastrar Novo Usuário</a></li>
            <li><a href="..//pages/visualizar_pessoas.php">Visualizar Pessoas</a></li>
        </ul>
    </section>
</div>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
