<?php
require 'config_google.php';

$client = getClient();
$tokenPath = 'token.json';
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
}

if ($client->isAccessTokenExpired()) {
    exit('Token expirado. Refaça a autenticação.');
}

$service = new Google\Service\Calendar($client);
$event = new Google\Service\Calendar\Event([
    'summary' => 'Consulta de Psicologia',
    'location' => 'Online',
    'description' => 'Sessão de terapia com cliente.',
    'start' => ['dateTime' => '2025-02-21T10:12:00-03:00'],
    'end' => ['dateTime' => '2025-02-21T11:12:30-03:00'],
    'attendees' => [['email' => 'cliente@example.com']],
]);

$calendarId = 'primary'; // Ou o ID de uma agenda específica
$event = $service->events->insert($calendarId, $event);

echo "Evento criado: " . $event->htmlLink;
?>
