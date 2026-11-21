<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    setFlash('danger', 'ID inválido.');
    redirect('../frete/index.php');
}

$db = getDB();

$stmt = $db->prepare(
    'SELECT f.*,
            t.nome  AS t_nome,  t.cpf_cnpj AS t_doc,  t.telefone AS t_tel,
            t.veiculo AS t_veiculo, t.placa AS t_placa,
            eo.cep AS o_cep, eo.logradouro AS o_logradouro, eo.numero AS o_numero,
            eo.complemento AS o_comp, eo.bairro AS o_bairro,
            eo.cidade AS o_cidade, eo.uf AS o_uf,
            ed.cep AS d_cep, ed.logradouro AS d_logradouro, ed.numero AS d_numero,
            ed.complemento AS d_comp, ed.bairro AS d_bairro,
            ed.cidade AS d_cidade, ed.uf AS d_uf
     FROM fretes f
     JOIN transportadores t ON t.id = f.transportador_id
     JOIN enderecos eo       ON eo.id = f.endereco_origem_id
     JOIN enderecos ed       ON ed.id = f.endereco_destino_id
     WHERE f.id = ?'
);
$stmt->execute([$id]);
$frete = $stmt->fetch();

if (!$frete) {
    setFlash('danger', 'Frete não encontrado.');
    redirect('../frete/index.php');
}

$stmtProd = $db->prepare(
    'SELECT p.nome, p.peso_kg, p.volume_m3, p.valor_reais, fp.quantidade,
            (p.valor_reais * fp.quantidade) AS subtotal
     FROM frete_produtos fp
     JOIN produtos p ON p.id = fp.produto_id
     WHERE fp.frete_id = ?'
);
$stmtProd->execute([$id]);
$itens = $stmtProd->fetchAll();

$totalValor = 0;
$totalPeso  = 0;
foreach ($itens as $item) {
    $totalValor += $item['subtotal'];
    $totalPeso  += ($item['peso_kg'] * $item['quantidade']);
}

$pageTitle = 'Frete #' . $id;
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-file-earmark-text-fill me-2 text-accent"></i>
            Frete <span class="text-accent">#<?= e($id) ?></span>
        </h1>
        <p class="page-subtitle">Gerado em <?= e(date('d/m/Y H:i', strtotime($frete['created_at']))) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-accent">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer-fill me-2"></i>Imprimir
        </button>
        <button type="button" class="btn btn-outline-danger"
            onclick="confirmarExclusao('delete.php?id=<?= (int)$frete['id'] ?>', 'Frete #<?= (int)$frete['id'] ?>')">
            <i class="bi bi-trash3-fill me-2"></i>Excluir
        </button>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-person-badge-fill me-2 text-accent"></i>Transportador
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="form-label">Nome</span>
                    <div class="fw-semibold"><?= e($frete['t_nome']) ?></div>
                </div>
                <div class="mb-2">
                    <span class="form-label">CPF/CNPJ</span>
                    <div><code><?= e($frete['t_doc']) ?></code></div>
                </div>
                <?php if ($frete['t_tel']): ?>
                <div class="mb-2">
                    <span class="form-label">Telefone</span>
                    <div><?= e($frete['t_tel']) ?></div>
                </div>
                <?php endif; ?>
                <?php if ($frete['t_veiculo']): ?>
                <div class="mb-2">
                    <span class="form-label">Veículo</span>
                    <div><?= e($frete['t_veiculo']) ?>
                        <?= $frete['t_placa'] ? ' — <span class="badge bg-dark border border-secondary font-monospace">' . e($frete['t_placa']) . '</span>' : '' ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="mt-3">
                    <span class="form-label">Data do Frete</span>
                    <div class="fw-bold text-accent">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?= e(date('d/m/Y', strtotime($frete['data_frete']))) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-geo me-2 text-success"></i>Origem
            </div>
            <div class="card-body">
                <address class="mb-0">
                    <strong><?= e($frete['o_logradouro']) ?>, <?= e($frete['o_numero']) ?></strong><br>
                    <?= $frete['o_comp'] ? e($frete['o_comp']) . '<br>' : '' ?>
                    <?= e($frete['o_bairro']) ?><br>
                    <?= e($frete['o_cidade']) ?> / <strong><?= e($frete['o_uf']) ?></strong><br>
                    CEP: <code><?= e($frete['o_cep']) ?></code>
                </address>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-geo-fill me-2 text-danger"></i>Destino
            </div>
            <div class="card-body">
                <address class="mb-0">
                    <strong><?= e($frete['d_logradouro']) ?>, <?= e($frete['d_numero']) ?></strong><br>
                    <?= $frete['d_comp'] ? e($frete['d_comp']) . '<br>' : '' ?>
                    <?= e($frete['d_bairro']) ?><br>
                    <?= e($frete['d_cidade']) ?> / <strong><?= e($frete['d_uf']) ?></strong><br>
                    CEP: <code><?= e($frete['d_cep']) ?></code>
                </address>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <i class="bi bi-box-seam-fill me-2 text-accent"></i>Produtos do Frete
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th class="text-end">Peso Unit. (kg)</th>
                        <th class="text-end">Qtd</th>
                        <th class="text-end">Peso Total (kg)</th>
                        <th class="text-end">Valor Unit. (R$)</th>
                        <th class="text-end">Subtotal (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($item['nome']) ?></td>
                        <td class="text-end"><?= number_format((float)$item['peso_kg'], 3, ',', '.') ?></td>
                        <td class="text-end"><span class="badge bg-primary"><?= (int)$item['quantidade'] ?></span></td>
                        <td class="text-end"><?= number_format($item['peso_kg'] * $item['quantidade'], 3, ',', '.') ?></td>
                        <td class="text-end">R$ <?= number_format((float)$item['valor_reais'], 2, ',', '.') ?></td>
                        <td class="text-end fw-semibold text-success">R$ <?= number_format((float)$item['subtotal'], 2, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-active fw-bold">
                        <td colspan="3">TOTAL</td>
                        <td class="text-end"><?= number_format($totalPeso, 3, ',', '.') ?> kg</td>
                        <td></td>
                        <td class="text-end text-success">R$ <?= number_format($totalValor, 2, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php if ($frete['observacoes']): ?>
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-chat-text-fill me-2 text-accent"></i>Observações</div>
    <div class="card-body">
        <p class="mb-0 text-muted"><?= nl2br(e($frete['observacoes'])) ?></p>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
