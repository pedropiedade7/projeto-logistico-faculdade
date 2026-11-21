<?php
require_once __DIR__ . '/../config/db.php';

$db = getDB();
$erros = [];

$transportadores = $db->query('SELECT id, nome FROM transportadores ORDER BY nome')->fetchAll();
$origens         = $db->query("SELECT id, cep, logradouro, numero, cidade, uf FROM enderecos WHERE tipo='origem' ORDER BY cidade")->fetchAll();
$destinos        = $db->query("SELECT id, cep, logradouro, numero, cidade, uf FROM enderecos WHERE tipo='destino' ORDER BY cidade")->fetchAll();
$produtos        = $db->query('SELECT id, nome, peso_kg, valor_reais FROM produtos ORDER BY nome')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transportador_id    = (int)($_POST['transportador_id'] ?? 0);
    $endereco_origem_id  = (int)($_POST['endereco_origem_id'] ?? 0);
    $endereco_destino_id = (int)($_POST['endereco_destino_id'] ?? 0);
    $data_frete          = trim($_POST['data_frete'] ?? '');
    $observacoes         = trim($_POST['observacoes'] ?? '');
    $produtosPost        = $_POST['produtos'] ?? [];

    if ($transportador_id <= 0) {
        $erros[] = 'Selecione um transportador.';
    }
    if ($endereco_origem_id <= 0) {
        $erros[] = 'Selecione o endereço de origem.';
    }
    if ($endereco_destino_id <= 0) {
        $erros[] = 'Selecione o endereço de destino.';
    }
    if ($endereco_origem_id > 0 && $endereco_origem_id === $endereco_destino_id) {
        $erros[] = 'Origem e destino não podem ser o mesmo endereço.';
    }
    if ($data_frete === '') {
        $erros[] = 'Informe a data do frete.';
    }

    $produtosValidos = [];
    if (empty($produtosPost)) {
        $erros[] = 'Adicione pelo menos um produto ao frete.';
    } else {
        foreach ($produtosPost as $p) {
            $pid = (int)($p['id'] ?? 0);
            $qty = (int)($p['qtd'] ?? 0);
            if ($pid > 0 && $qty > 0) {
                $produtosValidos[] = ['id' => $pid, 'qtd' => $qty];
            }
        }
        if (empty($produtosValidos)) {
            $erros[] = 'Selecione produtos e quantidades válidas.';
        }
    }

    if (empty($erros)) {
        try {
            $stmt = $db->prepare('INSERT INTO fretes (transportador_id, endereco_origem_id, endereco_destino_id, data_frete, observacoes) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$transportador_id, $endereco_origem_id, $endereco_destino_id, $data_frete, $observacoes]);
            $freteId = (int)$db->lastInsertId();

            $stmtProd = $db->prepare('INSERT INTO frete_produtos (frete_id, produto_id, quantidade) VALUES (?, ?, ?)');
            foreach ($produtosValidos as $pv) {
                $stmtProd->execute([$freteId, $pv['id'], $pv['qtd']]);
            }

            setFlash('success', "Frete #{$freteId} cadastrado com sucesso!");
            redirect('../frete/view.php?id=' . $freteId);
        } catch (Exception $ex) {
            $erros[] = 'Erro ao salvar o frete no banco de dados.';
        }
    }
}

$pageTitle = 'Gerar Frete';
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-truck-flatbed me-2 text-accent"></i>Gerar Frete</h1>
        <p class="page-subtitle">Vincule um transportador a múltiplos produtos para uma rota.</p>
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

