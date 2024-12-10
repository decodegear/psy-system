<?php 
// Inclui o cabeçalho com o menu de navegação
include '../includes/header.php'; 
include '../includes/db_connect.php';


// Definir tipo (despesa ou receita)
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'despesa';


// Buscar categorias com prepared statements
$sql_categorias = "SELECT id, nome FROM categorias WHERE tipo = :tipo";
$stmt_categorias = $conn->prepare($sql_categorias);
$stmt_categorias->execute([':tipo' => ucfirst($tipo)]);

// Buscar contas
$sql_contas = "SELECT id, nome FROM contas";
$stmt_contas = $conn->prepare($sql_contas);
$stmt_contas->execute();

// Gerar token CSRF

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<div class="container mt-5 mb-5">
    <h1 class="mb-4">Cadastro de <?= ucfirst($tipo); ?></h1>

    <form action="../actions/insert_transacao.php?tipo=<?= ucfirst($tipo); ?>" method="post" class="row g-3 needs-validation" novalidate>
    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
    
    <!-- Campo oculto para o tipo -->
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo); ?>">

        <!-- Nome e Valor (Lado a Lado) -->
        <div class="col-md-6">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
            <div class="invalid-feedback">Por favor, insira o nome.</div>
        </div>

        <div class="col-md-6">
            <label for="valor" class="form-label">Valor:</label>
            <input type="number" class="form-control" id="valor" name="valor" step="0.01" required>
            <div class="invalid-feedback">Por favor, insira o valor.</div>
        </div>

        <!-- Data de Vencimento e Situação (Lado a Lado) -->
        <div class="col-md-6">
            <label for="data_vencimento" class="form-label">Data de Vencimento:</label>
            <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" required>
            <div class="invalid-feedback">Por favor, insira a data de vencimento.</div>
        </div>

        <div class="col-md-6">
            <label for="situacao" class="form-label">Situação:</label>
            <select class="form-select" id="situacao" name="situacao" required>
                <option value="">Selecione a situação</option>
                <option value="Pago">Pago</option>
                <option value="A pagar">A pagar</option>
            </select>
            <div class="invalid-feedback">Por favor, selecione a situação.</div>
        </div>

        <!-- Categoria e Conta (Lado a Lado) -->
        <div class="col-md-6">
            <label for="categoria_id" class="form-label">Categoria:</label>
            <select class="form-select" id="categoria_id" name="categoria_id" required>
                <option value="">Selecione a categoria</option>
                <?php while ($row_categoria = $stmt_categorias->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?= $row_categoria['id']; ?>"><?= htmlspecialchars($row_categoria['nome']); ?></option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Por favor, selecione a categoria.</div>
        </div>

        <div class="col-md-6">
            <label for="conta_id" class="form-label">Conta:</label>
            <select class="form-select" id="conta_id" name="conta_id" required>
                <option value="">Selecione a conta</option>
                <?php while ($row_conta = $stmt_contas->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?= $row_conta['id']; ?>"><?= htmlspecialchars($row_conta['nome']); ?></option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">Por favor, selecione a conta.</div>
        </div>

        <!-- Campo de Descrição (Linha Completa) -->
        <div class="col-12">
            <label for="descricao" class="form-label">Descrição:</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4" style="resize: none;" ></textarea>
            <div class="invalid-feedback">Por favor, insira a descrição.</div>
        </div>

        <!-- Parcelamento -->
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="parcelado" name="parcelado" value="1">
                <label class="form-check-label" for="parcelado">Parcelado</label>
            </div>
        </div>

        <div id="parcelas-container" class="col-md-6" style="display:none;">
            <label for="qtd_parcelas" class="form-label">Quantidade de Parcelas:</label>
            <input type="number" class="form-control" id="qtd_parcelas" name="qtd_parcelas" min="1" value="1">
        </div>

        <!-- Data de Inclusão -->
        <input type="hidden" id="data_inclusao" name="data_inclusao" value="<?= date('Y-m-d'); ?>">

        <!-- Botão de Submissão -->
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary">Cadastrar <?= ucfirst($tipo); ?></button>
        </div>
    </form>
</div>

<script>
    // Mostrar/esconder o campo de quantidade de parcelas com base na checkbox "Parcelado"
    document.getElementById('parcelado').addEventListener('change', function() {
        var parcelasContainer = document.getElementById('parcelas-container');
        parcelasContainer.style.display = this.checked ? 'block' : 'none';
    });

   /*  // Validação de formulário com Bootstrap
    (function () {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })(); */
    // Validação de formulário com Bootstrap
(function () {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                // Se o formulário não for válido
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                
                // Redirecionar para uma página de erro
                window.location.href = '/erro.html'; // Substitua pelo caminho da sua página de erro
            } else {
                // Se o formulário for válido
                form.classList.add('was-validated');
                
                // Redirecionar para uma página de sucesso
                window.location.href = "../views/visualizar_transacao.php?tipo=<?= ucfirst($tipo); ?>" // Substitua pelo caminho da sua página de sucesso
            }
        }, false);
    });
})();
</script>

<?php 
// Inclui o rodapé
include '../includes/footer.php'; 
?>
