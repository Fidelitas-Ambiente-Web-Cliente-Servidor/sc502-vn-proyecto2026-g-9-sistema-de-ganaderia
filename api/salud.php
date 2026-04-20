<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$user   = requireAuth();
$uid    = $user['id'];
$method = $_SERVER['REQUEST_METHOD'];
$pdo    = getDB();

if ($method === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT id, animal_ref AS animal, tipo,
                DATE_FORMAT(fecha,"%Y-%m-%d") AS fecha,
                DATE_FORMAT(proximo_control,"%Y-%m-%d") AS prox,
                veterinario AS vet, medicamento AS med, detalle
         FROM hf_salud_animal WHERE id_usuario = ? ORDER BY fecha DESC'
    );
    $stmt->execute([$uid]);
    jsonOk($stmt->fetchAll());
}

if ($method === 'POST') {
    $b      = getBody();
    $animal = trim($b['animal'] ?? '');
    $tipo   = trim($b['tipo']   ?? '');
    $fecha  = trim($b['fecha']  ?? '');

    if (!$animal || !$tipo || !$fecha) jsonError('Animal, tipo y fecha son requeridos');

    $stmt = $pdo->prepare(
        'INSERT INTO hf_salud_animal (animal_ref, tipo, fecha, proximo_control, veterinario, medicamento, detalle, id_usuario)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $animal, $tipo, $fecha,
        $b['prox']     ?: null,
        trim($b['vet']     ?? ''),
        trim($b['med']     ?? ''),
        trim($b['detalle'] ?? ''),
        $uid
    ]);
    jsonOk(['id' => $pdo->lastInsertId()], 'Registro sanitario guardado');
}

if ($method === 'PUT') {
    $b  = getBody();
    $id = (int)($b['id'] ?? 0);
    if (!$id) jsonError('ID requerido');

    $chk = $pdo->prepare('SELECT id FROM hf_salud_animal WHERE id = ? AND id_usuario = ?');
    $chk->execute([$id, $uid]);
    if (!$chk->fetch()) jsonError('Registro no encontrado', 404);

    $animal = trim($b['animal'] ?? '');
    $fecha  = trim($b['fecha']  ?? '');
    if (!$animal || !$fecha) jsonError('Animal y fecha son requeridos');

    $stmt = $pdo->prepare(
        'UPDATE hf_salud_animal SET animal_ref=?, tipo=?, fecha=?, proximo_control=?,
         veterinario=?, medicamento=?, detalle=? WHERE id=? AND id_usuario=?'
    );
    $stmt->execute([
        $animal,
        trim($b['tipo']    ?? ''),
        $fecha,
        $b['prox']     ?: null,
        trim($b['vet']     ?? ''),
        trim($b['med']     ?? ''),
        trim($b['detalle'] ?? ''),
        $id, $uid
    ]);
    jsonOk(null, 'Registro actualizado');
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('ID requerido');

    $stmt = $pdo->prepare('DELETE FROM hf_salud_animal WHERE id = ? AND id_usuario = ?');
    $stmt->execute([$id, $uid]);
    if (!$stmt->rowCount()) jsonError('Registro no encontrado', 404);
    jsonOk(null, 'Registro eliminado');
}

jsonError('Método no permitido', 405);