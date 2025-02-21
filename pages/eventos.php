<?php
require '../includes/header.php';
require '../google_calendar/config_google.php';

$client = getClient();
$tokenPath = '../google_calendar/token.json';

// Verifica se o token existe
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
}

// Se o token expirou, interrompe a execução
if ($client->isAccessTokenExpired()) {
    exit('Token expirado. Refaça a autenticação.');
}

$service = new Google\Service\Calendar($client);
$calendarId = 'primary';

// Criar evento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criar'])) {
    $evento = new Google\Service\Calendar\Event([
        'summary'     => $_POST['titulo'],
        'description' => $_POST['descricao'],
        'start'       => ['dateTime' => $_POST['data_inicio']],
        'end'         => ['dateTime' => $_POST['data_fim']],
    ]);

    $service->events->insert($calendarId, $evento);
    header("Location: eventos.php");
    exit();
}

// Excluir evento
if (isset($_GET['excluir'])) {
    $service->events->delete($calendarId, $_GET['excluir']);
    header("Location: eventos.php");
    exit();
}

// Definição do período de filtragem
$filtro = $_GET['filtro'] ?? 'dia';
$hoje = new DateTime();
$inicio = clone $hoje;
$fim = clone $hoje;

if ($filtro === 'semana') {
    $inicio->modify('monday this week');
    $fim->modify('sunday this week');
} elseif ($filtro === 'mes') {
    $inicio->modify('first day of this month');
    $fim->modify('last day of this month');
}

$parametros = [
    'timeMin' => $inicio->format('Y-m-d\T00:00:00\Z'),
    'timeMax' => $fim->format('Y-m-d\T23:59:59\Z'),
    'orderBy' => 'startTime',
    'singleEvents' => true,
];

// Buscar eventos filtrados
$eventos = $service->events->listEvents($calendarId, $parametros);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eventos</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/agenda.css">
</head>
<body>
    <div class="container">
        <h2>Criar Novo Evento</h2>
        <form method="POST" class="form-evento">
            <label>Título:</label>
            <input type="text" name="titulo" required>
            <label>Descrição:</label>
            <textarea name="descricao"></textarea>
            <label>Data Início:</label>
            <input type="datetime-local" name="data_inicio" required>
            <label>Data Fim:</label>
            <input type="datetime-local" name="data_fim" required>
            <button type="submit" name="criar">Criar Evento</button>
        </form>

        <h2>Lista de Eventos</h2>

        <div class="filtros">
            <a href="eventos.php?filtro=dia" class="btn">Hoje</a>
            <a href="eventos.php?filtro=semana" class="btn">Semana</a>
            <a href="eventos.php?filtro=mes" class="btn">Mês</a>
        </div>

        <ul class="lista-eventos">
            <?php foreach ($eventos->getItems() as $evento): ?>
                <li class="evento">
                    <strong><?= htmlspecialchars($evento->getSummary() ?? 'Sem título', ENT_QUOTES, 'UTF-8'); ?></strong> 
                    <span>
                        <?php 
                        $start = "Data não definida";
                        if ($evento->getStart()) {
                            $start = $evento->getStart()->getDateTime() ?? $evento->getStart()->getDate() ?? "Data não definida";
                        }
                        ?>
                        <?= htmlspecialchars($start, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <a href="eventos.php?excluir=<?= htmlspecialchars($evento->getId(), ENT_QUOTES, 'UTF-8'); ?>" 
                       class="btn-excluir"
                       onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</body>
</html>

<?php require '../includes/footer.php'; ?>
