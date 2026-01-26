<?php
session_start();
include '../includes/db_connect.php';
include '../includes/mailer.php';

// Verificar se a conexão foi estabelecida
if (!isset($conn) || $conn === null) {
    die("Erro: Conexão com o banco de dados não estabelecida.");
}

function ensurePasswordResetTable(PDO $conn)
{
    $conn->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario_id INTEGER NOT NULL,
        token_hash TEXT NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        used_at DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    );");
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_usuario ON password_resets(usuario_id);");
    $conn->exec("CREATE INDEX IF NOT EXISTS idx_password_resets_token ON password_resets(token_hash);");
}

ensurePasswordResetTable($conn);

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $cpfClean = preg_replace('/\D/', '', $cpf);

    if ($email === '' || $cpf === '') {
        $error = "Preencha email e CPF para continuar.";
    } else {
        $sql = "SELECT id, nome, email FROM usuarios 
                WHERE email = :email 
                  AND (cpf = :cpf OR REPLACE(REPLACE(cpf, '.', ''), '-', '') = :cpf_clean)
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':cpf' => $cpf,
            ':cpf_clean' => $cpfClean
        ]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

            $cleanup = $conn->prepare("DELETE FROM password_resets WHERE usuario_id = :usuario_id");
            $cleanup->execute([':usuario_id' => $usuario['id']]);

            $insert = $conn->prepare("INSERT INTO password_resets (usuario_id, token_hash, expires_at) VALUES (:usuario_id, :token_hash, :expires_at)");
            $insert->execute([
                ':usuario_id' => $usuario['id'],
                ':token_hash' => $tokenHash,
                ':expires_at' => $expiresAt
            ]);

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $path = rtrim(dirname($_SERVER['REQUEST_URI'] ?? '/'), '/');
            $resetLink = $scheme . '://' . $host . $path . '/resetar_senha.php?token=' . urlencode($rawToken);

            $subject = "Recuperação de senha";
            $message = "Olá " . $usuario['nome'] . ",\n\n" .
                "Recebemos uma solicitação para redefinir sua senha.\n" .
                "Clique no link abaixo para criar uma nova senha (válido por 1 hora):\n\n" .
                $resetLink . "\n\n" .
                "Se você não solicitou, ignore este email.\n";

            sendMailPitMessage($usuario['email'], $usuario['nome'], $subject, $message);
        }

        $success = "Se os dados estiverem corretos, enviamos um email com o link de recuperação.";
    }
}

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-page">
    <div class="container login-wrapper">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-10 col-lg-6">
                <div class="login-card">
                    <div class="login-card__header">
                        <div class="login-card__icon" aria-hidden="true">
                            <span>Ψ</span>
                        </div>
                        <div>
                            <h2 class="login-card__title">Recuperação de senha</h2>
                            <p class="login-card__subtitle">Confirme seus dados para receber o link</p>
                        </div>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="recuperar_senha.php" class="login-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" required>
                        </div>
                        <div class="d-grid">
                            <input type="submit" value="Enviar link" class="btn btn-primary login-submit">
                        </div>
                        <p class="login-helper">O link expira em 1 hora. Verifique a caixa de spam.</p>
                        <div class="login-links">
                            <a href="login.php" class="login-link">Voltar ao login</a>
                            <a href="../index.php" class="login-link">Voltar ao site</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php include '../includes/footer.php'; ?>