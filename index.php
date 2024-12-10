<?php 
session_start();
$base = ($_SERVER['DOCUMENT_ROOT']);
//include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
include $base.'/includes/header.php'; 


// Mensagem de erro genérica para evitar exposiçao de dados
if (isset($_SESSION['erro'])): ?>
    <p style="color:red">Ocorreu um erro. Tente novamente ou contate o suporte.</p>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>

<h2> Gestão Psicológica 1.0</h2>
<p>Gerencie seus atendimentos, sessões, prontuários, receitas e finanças de forma eficiente com nosso sistema completo de controle de receitas, despesas e muito mais.</p>

<div class="dashboard container">
    <!-- Exemplo de um painel de controle simples, agora com classes Bootstrap -->
    <section class="panel row">
        <h2>Visão Geral</h2>
        <ul class="list-group">
            <li class="list-group-item"><a href="/views/visualizar_transacao.php?tipo=receita">Visualizar Receitas</a></li>
            <li class="list-group-item"><a href="/views/visualizar_transacao.php?tipo=despesa">Visualizar Despesas</a></li>
            <li class="list-group-item"><a href="/pages/cadastro_transacao.php?tipo=receita">Cadastrar Nova Receita</a></li>
            <li class="list-group-item"><a href="/pages/cadastro_transacao.php?tipo=despesa">Cadastrar Nova Despesa</a></li>
        </ul>
    </section>

    <section class="panel row">
        <h2>Utilitários</h2>
        <ul class="list-group">
            <li class="list-group-item"><a href="/pages/agendar_pacientes.php">Gerenciar Agenda</a></li>
            <li class="list-group-item"><a href="/views/visualizar_agendamentos.php">Visualizar Agendamentos</a></li>
            <li class="list-group-item"><a href="/pages/cadastrar_usuario.php">Cadastrar Novo Usuário</a></li>
            <li class="list-group-item"><a href="/views/visualizar_pessoa.php">Visualizar Pessoas</a></li>
        </ul>
    </section>
</div>

<?php 
// Inclui o rodapé
include 'includes/footer.php'; 
?>
