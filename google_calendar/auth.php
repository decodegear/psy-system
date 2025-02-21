<?php
require 'config_google.php';

$client = new Google\Client();
$client->setAuthConfig(__DIR__ . '/google_calendar.json');
$client->setRedirectUri('http://localhost/callback.php');
$client->addScope(Google\Service\Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

$authUrl = $client->createAuthUrl();

echo "Acesse este link para autenticar: <a href='$authUrl'>$authUrl</a>";
?>
