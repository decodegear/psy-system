<?php
session_start();
session_unset();  // Limpa as variáveis de sessão
session_destroy();  // Destroi a sessão

// Redirecionar para a página inicial
header("Location: ../index.php");
exit;
