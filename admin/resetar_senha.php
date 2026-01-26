<?php
session_start();
include '../includes/db_connect.php';

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

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$token = trim($token);

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $senha = $_POST['senha'] ?? '';
    $confirmacao = $_POST['confirmacao'] ?? '';

    if ($token === '') {
        $error = "Token inválido.";
    } elseif ($senha === '' || $confirmacao === '') {
        $error = "Preencha todos os campos de senha.";
    } elseif ($senha !== $confirmacao) {
        $error = "As senhas não conferem.";
    } else {
        $tokenHash = hash('sha256', $token);
        $stmt = $conn->prepare("SELECT pr.id, pr.usuario_id, pr.expires_at
                                FROM password_resets pr
                                WHERE pr.token_hash = :token_hash
                                  AND pr.used_at IS NULL
                                  AND pr.expires_at >= CURRENT_TIMESTAMP
                                LIMIT 1");
        $stmt->execute([':token_hash' => $tokenHash]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $error = "Token inválido ou expirado.";
        } else {
            $novaSenha = password_hash($senha, PASSWORD_DEFAULT);
            $conn->beginTransaction();

            $updateUser = $conn->prepare("UPDATE usuarios SET senha = :senha, atualizado_em = CURRENT_TIMESTAMP WHERE id = :id");
            $updateUser->execute([
                ':senha' => $novaSenha,
                ':id' => $reset['usuario_id']
            ]);

            $markUsed = $conn->prepare("UPDATE password_resets SET used_at = CURRENT_TIMESTAMP WHERE id = :id");
            $markUsed->execute([':id' => $reset['id']]);

            $conn->commit();

            $success = "Senha atualizada com sucesso. Você já pode fazer login.";
        }
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
    <title>Redefinir Senha</title>
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
                            <h2 class="login-card__title">Redefinir senha</h2>
                            <p class="login-card__subtitle">Crie uma nova senha de acesso</p>
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
                        <div class="login-links">
                            <a href="login.php" class="login-link">Ir para login</a>
                            <a href="../index.php" class="login-link">Voltar ao site</a>
                        </div>
                    <?php else: ?>
                        <form method="post" action="resetar_senha.php" class="login-form">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="mb-3">
                                <label for="senha" class="form-label">Nova senha</label>
                                <input type="password" id="senha" name="senha" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmacao" class="form-label">Confirmar senha</label>
                                <input type="password" id="confirmacao" name="confirmacao" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <input type="submit" value="Atualizar senha" class="btn btn-primary login-submit">
                            </div>
                            <p class="login-helper">O link expira em 1 hora.</p>
                            <div class="login-links">
                                <a href="login.php" class="login-link">Voltar ao login</a>
                                <a href="../index.php" class="login-link">Voltar ao site</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php include '../includes/footer.php'; ?>