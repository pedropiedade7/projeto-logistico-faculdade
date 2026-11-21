<?php
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Endereços';
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();
$enderecos = $db->query('SELECT * FROM enderecos ORDER BY id DESC')->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-geo-alt-fill me-2 text-accent"></i>Endereços</h1>
        <p class="page-subtitle">Endereços de origem e destino para as rotas de frete.</p>
    </div>
    <a href="form.php" class="btn btn-accent">
        <i class="bi bi-plus-circle me-2"></i>Novo Endereço
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($enderecos)): ?>
            <div class="empty-state">
                <i class="bi bi-geo-alt"></i>
                <p class="mb-2">Nenhum endereço cadastrado.</p>
                <a href="form.php" class="btn btn-accent btn-sm">Cadastrar Primeiro Endereço</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo</th>
                        <th>CEP</th>
                        <th>Logradouro</th>
                        <th>Número</th>
                        <th>Complemento</th>
                        <th>Bairro</th>
                        <th>Cidade / UF</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enderecos as $e): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= e($e['id']) ?></span></td>
                        <td>
                            <?php if ($e['tipo'] === 'origem'): ?>
                                <span class="badge bg-success-subtle text-success border border-success">Origem</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger">Destino</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= e($e['cep']) ?></code></td>
                        <td class="fw-semibold"><?= e($e['logradouro']) ?></td>
                        <td><?= e($e['numero']) ?></td>
                        <td class="text-muted"><?= e($e['complemento'] ?: '—') ?></td>
                        <td><?= e($e['bairro']) ?></td>
                        <td><?= e($e['cidade']) ?> / <?= e($e['uf']) ?></td>
                        <td class="text-end table-actions">
                            <a href="form.php?id=<?= (int)$e['id'] ?>" class="btn btn-sm btn-outline-accent" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                onclick="confirmarExclusao('delete.php?id=<?= (int)$e['id'] ?>', '<?= e(addslashes($e['logradouro'])) ?>')">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
