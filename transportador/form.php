<?php
require_once __DIR__ . '/../config/db.php';

$db = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$transp = null;
$erros = [];

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM transportadores WHERE id = ?');
    $stmt->execute([$id]);
    $transp = $stmt->fetch();
    if (!$transp) {
        setFlash('danger', 'Transportador não encontrado.');
        redirect('../transportador/index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf_cnpj = trim($_POST['cpf_cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $veiculo = trim($_POST['veiculo'] ?? '');
    $placa = strtoupper(trim($_POST['placa'] ?? ''));

    if ($nome === '') {
        $erros[] = 'O nome do transportador é obrigatório.';
    }
    if ($cpf_cnpj === '') {
        $erros[] = 'O CPF ou CNPJ é obrigatório.';
    }

    if (empty($erros)) {
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE transportadores SET nome = ?, cpf_cnpj = ?, telefone = ?, email = ?, veiculo = ?, placa = ? WHERE id = ?');
            $stmt->execute([$nome, $cpf_cnpj, $telefone, $email, $veiculo, $placa, $id]);
            setFlash('success', 'Transportador atualizado com sucesso!');
        } else {
            $stmt = $db->prepare('INSERT INTO transportadores (nome, cpf_cnpj, telefone, email, veiculo, placa) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nome, $cpf_cnpj, $telefone, $email, $veiculo, $placa]);
            setFlash('success', 'Transportador cadastrado com sucesso!');
        }
        redirect('../transportador/index.php');
    }
}

$pageTitle = $id ? 'Editar Transportador' : 'Novo Transportador';
$depth = '../';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-person-badge-fill me-2 text-accent"></i>
            <?= $id ? 'Editar' : 'Novo' ?> Transportador
        </h1>
        <p class="page-subtitle"><?= $id ? 'Altere os dados do transportador abaixo.' : 'Preencha os dados do motorista ou empresa de transporte.' ?></p>
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
                    <label for="nome" class="form-label">Nome Completo / Razão Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nome" name="nome"
                           value="<?= e($_POST['nome'] ?? $transp['nome'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="cpf_cnpj" class="form-label">CPF ou CNPJ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj"
                           placeholder="000.000.000-00 ou 00.000.000/0000-00"
                           value="<?= e($_POST['cpf_cnpj'] ?? $transp['cpf_cnpj'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="telefone" class="form-label">Telefone / Celular</label>
                    <input type="text" class="form-control" id="telefone" name="telefone"
                           placeholder="(00) 00000-0000"
                           value="<?= e($_POST['telefone'] ?? $transp['telefone'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= e($_POST['email'] ?? $transp['email'] ?? '') ?>">
                </div>

                <div class="col-md-2">
                    <label for="veiculo" class="form-label">Modelo do Veículo</label>
                    <input type="text" class="form-control" id="veiculo" name="veiculo"
                           placeholder="Ex: Scania R450, Fiorino"
                           value="<?= e($_POST['veiculo'] ?? $transp['veiculo'] ?? '') ?>">
                </div>

                <div class="col-md-2">
                    <label for="placa" class="form-label">Placa</label>
                    <input type="text" class="form-control" id="placa" name="placa" maxlength="8"
                           placeholder="ABC-1234"
                           value="<?= e($_POST['placa'] ?? $transp['placa'] ?? '') ?>">
                </div>

                <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-check2-circle me-1"></i>Salvar Transportador
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
