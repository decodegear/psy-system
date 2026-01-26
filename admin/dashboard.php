<?php
session_start();

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
        header("Location: ../admin/login.php");  // Redireciona para a página inicial
        exit;
    }
}

// Atualiza o timestamp da última atividade
$_SESSION['last_activity'] = time();

include '../includes/header.php';
?>

<main class="dashboard-page">
    <section class="dashboard-hero">
        <div>
            <p class="dashboard-eyebrow">Painel administrativo</p>
            <h1 class="montserrat-text">Ol&aacute;, <?= htmlspecialchars($_SESSION['admin_nome']) ?></h1>
            <p class="dashboard-subtitle">Vis&atilde;o clara das sess&otilde;es, agenda e finan&ccedil;as em um s&oacute; lugar.</p>
        </div>
        <div class="dashboard-actions">
            <a class="btn btn-primary" href="../pages/agendar_pacientes.php">Novo agendamento</a>
            <a class="btn btn-outline-primary" href="../pages/cadastro_transacao.php?tipo=receita">Nova receita</a>
            <a class="btn btn-outline-primary" href="../pages/cadastro_transacao.php?tipo=despesa">Nova despesa</a>
        </div>
    </section>

    <div class="row g-4 dashboard-grid">
        <div class="col-12">
            <section class="dashboard-card">
                <header class="dashboard-card-header">
                    <div>
                        <h2><i class="bi bi-calendar-week"></i> Agenda</h2>
                        <p>Resumo semanal de atendimentos.</p>
                    </div>
                    <a class="dashboard-link" href="../views/visualizar_agendamentos.php">Ver tudo</a>
                </header>
                <div class="dashboard-card-body">
                    <div class="calendar-embed">
                        <iframe src="https://calendar.google.com/calendar/embed?height=560&wkst=1&ctz=America%2FSao_Paulo&mode=WEEK&title=Alexandre%20Batista&showDate=0&showCalendars=0&showTz=0&src=OTg2ZWI1ZTc5YzFmNTU0ZTRkM2IyOGE4M2I0YzVjZWRmNTVjZDdlY2U0OGQzMWNiM2FjNDUzZDY3YTAzNjQ4ZEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=cHQuYnJhemlsaWFuI2hvbGlkYXlAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&color=%23C0CA33&color=%230B8043" style="border-width:0" width="100%" height="560" frameborder="0" scrolling="no"></iframe>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="dashboard-card">
                <header class="dashboard-card-header">
                    <div>
                        <h2><i class="bi bi-arrow-down-circle"></i> Despesas</h2>
                        <p>Ultimos 5 vencimentos registrados.</p>
                    </div>
                    <a class="dashboard-link" href="../views/visualizar_transacao.php?tipo=despesa">Ver detalhes</a>
                </header>
                <div class="dashboard-card-body">
                    <?php
                    $totalDespesas = 0.0;
                    $despesasRecentes = [];

                    if ($conn instanceof PDO) {
                        // Obter total de despesas
                        $totalStmt = $conn->prepare("SELECT SUM(valor) as total FROM transacoes WHERE tipo = 'despesa'");
                        $totalStmt->execute();
                        $totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
                        $totalDespesas = (float)($totalRow['total'] ?? 0);

                        // Obter as despesas mais recentes
                        $recentStmt = $conn->prepare("SELECT nome, valor, data_vencimento FROM transacoes WHERE tipo = 'despesa' ORDER BY data_vencimento DESC LIMIT 5");
                        $recentStmt->execute();
                        $despesasRecentes = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    ?>

                    <div class="table-wrapper">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Vencimento</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($despesasRecentes)): ?>
                                    <?php foreach ($despesasRecentes as $despesa): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($despesa['data_vencimento'])); ?></td>
                                            <td><?= htmlspecialchars($despesa['nome']) ?></td>
                                            <td><?= htmlspecialchars(number_format($despesa['valor'], 2, ',', '.')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Nenhuma despesa recente.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-total">Total do per&iacute;odo: R$ <?= number_format($totalDespesas, 2, ',', '.'); ?></div>
                </div>
            </section>
        </div>

        <div class="col-lg-6">
            <section class="dashboard-card">
                <header class="dashboard-card-header">
                    <div>
                        <h2><i class="bi bi-arrow-up-circle"></i> Receitas</h2>
                        <p>Entradas mais recentes do consult&oacute;rio.</p>
                    </div>
                    <a class="dashboard-link" href="../views/visualizar_transacao.php?tipo=receita">Ver detalhes</a>
                </header>
                <div class="dashboard-card-body">
                    <?php
                    $totalReceitas = 0.0;
                    $despesasRecentes = [];

                    if ($conn instanceof PDO) {
                        // Obter total de receitas
                        $totalStmt = $conn->prepare("SELECT SUM(valor) as total FROM transacoes WHERE tipo = 'receita'");
                        $totalStmt->execute();
                        $totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
                        $totalReceitas = (float)($totalRow['total'] ?? 0);

                        // Obter as receitas mais recentes
                        $recentStmt = $conn->prepare("SELECT nome, valor, data_vencimento FROM transacoes WHERE tipo = 'receita' ORDER BY data_vencimento DESC LIMIT 5");
                        $recentStmt->execute();
                        $despesasRecentes = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    ?>

                    <div class="table-wrapper">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Vencimento</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($despesasRecentes)): ?>
                                    <?php foreach ($despesasRecentes as $despesa): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($despesa['data_vencimento'])); ?></td>
                                            <td><?= htmlspecialchars($despesa['nome']) ?></td>
                                            <td><?= htmlspecialchars(number_format($despesa['valor'], 2, ',', '.')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Nenhuma receita recente.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-total">Total do per&iacute;odo: R$ <?= number_format($totalReceitas, 2, ',', '.'); ?></div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>