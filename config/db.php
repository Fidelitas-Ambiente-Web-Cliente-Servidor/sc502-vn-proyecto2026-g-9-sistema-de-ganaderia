<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'happy_farmer');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        jsonError('Error de conexión: ' . $e->getMessage(), 500);
    }
    return $pdo;
}

function jsonOk($data = null, string $msg = 'OK'): void {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => $msg, 'data' => $data]);
    exit;
}

function jsonError(string $msg, int $code = 400): void {
    ob_clean();
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $msg, 'data' => null]);
    exit;
}

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function requireAuth(): array {
    startSession();
    if (empty($_SESSION['usuario'])) jsonError('No autenticado', 401);
    return $_SESSION['usuario'];
}

function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}