<?php
session_start();
// Verificar se o administrador esta logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
include '../includes/db_connect.php';

// Sanitizacao e validacao de entrada
$nome_paciente = filter_var($_POST['nome_paciente'], FILTER_SANITIZE_STRING);
$data_agendamento = $_POST['data_agendamento'];
$hora_agendamento = $_POST['hora_agendamento'];
$observacoes_raw = $_POST['descricao'] ?? $_POST['observacoes'] ?? '';
$observacoes = filter_var($observacoes_raw, FILTER_SANITIZE_STRING);

$calendarError = null;
try {
    $configPath = __DIR__ . '/../google_calendar/config_google.php';
    if (!file_exists($configPath)) {
        throw new RuntimeException('Google Calendar config nao encontrado.');
    }
    require_once $configPath;

    $tokenPath = __DIR__ . '/../google_calendar/token.json';
    if (!file_exists($tokenPath)) {
        throw new RuntimeException('Token do Google Calendar nao encontrado.');
    }

    $client = getClient();
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    if (empty($accessToken)) {
        throw new RuntimeException('Token do Google Calendar invalido.');
    }
    $client->setAccessToken($accessToken);

    if ($client->isAccessTokenExpired()) {
        $refreshToken = $client->getRefreshToken();
        if ($refreshToken) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
            if (isset($newToken['error'])) {
                throw new RuntimeException('Token expirado. Refaca a autenticacao.');
            }
            $accessToken = array_merge($accessToken, $newToken);
            file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));
            $client->setAccessToken($accessToken);
        } else {
            throw new RuntimeException('Token expirado. Refaca a autenticacao.');
        }
    }

    $service = new Google\Service\Calendar($client);
    $calendarId = 'primary';
    $timezone = date_default_timezone_get() ?: 'UTC';
    $inicio = new DateTime($data_agendamento . ' ' . $hora_agendamento);
    $fim = clone $inicio;
    $fim->modify('+1 hour');

    $evento = new Google\Service\Calendar\Event([
        'summary' => 'Consulta - ' . $nome_paciente,
        'description' => $observacoes,
        'start' => [
            'dateTime' => $inicio->format(DateTime::RFC3339),
            'timeZone' => $timezone,
        ],
        'end' => [
            'dateTime' => $fim->format(DateTime::RFC3339),
            'timeZone' => $timezone,
        ],
    ]);

    $service->events->insert($calendarId, $evento);
} catch (Throwable $e) {
    $calendarError = $e->getMessage();
}

try {
    if ($calendarError) {
        throw new RuntimeException($calendarError);
    }

    // Inserir agendamento
    $sql = "INSERT INTO agendamentos (nome_paciente, data_agendamento, hora_agendamento, observacoes) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nome_paciente, $data_agendamento, $hora_agendamento, $observacoes]);

    // Redireciona para a pagina de agendamento com status de sucesso
    header("Location: ../views/visualizar_agendamentos.php?status=success");
    exit;
} catch (PDOException $e) {
    // Log de erro e redirecionamento em caso de falha
    error_log("Erro ao inserir agendamento: " . $e->getMessage());
    header("Location: ../pages/agendar_pacientes.php?status=error");
    exit;
} catch (RuntimeException $e) {
    error_log("Erro ao criar evento no Google Calendar: " . $e->getMessage());
    header("Location: ../pages/agendar_pacientes.php?status=calendar_error");
    exit;
}
?>
