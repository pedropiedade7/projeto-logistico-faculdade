<?php
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Fretes';
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();

$fretes = $db->query(
    'SELECT f.id, f.data_frete, f.created_at,
            t.nome AS transportador_nome,
            eo.cidade AS origem_cidade,  eo.uf AS origem_uf,
            ed.cidade AS destino_cidade, ed.uf AS destino_uf,
            COUNT(fp.id) AS total_produtos,
            COALESCE(SUM(fp.quantidade * p.valor_reais), 0) AS valor_total,
            COALESCE(SUM(fp.quantidade * p.peso_kg), 0)     AS peso_total
     FROM fretes f
     JOIN transportadores t ON t.id = f.transportador_id
     JOIN enderecos eo       ON eo.id = f.endereco_origem_id
     JOIN enderecos ed       ON ed.id = f.endereco_destino_id
     LEFT JOIN frete_produtos fp ON fp.frete_id = f.id
     LEFT JOIN produtos p        ON p.id = fp.produto_id
     GROUP BY f.id
     ORDER BY f.id DESC'
)->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-truck-flatbed me-2 text-accent"></i>Fretes</h1>
        <p class="page-subtitle">Listagem de todos os fretes e cargas despachadas.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="relatorio.php" class="btn btn-outline-accent">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Relatório
        </a>
        <a href="form.php" class="btn btn-accent">
            <i class="bi bi-plus-circle me-2"></i>Gerar Frete
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($fretes)): ?>
            <div class="empty-state">
                <i class="bi bi-truck"></i>
                <p class="mb-2">Nenhum frete gerado até o momento.</p>
                <a href="form.php" class="btn btn-accent btn-sm">Gerar Primeiro Frete</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data do Frete</th>
                        <th>Transportador</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Produtos</th>
                        <th>Peso Total</th>
                        <th>Valor Total</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fretes as $f): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= e($f['id']) ?></span></td>
                        <td>
                            <i class="bi bi-calendar3 me-1 text-accent"></i>
                            <?= e(date('d/m/Y', strtotime($f['data_frete']))) ?>
                        </td>
                        <td class="fw-semibold"><?= e($f['transportador_nome']) ?></td>
                        <td><i class="bi bi-geo me-1 text-success"></i><?= e($f['origem_cidade']) ?>/<?= e($f['origem_uf']) ?></td>
                        <td><i class="bi bi-geo-fill me-1 text-danger"></i><?= e($f['destino_cidade']) ?>/<?= e($f['destino_uf']) ?></td>
                        <td>
                            <span class="badge bg-primary">
                                <?= (int)$f['total_produtos'] ?> item(ns)
                            </span>
                        </td>
                        <td><?= number_format((float)$f['peso_total'], 3, ',', '.') ?> kg</td>
                        <td class="fw-semibold text-success">
                            R$ <?= number_format((float)$f['valor_total'], 2, ',', '.') ?>
                        </td>
                        <td class="text-end table-actions">
                            <a href="view.php?id=<?= (int)$f['id'] ?>" class="btn btn-sm btn-outline-accent" title="Visualizar">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                onclick="confirmarExclusao('delete.php?id=<?= (int)$f['id'] ?>', 'Frete #<?= (int)$f['id'] ?>')">
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
