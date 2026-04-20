<?php
// api/costos.php — CRUD gastos
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
$user   = requireAuth();
$uid    = $user['id'];
$method = $_SERVER['REQUEST_METHOD'];
$pdo    = getDB();

// ─── GET: listar todos ───
if ($method === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT id, categoria AS cat,
                monto, DATE_FORMAT(fecha,"%Y-%m-%d") AS fecha,
                metodo_pago AS metodo, descripcion AS `desc`
         FROM hf_gastos WHERE id_usuario = ? ORDER BY fecha DESC, id DESC'
    );
    $stmt->execute([$uid]);
    jsonOk($stmt->fetchAll());
}

// ─── POST: crear gasto ───
if ($method === 'POST') {
    $b     = getBody();
    $monto = (float)($b['monto'] ?? 0);
    $fecha = trim($b['fecha'] ?? '');

    if (!$fecha || $monto <= 0) jsonError('Monto y fecha son requeridos');

    $stmt = $pdo->prepare(
        'INSERT INTO hf_gastos (categoria, monto, fecha, metodo_pago, descripcion, id_usuario)
         VALUES (?,?,?,?,?,?)'
    );
    $stmt->execute([
        $b['cat']    ?? 'Otro',
        $monto, $fecha,
        $b['metodo'] ?? 'Efectivo',
        trim($b['desc'] ?? ''),
        $uid
    ]);

    jsonOk(['id' => $pdo->lastInsertId()], 'Gasto registrado');
}

// ─── PUT: actualizar gasto ───
if ($method === 'PUT') {
    $b     = getBody();
    $id    = (int)($b['id'] ?? 0);
    $monto = (float)($b['monto'] ?? 0);
    $fecha = trim($b['fecha'] ?? '');

    if (!$id) jsonError('ID requerido');
    if (!$fecha || $monto <= 0) jsonError('Monto y fecha son requeridos');

    $chk = $pdo->prepare('SELECT id FROM hf_gastos WHERE id = ? AND id_usuario = ?');
    $chk->execute([$id, $uid]);
    if (!$chk->fetch()) jsonError('Gasto no encontrado', 404);

    $stmt = $pdo->prepare(
        'UPDATE hf_gastos SET categoria=?, monto=?, fecha=?, metodo_pago=?, descripcion=?
         WHERE id=? AND id_usuario=?'
    );
    $stmt->execute([
        $b['cat']    ?? 'Otro',
        $monto, $fecha,
        $b['metodo'] ?? 'Efectivo',
        trim($b['desc'] ?? ''),
        $id, $uid
    ]);

    jsonOk(null, 'Gasto actualizado');
}

// ─── DELETE: eliminar gasto ───
if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonError('ID requerido');

    $stmt = $pdo->prepare('DELETE FROM hf_gastos WHERE id = ? AND id_usuario = ?');
    $stmt->execute([$id, $uid]);
    if (!$stmt->rowCount()) jsonError('Gasto no encontrado', 404);

    jsonOk(null, 'Gasto eliminado');
}

jsonError('Método no permitido', 405);
