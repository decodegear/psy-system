<?php
ob_start();
include '../includes/header.php';

$messages = [];
$calendarReady = true;
$credentialsPath = __DIR__ . '/../google_calendar/credentials.json';
$tokenPath = __DIR__ . '/../google_calendar/token.json';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/pages/eventos.php';
$basePath = rtrim(str_replace('/pages/eventos.php', '', $scriptPath), '/');
$authorizeUrl = $scheme . '://' . $host . $basePath . '/google_calendar/authorize.php';

if (!file_exists($credentialsPath)) {
    $calendarReady = false;
    $messages[] = 'Arquivo credentials.json nao encontrado. Adicione o arquivo em google_calendar.';
}

if ($calendarReady) {
    require '../google_calendar/config_google.php';
    $client = getClient();

    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    } else {
        $calendarReady = false;
        $messages[] = 'Token nao encontrado. Autorize o Google Calendar para gerar o token.';
    }

    if ($calendarReady && $client->isAccessTokenExpired()) {
        $refreshToken = $accessToken['refresh_token'] ?? $client->getRefreshToken();
        if ($refreshToken) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
            if (!isset($newToken['error'])) {
                $accessToken = array_merge($accessToken, $newToken, ['refresh_token' => $refreshToken]);
                file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));
                $client->setAccessToken($accessToken);
            } else {
                $calendarReady = false;
                $messages[] = 'Token expirado. Refaca a autenticacao para continuar.';
            }
        } else {
            $calendarReady = false;
            $messages[] = 'Token expirado. Refaca a autenticacao para continuar.';
        }
    }
}

$eventos = [];
if ($calendarReady) {
    $service = new Google\Service\Calendar($client);
    $calendarId = '986eb5e79c1f554e4d3b28a83b4c5cedf55cd7ece48d31cb3ac453d67a03648d@group.calendar.google.com';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criar'])) {
        $timezone = 'America/Sao_Paulo';
        $inicioInput = $_POST['data_inicio'] ?? '';
        $fimInput = $_POST['data_fim'] ?? '';

        $inicio = DateTime::createFromFormat('Y-m-d\TH:i', $inicioInput, new DateTimeZone($timezone));
        $fim = DateTime::createFromFormat('Y-m-d\TH:i', $fimInput, new DateTimeZone($timezone));

        if (!$inicio || !$fim) {
            $messages[] = 'Datas invalidas. Verifique data e hora do evento.';
        } else {
            $evento = new Google\Service\Calendar\Event([
                'summary'     => $_POST['titulo'],
                'description' => $_POST['descricao'],
                'start'       => [
                    'dateTime' => $inicio->format('Y-m-d\TH:i:s'),
                    'timeZone' => $timezone,
                ],
                'end'         => [
                    'dateTime' => $fim->format('Y-m-d\TH:i:s'),
                    'timeZone' => $timezone,
                ],
            ]);

            $service->events->insert($calendarId, $evento);
            header('Location: eventos.php');
            exit();
        }
    }

    if (isset($_GET['excluir'])) {
        $service->events->delete($calendarId, $_GET['excluir']);
        header('Location: eventos.php');
        exit();
    }

    $filtro = $_GET['filtro'] ?? 'dia';
    $timezone = 'America/Sao_Paulo';
    $hoje = new DateTime('now', new DateTimeZone($timezone));
    $inicio = clone $hoje;
    $fim = clone $hoje;

    if ($filtro === 'semana') {
        $inicio->modify('monday this week');
        $fim->modify('sunday this week');
    } elseif ($filtro === 'mes') {
        $inicio->modify('first day of this month');
        $fim->modify('last day of this month');
    }

    $inicio->setTime(0, 0, 0);
    $fim->setTime(23, 59, 59);

    $parametros = [
        'timeMin' => $inicio->format(DateTime::RFC3339),
        'timeMax' => $fim->format(DateTime::RFC3339),
        'timeZone' => $timezone,
        'orderBy' => 'startTime',
        'singleEvents' => true,
    ];

    $eventos = $service->events->listEvents($calendarId, $parametros);
}
?>

<main class="transactions-page">
    <section class="page-hero">
        <div>
            <p class="page-eyebrow">Agenda</p>
            <h1>Gerenciar Eventos</h1>
            <p class="page-subtitle">Sincronize eventos com o Google Calendar.</p>
        </div>
        <a class="btn btn-outline-primary page-cta" href="<?php echo $authorizeUrl; ?>">Autorizar Google</a>
    </section>

    <?php if (!empty($messages)): ?>
        <section class="alert-card">
            <?php foreach ($messages as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="table-card">
        <h2>Criar Novo Evento</h2>
        <form method="POST" class="form-grid">
            <div class="form-field">
                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-field">
                <label for="data_inicio">Data Inicio:</label>
                <input type="datetime-local" id="data_inicio" name="data_inicio" required>
            </div>
            <div class="form-field">
                <label for="data_fim">Data Fim:</label>
                <input type="datetime-local" id="data_fim" name="data_fim" required>
            </div>
            <div class="form-field form-field--full">
                <label for="descricao">Descricao:</label>
                <textarea id="descricao" name="descricao"></textarea>
            </div>
            <div class="form-field form-field--full">
                <button type="submit" name="criar" class="btn btn-primary">Criar Evento</button>
            </div>
        </form>
    </section>

    <section class="table-card">
        <div class="dashboard-actions">
            <a href="eventos.php?filtro=dia" class="btn btn-outline-primary">Hoje</a>
            <a href="eventos.php?filtro=semana" class="btn btn-outline-primary">Semana</a>
            <a href="eventos.php?filtro=mes" class="btn btn-outline-primary">Mes</a>
        </div>

        <h2>Lista de Eventos</h2>
        <ul class="event-list">
            <?php if ($calendarReady && $eventos): ?>
                <?php foreach ($eventos->getItems() as $evento): ?>
                    <li class="event-item">
                        <div>
                            <strong><?php echo htmlspecialchars($evento->getSummary() ?? 'Sem titulo', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <div class="event-meta">
                                <?php
                                $start = 'Data nao definida';
                                if ($evento->getStart()) {
                                    $start = $evento->getStart()->getDateTime() ?? $evento->getStart()->getDate() ?? 'Data nao definida';
                                }
                                echo htmlspecialchars($start, ENT_QUOTES, 'UTF-8');
                                ?>
                            </div>
                        </div>
                        <div class="event-actions">
                            <a href="eventos.php?excluir=<?php echo htmlspecialchars($evento->getId(), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="event-item">
                    <div>
                        <strong>Nenhum evento encontrado.</strong>
                        <div class="event-meta">Autorize e sincronize para listar eventos.</div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
<?php ob_end_flush(); ?>