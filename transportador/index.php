<?php
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Transportadores';
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();
$transportadores = $db->query('SELECT * FROM transportadores ORDER BY id DESC')->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-person-badge-fill me-2 text-accent"></i>Transportadores</h1>
        <p class="page-subtitle">Motoristas e empresas responsáveis pelos transportes.</p>
    </div>
    <a href="form.php" class="btn btn-accent">
        <i class="bi bi-plus-circle me-2"></i>Novo Transportador
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($transportadores)): ?>
            <div class="empty-state">
                <i class="bi bi-person-badge"></i>
                <p class="mb-2">Nenhum transportador cadastrado.</p>
                <a href="form.php" class="btn btn-accent btn-sm">Cadastrar Primeiro Transportador</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>CPF / CNPJ</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Veículo</th>
                        <th>Placa</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transportadores as $t): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= e($t['id']) ?></span></td>
                        <td class="fw-semibold"><?= e($t['nome']) ?></td>
                        <td><code><?= e($t['cpf_cnpj']) ?></code></td>
                        <td><?= e($t['telefone'] ?: '—') ?></td>
                        <td class="text-muted"><?= e($t['email'] ?: '—') ?></td>
                        <td><?= e($t['veiculo'] ?: '—') ?></td>
                        <td>
                            <?php if ($t['placa']): ?>
                                <span class="badge bg-dark border border-secondary font-monospace"><?= e($t['placa']) ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="text-end table-actions">
                            <a href="form.php?id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-accent" title="Editar">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                onclick="confirmarExclusao('delete.php?id=<?= (int)$t['id'] ?>', '<?= e(addslashes($t['nome'])) ?>')">
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
