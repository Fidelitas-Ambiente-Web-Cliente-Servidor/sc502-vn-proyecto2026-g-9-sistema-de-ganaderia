<?php
// api/usuarios_finca.php — gestión de usuarios adicionales de la finca
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$user   = requireAuth();
$uid    = $user['id'];
$method = $_SERVER['REQUEST_METHOD'];
$pdo    = getDB();

// ─── GET: listar usuarios de la finca ───
if ($method === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT id, nombre, rol, estado FROM hf_usuarios_finca WHERE id_propietario = ?'
    );
    $stmt->execute([$uid]);
    jsonOk($stmt->fetchAll());
}

// ─── POST: agregar usuario ───
if ($method === 'POST') {
    $b      = getBody();
    $nombre = trim($b['nombre'] ?? '');
    $rol    = $b['rol'] ?? 'Consulta';
    if (!$nombre) jsonError('El nombre es requerido');

    $stmt = $pdo->prepare(
        'INSERT INTO hf_usuarios_finca (nombre, rol, id_propietario) VALUES (?,?,?)'
    );
    $stmt->execute([$nombre, $rol, $uid]);
    jsonOk(['id' => $pdo->lastInsertId()], 'Usuario agregado');
}

// ─── DELETE: eliminar usuario ───
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('ID requerido');

    $stmt = $pdo->prepare('DELETE FROM hf_usuarios_finca WHERE id = ? AND id_propietario = ?');
    $stmt->execute([$id, $uid]);
    if (!$stmt->rowCount()) jsonError('Usuario no encontrado', 404);
    jsonOk(null, 'Usuario eliminado');
}

jsonError('Método no permitido', 405);
