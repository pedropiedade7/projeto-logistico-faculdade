<?php
require_once __DIR__ . '/../config/db.php';

$db = getDB();

$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim    = $_GET['data_fim']    ?? date('Y-m-t');

if (isset($_GET['download'])) {
    $stmt = $db->prepare(
        'SELECT f.id, f.data_frete, f.observacoes,
                t.nome AS transportador, t.cpf_cnpj, t.veiculo, t.placa,
                eo.cidade AS origem_cidade, eo.uf AS origem_uf,
                ed.cidade AS destino_cidade, ed.uf AS destino_uf,
                p.nome AS produto, fp.quantidade,
                p.peso_kg, p.valor_reais,
                (p.valor_reais * fp.quantidade) AS subtotal
         FROM fretes f
         JOIN transportadores t ON t.id = f.transportador_id
         JOIN enderecos eo       ON eo.id = f.endereco_origem_id
         JOIN enderecos ed       ON ed.id = f.endereco_destino_id
         JOIN frete_produtos fp  ON fp.frete_id = f.id
         JOIN produtos p         ON p.id = fp.produto_id
         WHERE f.data_frete BETWEEN ? AND ?
         ORDER BY f.data_frete DESC'
    );
    $stmt->execute([$dataInicio, $dataFim]);
    $rows = $stmt->fetchAll();

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="relatorio_fretes.csv"');

    // BOM para acentos abrirem corretamente no Excel
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');

    fputcsv($out, [
        'ID Frete', 'Data', 'Transportador', 'CPF/CNPJ', 'Veiculo', 'Placa',
        'Origem', 'Destino', 'Produto', 'Quantidade', 'Valor Unitario', 'Subtotal', 'Observacoes'
    ], ';');

    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'],
            date('d/m/Y', strtotime($r['data_frete'])),
            $r['transportador'],
            $r['cpf_cnpj'],
            $r['veiculo'],
            $r['placa'],
            $r['origem_cidade'] . '/' . $r['origem_uf'],
            $r['destino_cidade'] . '/' . $r['destino_uf'],
            $r['produto'],
            $r['quantidade'],
            number_format((float)$r['valor_reais'], 2, ',', ''),
            number_format((float)$r['subtotal'], 2, ',', ''),
            $r['observacoes']
        ], ';');
    }

    fclose($out);
    exit;
}

$pageTitle = 'Relatório de Fretes';
$depth     = '../';
require_once __DIR__ . '/../includes/header.php';

$stmt = $db->prepare(
    'SELECT f.id, f.data_frete, t.nome AS transportador,
            eo.cidade AS origem, ed.cidade AS destino,
            COUNT(fp.id) AS total_prod
     FROM fretes f
     JOIN transportadores t ON t.id = f.transportador_id
     JOIN enderecos eo       ON eo.id = f.endereco_origem_id
     JOIN enderecos ed       ON ed.id = f.endereco_destino_id
     LEFT JOIN frete_produtos fp ON fp.frete_id = f.id
     WHERE f.data_frete BETWEEN ? AND ?
     GROUP BY f.id
     ORDER BY f.data_frete DESC'
);
$stmt->execute([$dataInicio, $dataFim]);
$preview = $stmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-file-earmark-spreadsheet-fill me-2 text-accent"></i>Relatório de Fretes</h1>
        <p class="page-subtitle">Exporte os fretes de um período em arquivo CSV.</p>
    </div>
    <a href="index.php" class="btn btn-outline-accent">
        <i class="bi bi-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-funnel-fill me-2 text-accent"></i>Filtrar Período</div>
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="data_inicio" class="form-label">Data Início</label>
                <input type="date" id="data_inicio" name="data_inicio" class="form-control"
                       value="<?= e($dataInicio) ?>">
            </div>
            <div class="col-md-4">
                <label for="data_fim" class="form-label">Data Fim</label>
                <input type="date" id="data_fim" name="data_fim" class="form-control"
                       value="<?= e($dataFim) ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-accent flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a href="relatorio.php?data_inicio=<?= urlencode($dataInicio) ?>&data_fim=<?= urlencode($dataFim) ?>&download=1"
                   class="btn btn-accent flex-fill">
                    <i class="bi bi-download me-1"></i>Baixar CSV
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-table me-2 text-accent"></i>
            Prévia dos Fretes
        </span>
        <span class="badge bg-primary"><?= count($preview) ?> frete(s)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($preview)): ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p class="mb-0">Nenhum frete encontrado no período selecionado.</p>
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
                    <?php foreach ($preview as $f): ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= e($f['id']) ?></span></td>
                        <td><?= e(date('d/m/Y', strtotime($f['data_frete']))) ?></td>
                        <td><?= e($f['transportador']) ?></td>
                        <td><i class="bi bi-geo me-1 text-success"></i><?= e($f['origem']) ?></td>
                        <td><i class="bi bi-geo-fill me-1 text-danger"></i><?= e($f['destino']) ?></td>
                        <td><span class="badge bg-primary"><?= (int)$f['total_prod'] ?> produto(s)</span></td>
                        <td class="text-end">
                            <a href="view.php?id=<?= (int)$f['id'] ?>" class="btn btn-sm btn-outline-accent">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
