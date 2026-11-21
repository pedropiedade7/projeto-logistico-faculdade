<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    setFlash('danger', 'ID inválido.');
    redirect('../endereco/index.php');
}

$db = getDB();

// Checa existencia
$stmt = $db->prepare('SELECT id, logradouro FROM enderecos WHERE id = ?');
$stmt->execute([$id]);
$end = $stmt->fetch();

if (!$end) {
    setFlash('danger', 'Endereço não encontrado.');
    redirect('../endereco/index.php');
}

// Checa vinculos com fretes de origem ou destino
$stmt = $db->prepare('SELECT COUNT(*) FROM fretes WHERE endereco_origem_id = ? OR endereco_destino_id = ?');
$stmt->execute([$id, $id]);
$vinculos = (int)$stmt->fetchColumn();

if ($vinculos > 0) {
    setFlash('warning', "Este endereço não pode ser excluído pois está vinculado a {$vinculos} frete(s).");
    redirect('../endereco/index.php');
}

$stmt = $db->prepare('DELETE FROM enderecos WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', 'Endereço excluído com sucesso!');
redirect('../endereco/index.php');
