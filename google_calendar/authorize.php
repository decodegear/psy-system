<?php
require __DIR__ . '/config_google.php';

$client = getClient();
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/google_calendar/authorize.php';
$redirectUri = $scheme . '://' . $host . $scriptPath;
$basePath = rtrim(str_replace('/google_calendar/authorize.php', '', $scriptPath), '/');
$client->setRedirectUri($redirectUri);

if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  if (isset($token['error'])) {
    exit('Authorization failed: ' . $token['error']);
  }
  file_put_contents(__DIR__ . '/token.json', json_encode($token, JSON_PRETTY_PRINT));
  header('Location: ' . $scheme . '://' . $host . $basePath . '/pages/eventos.php');
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