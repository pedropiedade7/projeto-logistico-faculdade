<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    setFlash('danger', 'ID inválido.');
    redirect('../produto/index.php');
}

$db = getDB();

$stmt = $db->prepare('SELECT nome FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$prod = $stmt->fetch();

if (!$prod) {
    setFlash('danger', 'Produto não encontrado.');
    redirect('../produto/index.php');
}

// Verifica se tem frete usando esse produto
$stmt = $db->prepare('SELECT COUNT(*) FROM frete_produtos WHERE produto_id = ?');
$stmt->execute([$id]);
$vinculos = (int)$stmt->fetchColumn();

if ($vinculos > 0) {
    setFlash('warning', "O produto não pode ser excluído pois está vinculado a {$vinculos} frete(s).");
    redirect('../produto/index.php');
}

$stmt = $db->prepare('DELETE FROM produtos WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', 'Produto excluído com sucesso!');
redirect('../produto/index.php');
