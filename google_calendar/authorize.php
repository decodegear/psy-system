<?php
require __DIR__ . '/config_google.php';

$client = getClient();
$redirectUri = 'http://localhost/psy-system/google_calendar/authorize.php';
$client->setRedirectUri($redirectUri);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        exit('Authorization failed: ' . $token['error']);
    }
    file_put_contents(__DIR__ . '/token.json', json_encode($token, JSON_PRETTY_PRINT));
    header('Location: /psy-system/pages/eventos.php');
    exit;
}
$authUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Autorizar Google Calendar</title>
</head>
<body>
  <h1>Autorizar Google Calendar</h1>
  <p><a href="<?php echo htmlspecialchars($authUrl); ?>">Clique para autorizar</a></p>
</body>
</html>
