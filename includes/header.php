<?php
/**
 * Cabeçalho padrão — Navbar + head HTML com Bootstrap 5.
 * Incluir no início de cada página: require_once __DIR__ . '/../includes/header.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Título da página (cada arquivo define $pageTitle antes de incluir o header)
$pageTitle = $pageTitle ?? 'Gerenciador Logístico';

// Define o prefixo de caminho relativo à raiz (usado para assets)
$depth   = $depth ?? '';   // ex: '../' para sub-pastas
$navRoot = $depth ?: './';
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gerenciamento logístico - APO Fundamentos de Programação para Internet">
    <meta name="author" content="Pedro Henrique de Souza Piedade">
    <title><?= e($pageTitle) ?> | LogiSystem</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $navRoot ?>css/style.css">
</head>
<body>

<!-- ======================= NAVBAR ======================= -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container-fluid px-4">

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= $navRoot ?>index.php">
            <span class="brand-icon"><i class="bi bi-truck-front-fill"></i></span>
            <span>Logi<span class="text-accent">System</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMain" aria-controls="navbarMain"
                aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4 gap-1">

                <li class="nav-item">
                    <a class="nav-link nav-pill" href="<?= $navRoot ?>index.php">
                        <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill dropdown-toggle" href="#" id="dropProduto"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-seam-fill me-1"></i>Produtos
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-glass" aria-labelledby="dropProduto">
                        <li><a class="dropdown-item" href="<?= $navRoot ?>produto/index.php">
                            <i class="bi bi-list-ul me-2"></i>Listar Produtos</a></li>
                        <li><a class="dropdown-item" href="<?= $navRoot ?>produto/form.php">
                            <i class="bi bi-plus-circle me-2"></i>Novo Produto</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill dropdown-toggle" href="#" id="dropEndereco"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-geo-alt-fill me-1"></i>Endereços
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-glass" aria-labelledby="dropEndereco">
                        <li><a class="dropdown-item" href="<?= $navRoot ?>endereco/index.php">
                            <i class="bi bi-list-ul me-2"></i>Listar Endereços</a></li>
                        <li><a class="dropdown-item" href="<?= $navRoot ?>endereco/form.php">
                            <i class="bi bi-plus-circle me-2"></i>Novo Endereço</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill dropdown-toggle" href="#" id="dropTransp"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-badge-fill me-1"></i>Transportadores
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-glass" aria-labelledby="dropTransp">
                        <li><a class="dropdown-item" href="<?= $navRoot ?>transportador/index.php">
                            <i class="bi bi-list-ul me-2"></i>Listar Transportadores</a></li>
                        <li><a class="dropdown-item" href="<?= $navRoot ?>transportador/form.php">
                            <i class="bi bi-plus-circle me-2"></i>Novo Transportador</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill dropdown-toggle" href="#" id="dropFrete"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-text-fill me-1"></i>Fretes
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-glass" aria-labelledby="dropFrete">
                        <li><a class="dropdown-item" href="<?= $navRoot ?>frete/index.php">
                            <i class="bi bi-list-ul me-2"></i>Listar Fretes</a></li>
                        <li><a class="dropdown-item" href="<?= $navRoot ?>frete/form.php">
                            <i class="bi bi-plus-circle me-2"></i>Gerar Frete</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= $navRoot ?>frete/relatorio.php">
                            <i class="bi bi-download me-2"></i>Relatório CSV</a></li>
                    </ul>
                </li>

            </ul>

            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-live">
                    <i class="bi bi-circle-fill blink me-1" style="font-size:.5rem"></i>Sistema Online
                </span>
            </div>
        </div>
    </div>
</nav>
<!-- ==================== /NAVBAR ========================= -->

<div class="page-wrapper">
<div class="container-fluid px-4 py-4">

<?php
// Exibe mensagem flash (toast)
$flash = getFlash();
if ($flash): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999; margin-top:70px">
    <div id="flashToast" class="toast align-items-center text-bg-<?= e($flash['type']) ?> border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <?php if ($flash['type'] === 'success'): ?><i class="bi bi-check-circle-fill me-2"></i><?php endif; ?>
                <?php if ($flash['type'] === 'danger'):  ?><i class="bi bi-x-circle-fill me-2"></i><?php endif; ?>
                <?php if ($flash['type'] === 'warning'): ?><i class="bi bi-exclamation-triangle-fill me-2"></i><?php endif; ?>
                <?= e($flash['msg']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>
