<?php
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Dashboard';
$depth     = '';
require_once __DIR__ . '/includes/header.php';

$db = getDB();

$totProdutos        = (int) $db->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$totEnderecos       = (int) $db->query('SELECT COUNT(*) FROM enderecos')->fetchColumn();
$totTransportadores = (int) $db->query('SELECT COUNT(*) FROM transportadores')->fetchColumn();
$totFretes          = (int) $db->query('SELECT COUNT(*) FROM fretes')->fetchColumn();

// Fretes recentes
$fretes = $db->query(
    'SELECT f.id, f.data_frete, t.nome AS transportador,
            eo.cidade AS cidade_origem, ed.cidade AS cidade_destino,
            COUNT(fp.id) AS total_produtos
     FROM fretes f
     JOIN transportadores t ON t.id = f.transportador_id
     JOIN enderecos eo       ON eo.id = f.endereco_origem_id
     JOIN enderecos ed       ON ed.id = f.endereco_destino_id
     LEFT JOIN frete_produtos fp ON fp.frete_id = f.id
     GROUP BY f.id
     ORDER BY f.created_at DESC
     LIMIT 5'
)->fetchAll();
?>

<!-- Hero -->
<div class="dashboard-hero">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="mb-1">
                Bem-vindo ao <span class="text-accent">LogiSystem</span> 🚚
            </h1>
            <p class="text-muted mb-3">
                Gerencie produtos, endereços, transportadores e fretes de forma simples e segura.
            </p>
            <a href="frete/form.php" class="btn btn-accent me-2">
                <i class="bi bi-plus-circle me-2"></i>Gerar Novo Frete
            </a>
            <a href="frete/relatorio.php" class="btn btn-outline-accent">
                <i class="bi bi-download me-2"></i>Relatório CSV
            </a>
        </div>
        <div class="col-lg-4 d-none d-lg-flex justify-content-end">
            <i class="bi bi-truck-front-fill" style="font-size:7rem; color: rgba(63,185,224,.15)"></i>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(63,185,224,.12); color:var(--color-accent)">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <div class="stat-num text-accent"><?= $totProdutos ?></div>
                <div class="stat-label">Produtos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(34,197,94,.12); color:var(--color-success)">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <div class="stat-num" style="color:var(--color-success)"><?= $totEnderecos ?></div>
                <div class="stat-label">Endereços</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,.12); color:var(--color-warning)">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div>
                <div class="stat-num" style="color:var(--color-warning)"><?= $totTransportadores ?></div>
                <div class="stat-label">Transportadores</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(37,99,235,.12); color:#60a5fa">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div>
                <div class="stat-num" style="color:#60a5fa"><?= $totFretes ?></div>
                <div class="stat-label">Fretes</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="produto/form.php" class="quick-link">
            <i class="bi bi-plus-square-fill"></i>
            Novo Produto
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="endereco/form.php" class="quick-link">
            <i class="bi bi-pin-map-fill"></i>
            Novo Endereço
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="transportador/form.php" class="quick-link">
            <i class="bi bi-person-plus-fill"></i>
            Novo Transportador
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="frete/form.php" class="quick-link">
            <i class="bi bi-file-earmark-plus-fill"></i>
            Gerar Frete
        </a>
    </div>
</div>

<!-- Fretes Recentes -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2 text-accent"></i>Fretes Recentes</span>
        <a href="frete/index.php" class="btn btn-sm btn-outline-accent">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($fretes)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p class="mb-0">Nenhum frete gerado ainda.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Transportador</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Produtos</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fretes as $f): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= e($f['id']) ?></span></td>
                        <td><?= e(date('d/m/Y', strtotime($f['data_frete']))) ?></td>
                        <td><i class="bi bi-person-badge me-1 text-accent"></i><?= e($f['transportador']) ?></td>
                        <td><i class="bi bi-geo me-1 text-success"></i><?= e($f['cidade_origem']) ?></td>
                        <td><i class="bi bi-geo-fill me-1 text-danger"></i><?= e($f['cidade_destino']) ?></td>
                        <td><span class="badge bg-primary"><?= (int)$f['total_produtos'] ?> produto(s)</span></td>
                        <td class="text-end">
                            <a href="frete/view.php?id=<?= (int)$f['id'] ?>"
                               class="btn btn-sm btn-outline-accent" title="Ver detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
