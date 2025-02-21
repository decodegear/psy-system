<?php
require __DIR__ . '/../vendor/autoload.php';

function getClient() {
    $client = new Google\Client();
    $client->setAuthConfig(__DIR__ . '/google_calendar.json');
    $client->setScopes(Google\Service\Calendar::CALENDAR);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = __DIR__ . '/token.json';

    // Verifica se o token existe
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);

        // Se o token expirou, tenta renová-lo
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $client->setAccessToken($newToken);
                file_put_contents($tokenPath, json_encode($newToken));
            } else {
                // Se não houver refresh token, exclui o token e pede autenticação novamente
                unlink($tokenPath);
                header("Location: auth.php");
                exit();
            }
        }
    } else {
        // Garante que não está redirecionando infinitamente
        if (!isset($_GET['auth'])) {
            header("Location: auth.php?auth=1");
            exit();
        }
    }

    return $client;
}
?>
