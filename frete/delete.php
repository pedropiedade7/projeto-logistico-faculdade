<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    setFlash('danger', 'ID inválido.');
    redirect('../frete/index.php');
}

$db = getDB();

$stmt = $db->prepare('SELECT id FROM fretes WHERE id = ?');
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    setFlash('danger', 'Frete não encontrado.');
    redirect('../frete/index.php');
}

$stmt = $db->prepare('DELETE FROM fretes WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', "Frete #{$id} excluído com sucesso!");
redirect('../frete/index.php');
