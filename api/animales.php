<?php
// api/animales.php — CRUD de animales
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$user   = requireAuth();
$uid    = $user['id'];
$method = $_SERVER['REQUEST_METHOD'];
$pdo    = getDB();

// ─── GET: listar todos ───
if ($method === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT id, identificacion, nombre, raza, sexo,
                DATE_FORMAT(fecha_nacimiento,"%Y-%m-%d") AS fecha_nacimiento,
                estado, observaciones
         FROM hf_animales WHERE id_usuario = ? ORDER BY fecha_registro DESC'
    );
    $stmt->execute([$uid]);
    jsonOk($stmt->fetchAll());
}

// ─── POST: crear animal ───
if ($method === 'POST') {
    $b = getBody();
    $id_anim  = trim($b['identificacion'] ?? '');
    $raza     = trim($b['raza'] ?? '');

    if (!$id_anim || !$raza) jsonError('Identificación y raza son requeridos');

    // Verificar ID único para este usuario
    $chk = $pdo->prepare('SELECT id FROM hf_animales WHERE identificacion = ? AND id_usuario = ?');
    $chk->execute([$id_anim, $uid]);
    if ($chk->fetch()) jsonError('Ya existe un animal con esa identificación');

    $stmt = $pdo->prepare(
        'INSERT INTO hf_animales (identificacion, nombre, raza, sexo, fecha_nacimiento, estado, observaciones, id_usuario)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $id_anim,
        trim($b['nombre']          ?? ''),
        $raza,
        $b['sexo']                  ?? 'Hembra',
        $b['fecha_nacimiento']      ?: null,
        $b['estado']                ?? 'Activo',
        trim($b['observaciones']   ?? ''),
        $uid
    ]);

    jsonOk(['id' => $pdo->lastInsertId()], 'Animal registrado');
}

// ─── PUT: actualizar animal ───
if ($method === 'PUT') {
    $b  = getBody();
    $id = (int)($b['id'] ?? 0);
    if (!$id) jsonError('ID requerido');

    // Verificar que pertenece al usuario
    $chk = $pdo->prepare('SELECT id FROM hf_animales WHERE id = ? AND id_usuario = ?');
    $chk->execute([$id, $uid]);
    if (!$chk->fetch()) jsonError('Animal no encontrado', 404);

    $id_anim = trim($b['identificacion'] ?? '');
    $raza    = trim($b['raza'] ?? '');
    if (!$id_anim || !$raza) jsonError('Identificación y raza son requeridos');

    // Verificar que identificación no la use otro animal del mismo usuario
    $dup = $pdo->prepare('SELECT id FROM hf_animales WHERE identificacion = ? AND id_usuario = ? AND id != ?');
    $dup->execute([$id_anim, $uid, $id]);
    if ($dup->fetch()) jsonError('Esa identificación ya la usa otro animal');

    $stmt = $pdo->prepare(
        'UPDATE hf_animales SET identificacion=?, nombre=?, raza=?, sexo=?,
         fecha_nacimiento=?, estado=?, observaciones=? WHERE id=? AND id_usuario=?'
    );
    $stmt->execute([
        $id_anim,
        trim($b['nombre']        ?? ''),
        $raza,
        $b['sexo']                ?? 'Hembra',
        $b['fecha_nacimiento']    ?: null,
        $b['estado']              ?? 'Activo',
        trim($b['observaciones'] ?? ''),
        $id, $uid
    ]);

    jsonOk(null, 'Animal actualizado');
}

// ─── DELETE: eliminar animal ───
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('ID requerido');

    $stmt = $pdo->prepare('DELETE FROM hf_animales WHERE id = ? AND id_usuario = ?');
    $stmt->execute([$id, $uid]);
    if (!$stmt->rowCount()) jsonError('Animal no encontrado', 404);

    jsonOk(null, 'Animal eliminado');
}

jsonError('Método no permitido', 405);
