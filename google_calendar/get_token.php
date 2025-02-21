<?php
require '../google_calendar/config_google.php';

if (!isset($_GET['code'])) {
    exit('Código de autenticação ausente.');
}

$client = getClient();
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
file_put_contents('token.json', json_encode($token));

echo "Token salvo com sucesso!";
?>
