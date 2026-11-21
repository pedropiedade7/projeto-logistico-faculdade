<?php
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Produtos';
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();
$produtos = $db->query('SELECT * FROM produtos ORDER BY id DESC')->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-box-seam-fill me-2 text-accent"></i>Produtos</h1>
        <p class="page-subtitle">Gerencie o catálogo de produtos do sistema.</p>
    </div>
    <a href="form.php" class="btn btn-accent">
        <i class="bi bi-plus-circle me-2"></i>Novo Produto
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                <p class="mb-2">Nenhum produto cadastrado.</p>
                <a href="form.php" class="btn btn-accent btn-sm">Cadastrar Primeiro Produto</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Peso (kg)</th>
                        <th>Volume (m³)</th>
                        <th>Valor (R$)</th>
                        <th>Cadastrado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= e($p['id']) ?></span></td>
                        <td class="fw-semibold"><?= e($p['nome']) ?></td>
                        <td class="text-muted"><?= e($p['descricao'] ?: '—') ?></td>
                        <td><?= number_format((float)$p['peso_kg'], 3, ',', '.') ?> kg</td>
                        <td><?= number_format((float)$p['volume_m3'], 3, ',', '.') ?> m³</td>
                        <td class="fw-semibold text-success">R$ <?= number_format((float)$p['valor_reais'], 2, ',', '.') ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($p['created_at']))) ?></td>
                        <td class="text-end table-actions">
                            <a href="form.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-accent" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                onclick="confirmarExclusao('delete.php?id=<?= (int)$p['id'] ?>', '<?= e(addslashes($p['nome'])) ?>')">
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