<?php if (empty($transportadores) || empty($origens) || empty($destinos) || empty($produtos)): ?>
<div class="alert alert-warning mb-4" role="alert">
    <h6 class="alert-heading"><i class="bi bi-exclamation-circle me-2"></i>Cadastros necessários pendentes</h6>
    <p class="mb-0">Para gerar um frete, você precisa ter ao menos um transportador, um endereço de origem, um endereço de destino e um produto cadastrados.</p>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" id="form-frete" novalidate>
            <div class="card mb-4 bg-dark-subtle border-secondary">
                <div class="card-header"><i class="bi bi-info-circle me-2 text-accent"></i>Dados Principais</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="transportador_id" class="form-label">Transportador <span class="text-danger">*</span></label>
                            <select class="form-select" id="transportador_id" name="transportador_id" required>
                                <option value="">Selecione um transportador...</option>
                                <?php foreach ($transportadores as $t): ?>
                                    <option value="<?= (int)$t['id'] ?>" <?= ((int)($_POST['transportador_id'] ?? 0) === (int)$t['id']) ? 'selected' : '' ?>>
                                        <?= e($t['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="data_frete" class="form-label">Data do Frete <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="data_frete" name="data_frete"
                                   value="<?= e($_POST['data_frete'] ?? date('Y-m-d')) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="endereco_origem_id" class="form-label">Endereço de Origem <span class="text-danger">*</span></label>
                            <select class="form-select" id="endereco_origem_id" name="endereco_origem_id" required>
                                <option value="">Selecione o endereço de origem...</option>
                                <?php foreach ($origens as $o): ?>
                                    <option value="<?= (int)$o['id'] ?>" <?= ((int)($_POST['endereco_origem_id'] ?? 0) === (int)$o['id']) ? 'selected' : '' ?>>
                                        <?= e($o['cidade']) ?>/<?= e($o['uf']) ?> — <?= e($o['logradouro']) ?>, <?= e($o['numero']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="endereco_destino_id" class="form-label">Endereço de Destino <span class="text-danger">*</span></label>
                            <select class="form-select" id="endereco_destino_id" name="endereco_destino_id" required>
                                <option value="">Selecione o endereço de destino...</option>
                                <?php foreach ($destinos as $d): ?>
                                    <option value="<?= (int)$d['id'] ?>" <?= ((int)($_POST['endereco_destino_id'] ?? 0) === (int)$d['id']) ? 'selected' : '' ?>>
                                        <?= e($d['cidade']) ?>/<?= e($d['uf']) ?> — <?= e($d['logradouro']) ?>, <?= e($d['numero']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-secondary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-box-seam me-2 text-accent"></i>Produtos da Carga</span>
                    <button type="button" class="btn btn-sm btn-accent" id="btn-add-produto">
                        <i class="bi bi-plus-circle me-1"></i>Adicionar Produto
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="tabela-produtos-frete">
                            <thead>
                                <tr>
                                    <th style="min-width: 250px;">Produto</th>
                                    <th style="width: 140px;">Quantidade</th>
                                    <th style="width: 140px;">Peso Unit. (kg)</th>
                                    <th style="width: 140px;">Valor Unit. (R$)</th>
                                    <th style="width: 140px;">Subtotal (R$)</th>
                                    <th style="width: 60px;" class="text-end">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="lista-produtos-body">
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <td colspan="4" class="text-end">TOTAIS ESTIMADOS:</td>
                                    <td id="resumo-valor-total" class="text-success">R$ 0,00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"
                              placeholder="Observações sobre a carga, instruções para entrega, etc..."><?= e($_POST['observacoes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-accent btn-lg">
                    <i class="bi bi-check2-circle me-1"></i>Concluir e Gerar Frete
                </button>
            </div>
        </form>
    </div>
</div>

<template id="tmpl-linha-produto">
    <tr class="linha-produto">
        <td>
            <select name="produtos[INDEX][id]" class="form-select select-produto" required>
                <option value="">Selecione o produto...</option>
                <?php foreach ($produtos as $p): ?>
                    <option value="<?= (int)$p['id'] ?>"
                            data-peso="<?= (float)$p['peso_kg'] ?>"
                            data-valor="<?= (float)$p['valor_reais'] ?>">
                        <?= e($p['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="produtos[INDEX][qtd]" class="form-control input-qtd" value="1" min="1" required>
        </td>
        <td class="col-peso align-middle text-muted">—</td>
        <td class="col-valor align-middle text-muted">—</td>
        <td class="col-subtotal align-middle fw-semibold text-success">—</td>
        <td class="text-end align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remover-linha" title="Remover item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
