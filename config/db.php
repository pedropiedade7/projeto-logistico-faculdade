<?php
// Conexao com o banco de dados via PDO

$host = 'localhost';
$banco = 'gerenciador_logistico';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

function getDB() {
    global $pdo;
    return $pdo;
}

// Funcao para evitar XSS ao exibir valores
function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

// Redirecionamento simples
function redirect($url) {
    header("Location: $url");
    exit;
}

// Mensagens flash de sessao
function setFlash($tipo, $msg) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['type' => $tipo, 'msg' => $msg];
}

function getFlash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
