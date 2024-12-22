<?php
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$servidor = $_SERVER['HTTP_HOST'];
$url = $_SERVER['REQUEST_URI'];
$base_url = $protocolo.$servidor;
echo "Base url é: ". $base_url;

    // Verifica se a sessão já foi iniciada
if (session_status() === PHP_SESSION_NONE) { 
    
    // Inicia a sessão apenas se ela não estiver ativa 
    session_start(); 
}
    // Inclui o arquivo de configuração
include 'db_connect.php'; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!--Meta Tags Requeridas-->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <!-- Link para o Bootstrap -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <!-- Link para o estilo CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Link para o Manifesto do PWA -->
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    
    <!--Base url -->
    <base href="" >
  
    <title>Gestão de Atendimento</title>
</head>
<body class="container">
