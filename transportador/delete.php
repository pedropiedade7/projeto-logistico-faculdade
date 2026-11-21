<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    setFlash('danger', 'ID inválido.');
    redirect('../transportador/index.php');
}

$db = getDB();

$stmt = $db->prepare('SELECT id, nome FROM transportadores WHERE id = ?');
$stmt->execute([$id]);
$t = $stmt->fetch();

if (!$t) {
    setFlash('danger', 'Transportador não encontrado.');
    redirect('../transportador/index.php');
}

// Verifica se tem frete associado a este transportador
$stmt = $db->prepare('SELECT COUNT(*) FROM fretes WHERE transportador_id = ?');
$stmt->execute([$id]);
$vinculos = (int)$stmt->fetchColumn();

if ($vinculos > 0) {
    setFlash('warning', "Este transportador não pode ser excluído pois está vinculado a {$vinculos} frete(s).");
    redirect('../transportador/index.php');
}

$stmt = $db->prepare('DELETE FROM transportadores WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', 'Transportador excluído com sucesso!');
redirect('../transportador/index.php');
