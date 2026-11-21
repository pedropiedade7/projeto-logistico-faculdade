<?php
require_once __DIR__ . '/../config/db.php';

$db = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$end = null;
$erros = [];

$ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS',
        'MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC',
        'SP','SE','TO'];

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM enderecos WHERE id = ?');
    $stmt->execute([$id]);
    $end = $stmt->fetch();
    if (!$end) {
        setFlash('danger', 'Endereço não encontrado.');
        redirect('../endereco/index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $cep = trim($_POST['cep'] ?? '');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));

    if (!in_array($tipo, ['origem', 'destino'])) {
        $erros[] = 'Selecione se o tipo é origem ou destino.';
    }
    if ($cep === '') {
        $erros[] = 'Informe o CEP.';
    }
    if ($logradouro === '') {
        $erros[] = 'Informe o logradouro.';
    }
    if ($numero === '') {
        $erros[] = 'Informe o número.';
    }
    if ($bairro === '') {
        $erros[] = 'Informe o bairro.';
    }
    if ($cidade === '') {
        $erros[] = 'Informe a cidade.';
    }
    if (!in_array($uf, $ufs)) {
        $erros[] = 'Selecione uma UF válida.';
    }

    if (empty($erros)) {
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE enderecos SET tipo = ?, cep = ?, logradouro = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, uf = ? WHERE id = ?');
            $stmt->execute([$tipo, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf, $id]);
            setFlash('success', 'Endereço atualizado com sucesso!');
        } else {
            $stmt = $db->prepare('INSERT INTO enderecos (tipo, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$tipo, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf]);
            setFlash('success', 'Endereço cadastrado com sucesso!');
        }
        redirect('../endereco/index.php');
    }
}

$pageTitle = $id ? 'Editar Endereço' : 'Novo Endereço';
$depth = '../';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-geo-alt-fill me-2 text-accent"></i>
            <?= $id ? 'Editar' : 'Novo' ?> Endereço
        </h1>
        <p class="page-subtitle"><?= $id ? 'Altere os dados do endereço abaixo.' : 'Preencha o CEP para preenchimento automático via ViaCEP.' ?></p>
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
                <div class="col-md-3">
                    <label for="tipo" class="form-label">Tipo de Endereço <span class="text-danger">*</span></label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <?php $valTipo = $_POST['tipo'] ?? $end['tipo'] ?? ''; ?>
                        <option value="">Selecione...</option>
                        <option value="origem"  <?= $valTipo === 'origem'  ? 'selected' : '' ?>>Origem</option>
                        <option value="destino" <?= $valTipo === 'destino' ? 'selected' : '' ?>>Destino</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="cep" class="form-label">
                        CEP <span class="text-danger">*</span>
                        <span id="cep-status" class="ms-1 small"></span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="cep" name="cep" maxlength="9"
                               placeholder="00000-000"
                               value="<?= e($_POST['cep'] ?? $end['cep'] ?? '') ?>" required>
                        <button type="button" class="btn btn-outline-secondary" id="btn-buscar-cep" title="Buscar CEP">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="logradouro" class="form-label">Logradouro (Rua, Av.) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="logradouro" name="logradouro"
                           value="<?= e($_POST['logradouro'] ?? $end['logradouro'] ?? '') ?>" required>
                </div>

                <div class="col-md-2">
                    <label for="numero" class="form-label">Número <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="numero" name="numero"
                           value="<?= e($_POST['numero'] ?? $end['numero'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" class="form-control" id="complemento" name="complemento"
                           placeholder="Apto, Galpão, Bloco..."
                           value="<?= e($_POST['complemento'] ?? $end['complemento'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label for="bairro" class="form-label">Bairro <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="bairro" name="bairro"
                           value="<?= e($_POST['bairro'] ?? $end['bairro'] ?? '') ?>" required>
                </div>

                <div class="col-md-2">
                    <label for="cidade" class="form-label">Cidade <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cidade" name="cidade"
                           value="<?= e($_POST['cidade'] ?? $end['cidade'] ?? '') ?>" required>
                </div>

                <div class="col-md-1">
                    <label for="uf" class="form-label">UF <span class="text-danger">*</span></label>
                    <select class="form-select" id="uf" name="uf" required>
                        <?php $valUf = $_POST['uf'] ?? $end['uf'] ?? ''; ?>
                        <option value="">UF</option>
                        <?php foreach ($ufs as $u): ?>
                            <option value="<?= $u ?>" <?= $valUf === $u ? 'selected' : '' ?>><?= $u ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-check2-circle me-1"></i>Salvar Endereço
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
