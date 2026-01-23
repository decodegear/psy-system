<?php
session_start();

include 'includes/header.php';
// Mensagem de erro genérica para evitar exposiçao de dados
if (isset($_SESSION['erro'])): ?>
    <p style="color:red">Ocorreu um erro. Tente novamente ou contate o suporte.</p>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>


<main class="home-page">
    <section class="dashboard-hero home-hero">
        <div>
            <p class="dashboard-eyebrow">Sistema de gest&atilde;o</p>
            <h1 class="montserrat-text">Alexandre Batista - Psic&oacute;logo</h1>
            <p class="dashboard-subtitle">Gerencie atendimentos, sess&otilde;es, prontu&aacute;rios, receitas e finan&ccedil;as com uma vis&atilde;o clara do consult&oacute;rio.</p>
        </div>
        <div class="dashboard-actions">
            <a class="btn btn-primary" href="/views/visualizar_transacao.php?tipo=receita">Ver receitas</a>
            <a class="btn btn-outline-primary" href="/pages/agendar_pacientes.php">Agendar paciente</a>
        </div>
    </section>

    <section class="home-grid">
        <div class="home-card">
            <header class="dashboard-card-header">
                <div>
                    <h2>Vis&atilde;o geral</h2>
                    <p>Finan&ccedil;as e relat&oacute;rios.</p>
                </div>
            </header>
            <ul class="home-links">
                <li><a href="/views/visualizar_transacao.php?tipo=receita">Visualizar receitas</a></li>
                <li><a href="/views/visualizar_transacao.php?tipo=despesa">Visualizar despesas</a></li>
                <li><a href="/pages/cadastro_transacao.php?tipo=receita">Cadastrar nova receita</a></li>
                <li><a href="/pages/cadastro_transacao.php?tipo=despesa">Cadastrar nova despesa</a></li>
                <li><a href="/views/view_relatorio.php">Relat&oacute;rio financeiro</a></li>
            </ul>
        </div>

        <div class="home-card">
            <header class="dashboard-card-header">
                <div>
                    <h2>Utilit&aacute;rios</h2>
                    <p>Agenda e cadastro de pessoas.</p>
                </div>
            </header>
            <ul class="home-links">
                <li><a href="/pages/agendar_pacientes.php">Gerenciar agenda</a></li>
                <li><a href="/views/visualizar_agendamentos.php">Visualizar agendamentos</a></li>
                <li><a href="/pages/cadastrar_usuario.php">Cadastrar novo usu&aacute;rio</a></li>
                <li><a href="/views/visualizar_pessoa.php">Visualizar pessoas</a></li>
            </ul>
        </div>
    </section>
</main>
<?php
// Inclui o rodapé
include 'includes/footer.php';
?>
