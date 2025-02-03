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

<div class="container mt-5">
    <h1 class="text-left montserrat-text">Olá, <?= htmlspecialchars($_SESSION['admin_nome']) ?></h1>

    <div class="row mt-4">
        <!-- Coluna para os cadastros -->
        <div class="col-md-4">

            <ul class="dashboard-list">
                <li><a href="../views/visualizar_agendamentos.php">Agendamentos</a></li>
                <!-- <?php

                        try {
                            // Consultar todos os agendamentos
                            $sql = "SELECT id, nome_paciente, data_agendamento, hora_agendamento, observacoes FROM agendamentos ORDER BY data_agendamento ASC, hora_agendamento ASC";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            echo "<p>Erro ao carregar agendamentos: " . htmlspecialchars($e->getMessage()) . "</p>";
                            exit;
                        }
                        ?>

                <?php if (count($agendamentos) > 0): ?>
                    <div class="container my-4">
                        <table class="table table-striped table-bordered">
                            <thead class="table-item">
                                <tr>
                                    <th>Data</th>
                                    <th>Paciente</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                                        <td><?php echo htmlspecialchars($agendamento['nome_paciente']); ?></td>
                                        <td><?php echo htmlspecialchars($agendamento['hora_agendamento']); ?></td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum agendamento encontrado.</p>
                    <?php endif; ?> -->
                <table class="table table-striped table-bordered">
                    <tbody>
                        <iframe src="https://calendar.google.com/calendar/embed?height=560&wkst=1&ctz=America%2FSao_Paulo&mode=WEEK&title=Alexandre%20Batista&showDate=0&showCalendars=0&showTz=0&src=OTg2ZWI1ZTc5YzFmNTU0ZTRkM2IyOGE4M2I0YzVjZWRmNTVjZDdlY2U0OGQzMWNiM2FjNDUzZDY3YTAzNjQ4ZEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=cHQuYnJhemlsaWFuI2hvbGlkYXlAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&color=%23C0CA33&color=%230B8043" style="border-width:0" width="100%" height="560" frameborder="0" scrolling="no"></iframe>
                    </tbody>
                </table>
        </div>
        </ul>
    </div>

    <!-- Coluna para Despesas -->
    <div class="col-md-4">

        <ul class="dashboard-list">
            <li><a href="../views/visualizar_transacao.php?tipo=despesa">Despesas</a></li>
            <?php

            // Obter total de despesas
            $totalStmt = $conn->prepare("SELECT SUM(valor) as total FROM transacoes WHERE tipo = 'despesa'");
            $totalStmt->execute();
            $totalDespesas = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Obter as despesas mais recentes
            $recentStmt = $conn->prepare("SELECT nome, valor, data_vencimento FROM transacoes WHERE tipo = 'despesa' ORDER BY data_vencimento DESC LIMIT 5");
            $recentStmt->execute();
            $despesasRecentes = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="container my-4">
                <table class="table ">
                    <thead class="table-item">
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
                                    <td><?php echo date('d/m/Y', htmlspecialchars(strtotime($despesa['data_vencimento']))); ?></td>
                                    <td><?= htmlspecialchars($despesa['nome']) ?></td>
                                    <td><?= htmlspecialchars(number_format($despesa['valor'], 2, ',', '.')) ?></td>

                                </tr>
                            <?php endforeach; ?>
                            <div class="mb-3">
                                <td>
                                    <h4>Total:
                                </td>
                                <td></td>
                                <td>R$ <?= number_format($totalDespesas, 2, ',', '.'); ?></h4>

                                </td>

                            </div>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma despesa recente.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </ul>
    </div>
    <!-- Coluna para Despesas -->
    <div class="col-md-4">

        <ul class="dashboard-list">
            <li><a href="../views/visualizar_transacao.php?tipo=receita">Receitas</a></li>
            <?php

            // Obter total de despesas
            $totalStmt = $conn->prepare("SELECT SUM(valor) as total FROM transacoes WHERE tipo = 'receita'");
            $totalStmt->execute();
            $totalDespesas = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Obter as despesas mais recentes
            $recentStmt = $conn->prepare("SELECT nome, valor, data_vencimento FROM transacoes WHERE tipo = 'receita' ORDER BY data_vencimento DESC LIMIT 5");
            $recentStmt->execute();
            $despesasRecentes = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="container my-4">

                <table class="table table-striped table-bordered">
                    <thead class="table-item">
                        <tr>
                            <th>Vencimento</th>
                            <th>Nome</th>
                            <th>Valor</th>

                        </tr>
                    </thead>
                    <tbody>
                        <!-- Mantive $despesas para aproveitar código -->
                        <?php if (!empty($despesasRecentes)): ?>
                            <?php foreach ($despesasRecentes as $despesa): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', htmlspecialchars(strtotime($despesa['data_vencimento']))); ?></td>
                                    <td><?= htmlspecialchars($despesa['nome']) ?></td>
                                    <td><?= htmlspecialchars(number_format($despesa['valor'], 2, ',', '.')) ?></td>

                                </tr>
                            <?php endforeach; ?>
                            <div class="mb-3">
                                <td>
                                    <h4>Total:
                                </td>
                                <td></td>
                                <td>R$ <?= number_format($totalDespesas, 2, ',', '.'); ?></h4>
                                </td>
                            </div>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma despesa recente.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </ul>
    </div>
</div>
</div>

<?php
include '../includes/footer.php';
?>