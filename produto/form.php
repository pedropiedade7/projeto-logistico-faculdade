<?php
require_once __DIR__ . '/../config/db.php';

$db = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prod = null;
$erros = [];

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM produtos WHERE id = ?');
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if (!$prod) {
        setFlash('danger', 'Produto não encontrado.');
        redirect('../produto/index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $peso_kg = str_replace(',', '.', trim($_POST['peso_kg'] ?? ''));
    $volume_m3 = str_replace(',', '.', trim($_POST['volume_m3'] ?? ''));
    $valor_reais = str_replace(',', '.', trim($_POST['valor_reais'] ?? ''));

    if ($nome === '') {
        $erros[] = 'O nome do produto é obrigatório.';
    }
    if (!is_numeric($peso_kg) || $peso_kg < 0) {
        $erros[] = 'Informe um peso válido.';
    }
    if (!is_numeric($volume_m3) || $volume_m3 < 0) {
        $erros[] = 'Informe um volume válido.';
    }
    if (!is_numeric($valor_reais) || $valor_reais < 0) {
        $erros[] = 'Informe um valor válido.';
    }

    if (empty($erros)) {
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE produtos SET nome = ?, descricao = ?, peso_kg = ?, volume_m3 = ?, valor_reais = ? WHERE id = ?');
            $stmt->execute([$nome, $descricao, $peso_kg, $volume_m3, $valor_reais, $id]);
            setFlash('success', 'Produto atualizado com sucesso!');
        } else {
            $stmt = $db->prepare('INSERT INTO produtos (nome, descricao, peso_kg, volume_m3, valor_reais) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nome, $descricao, $peso_kg, $volume_m3, $valor_reais]);
            setFlash('success', 'Produto cadastrado com sucesso!');
        }
        redirect('../produto/index.php');
    }
}

$pageTitle = $id ? 'Editar Produto' : 'Novo Produto';
$depth = '../';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-box-seam-fill me-2 text-accent"></i>
            <?= $id ? 'Editar' : 'Novo' ?> Produto
        </h1>
        <p class="page-subtitle"><?= $id ? 'Altere os dados do produto abaixo.' : 'Preencha os dados para cadastrar um novo produto.' ?></p>
    </div>
    <a href="index.php" class="btn btn-outline-accent">
        <i class="bi bi-arrow-left me-2"></i>Voltar
    </a>
</div>

<?php if (!empty($erros)): ?>
<div class="alert alert-danger mb-4" role="alert">
    <ul class="mb-0 ps-3">
        <?php foreach ($erros as $err): ?>
            <li><?= e($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome" name="nome"
                           value="<?= e($_POST['nome'] ?? $prod['nome'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="descricao" name="descricao"
                           value="<?= e($_POST['descricao'] ?? $prod['descricao'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label for="peso_kg" class="form-label">Peso (kg) <span class="text-danger">*</span></label>
                    <input type="number" step="0.001" min="0" class="form-control" id="peso_kg" name="peso_kg"
                           value="<?= e($_POST['peso_kg'] ?? $prod['peso_kg'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="volume_m3" class="form-label">Volume (m³) <span class="text-danger">*</span></label>
                    <input type="number" step="0.001" min="0" class="form-control" id="volume_m3" name="volume_m3"
                           value="<?= e($_POST['volume_m3'] ?? $prod['volume_m3'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="valor_reais" class="form-label">Valor (R$) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" step="0.01" min="0" class="form-control" id="valor_reais" name="valor_reais"
                               value="<?= e($_POST['valor_reais'] ?? $prod['valor_reais'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-check2-circle me-1"></i>Salvar Produto
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
