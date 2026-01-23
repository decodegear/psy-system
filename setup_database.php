<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Banco de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .success {
            color: green;
            padding: 10px;
            background: #d4edda;
            border: 1px solid green;
            margin: 10px 0;
            border-radius: 5px;
        }

        .error {
            color: red;
            padding: 10px;
            background: #f8d7da;
            border: 1px solid red;
            margin: 10px 0;
            border-radius: 5px;
        }

        .info {
            color: blue;
            padding: 10px;
            background: #d1ecf1;
            border: 1px solid blue;
            margin: 10px 0;
            border-radius: 5px;
        }

        pre {
            background: #f4f4f4;
            padding: 10px;
            overflow-x: auto;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background: #0056b3;
        }

        .file-path {
            font-family: monospace;
            background: #f4f4f4;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <h1>🔧 Configuração do Banco de Dados SQLite</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_db'])) {
        echo "<div class='info'>📦 Iniciando configuração do banco de dados SQLite...</div>";

        // Configurações do banco de dados SQLite
        $db_file = __DIR__ . '/database/psy_system.db';
        $db_dir = dirname($db_file);

        try {
            // Criar diretório do banco se não existir
            if (!is_dir($db_dir)) {
                mkdir($db_dir, 0755, true);
                echo "<div class='success'>✓ Diretório do banco de dados criado!</div>";
            }

            // Apagar banco existente para recriar
            if (file_exists($db_file)) {
                unlink($db_file);
                echo "<div class='info'>♻️ Banco de dados anterior removido.</div>";
            }

            // Conectar ao SQLite (cria o arquivo se não existir)
            $conn = new PDO("sqlite:" . $db_file);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "<div class='success'>✓ Conectado ao SQLite com sucesso!</div>";
            echo "<div class='info'>📁 Localização: <span class='file-path'>" . realpath($db_file) . "</span></div>";

            // Ler o arquivo SQL
            $sqlFile = __DIR__ . '/database_sqlite.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception("Arquivo database_sqlite.sql não encontrado!");
            }

            $sql = file_get_contents($sqlFile);
            echo "<div class='info'>📄 Arquivo SQL carregado. Executando comandos...</div>";

            // Habilitar chaves estrangeiras e melhorar performance
            $conn->exec('PRAGMA foreign_keys = ON;');
            $conn->exec('PRAGMA journal_mode = WAL;');

            // Função para dividir SQL respeitando triggers (BEGIN...END)
            function parseSQLCommands($sql)
            {
                $commands = [];
                $current = '';
                $inTrigger = false;

                $lines = explode("\n", $sql);

                foreach ($lines as $line) {
                    $trimmed = trim($line);

                    // Detectar início de trigger
                    if (stripos($trimmed, 'CREATE TRIGGER') === 0) {
                        $inTrigger = true;
                    }

                    $current .= $line . "\n";

                    // Detectar fim de comando
                    if (substr($trimmed, -1) === ';') {
                        // Se estamos em um trigger, só terminar no END;
                        if ($inTrigger) {
                            if (stripos($trimmed, 'END;') === 0 || preg_match('/END\s*;/i', $trimmed)) {
                                $commands[] = trim($current);
                                $current = '';
                                $inTrigger = false;
                            }
                        } else {
                            $commands[] = trim($current);
                            $current = '';
                        }
                    }
                }

                if (!empty(trim($current))) {
                    $commands[] = trim($current);
                }

                return $commands;
            }

            $commands = parseSQLCommands($sql);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($commands as $command) {
                $command = trim($command);

                // Ignorar comentários e linhas vazias
                if (
                    empty($command) ||
                    strpos($command, '--') === 0 ||
                    strpos($command, 'PRAGMA') === 0
                ) {
                    continue;
                }

                try {
                    $conn->exec($command);
                    $successCount++;
                } catch (PDOException $e) {
                    // Ignorar alguns erros comuns do SQLite
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        $errorCount++;
                        $errors[] = "Erro: " . $e->getMessage() . "<br>Comando: " . htmlspecialchars(substr($command, 0, 100)) . "...";
                    }
                }
            }

            echo "<div class='success'><strong>✓ Comandos executados com sucesso: $successCount</strong></div>";

            if ($errorCount > 0) {
                echo "<div class='error'><strong>✗ Comandos com erro: $errorCount</strong><br>";
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
                echo "</div>";
            }

            echo "<div class='success'>✓ Banco de dados SQLite configurado e acessível!</div>";

            // Verificar tabelas criadas
            $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo "<div class='info'><strong>📊 Tabelas criadas:</strong><br>";
            echo "<ul>";
            foreach ($tables as $table) {
                if ($table === 'sqlite_sequence') continue; // Ignorar tabela interna do SQLite

                // Contar registros
                $countStmt = $conn->query("SELECT COUNT(*) FROM `$table`");
                $count = $countStmt->fetchColumn();
                echo "<li><strong>$table</strong> ($count registros)</li>";
            }
            echo "</ul></div>";

            // Verificar views criadas
            $viewStmt = $conn->query("SELECT name FROM sqlite_master WHERE type='view' ORDER BY name");
            $views = $viewStmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($views)) {
                echo "<div class='info'><strong>👁️ Views criadas:</strong><br>";
                echo "<ul>";
                foreach ($views as $view) {
                    echo "<li>$view</li>";
                }
                echo "</ul></div>";
            }

            // Informações sobre o arquivo do banco
            $fileSize = filesize($db_file);
            $fileSizeKB = round($fileSize / 1024, 2);

            echo "<div class='success'><h2>✓ Configuração concluída com sucesso!</h2>";
            echo "<p><strong>📈 Informações do banco:</strong></p>";
            echo "<ul>";
            echo "<li>Tamanho do arquivo: $fileSizeKB KB</li>";
            echo "<li>Localização: <span class='file-path'>" . realpath($db_file) . "</span></li>";
            echo "<li>Tipo: SQLite 3</li>";
            echo "</ul>";

            echo "<p><strong>🔑 Credenciais de acesso:</strong></p>";
            echo "<pre>";
            echo "Email: admin@email.com\n";
            echo "Senha: 0389\n";
            echo "</pre>";
            echo "<p><a href='admin/login.php'><button type='button'>🚀 Ir para Login</button></a> ";
            echo "<a href='index.php'><button type='button'>🏠 Ir para Home</button></a></p>";
            echo "</div>";
        } catch (PDOException $e) {
            echo "<div class='error'><strong>✗ Erro de conexão:</strong> " . $e->getMessage() . "</div>";
        } catch (Exception $e) {
            echo "<div class='error'><strong>✗ Erro:</strong> " . $e->getMessage() . "</div>";
        }
    } else {
    ?>
        <div class="info">
            <h2>📋 Instruções:</h2>
            <p>Este script irá criar um banco de dados <strong>SQLite</strong> local com:</p>
            <ol>
                <li>✅ Todas as tabelas necessárias (usuarios, pessoas, agendamentos, categorias, contas, transacoes)</li>
                <li>✅ Dados iniciais:
                    <ul>
                        <li>Usuário admin padrão</li>
                        <li>Categorias de receitas e despesas</li>
                        <li>Contas financeiras padrão</li>
                    </ul>
                </li>
                <li>✅ Views para relatórios</li>
                <li>✅ Triggers para atualização automática de timestamps</li>
            </ol>

            <p><strong>⚠️ ATENÇÃO:</strong> Este processo irá <strong>apagar o banco de dados existente</strong> e criar um novo!</p>

            <h3>🎯 Vantagens do SQLite:</h3>
            <ul>
                <li>✅ Não precisa de servidor MySQL</li>
                <li>✅ Banco de dados em arquivo único</li>
                <li>✅ Fácil backup (copiar arquivo)</li>
                <li>✅ Performance excelente para aplicações pequenas/médias</li>
                <li>✅ Zero configuração</li>
            </ul>

            <h3>📁 Localização do banco:</h3>
            <pre><?php echo realpath(__DIR__) . '/database/psy_system.db'; ?></pre>

            <form method="post">
                <button type="submit" name="setup_db" onclick="return confirm('⚠️ Tem certeza que deseja criar/recriar o banco de dados?\n\nTodos os dados existentes serão perdidos!')">
                    🚀 Executar Configuração
                </button>
            </form>
        </div>
    <?php
    }
    ?>

    <hr>
    <p><small>Sistema de Gestão Psicológica v0.92 | Banco de Dados: SQLite 3</small></p>
</body>

</html>