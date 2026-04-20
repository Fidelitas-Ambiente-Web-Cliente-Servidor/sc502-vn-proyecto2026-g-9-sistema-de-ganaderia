<?php
require_once __DIR__ . '/../config/db.php';

startSession();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$body   = getBody();

if ($method === 'GET' && $action === 'check') {
    if (!empty($_SESSION['usuario'])) {
        jsonOk($_SESSION['usuario'], 'Sesión activa');
    }
    jsonError('Sin sesión', 401);
}

if ($method === 'POST') {
    $act = $body['action'] ?? '';

    if ($act === 'logout') {
        session_destroy();
        jsonOk(null, 'Sesión cerrada');
    }

    if ($act === 'login') {
        $correo = trim(strtolower($body['correo'] ?? ''));
        $clave  = $body['clave'] ?? '';
        if (!$correo || !$clave) jsonError('Correo y contraseña son requeridos');

        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM hf_usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($clave, $user['clave'])) {
            jsonError('Correo o contraseña incorrectos');
        }

        unset($user['clave']);
        $_SESSION['usuario'] = $user;
        jsonOk($user, 'Login exitoso');
    }

    if ($act === 'register') {
        $nombre = trim($body['nombre'] ?? '');
        $finca  = trim($body['finca']  ?? 'Mi finca');
        $correo = trim(strtolower($body['correo'] ?? ''));
        $clave  = $body['clave'] ?? '';

        if (!$nombre || !$correo || !$clave) jsonError('Todos los campos son requeridos');
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) jsonError('Correo inválido');
        if (strlen($clave) < 6) jsonError('La contraseña debe tener mínimo 6 caracteres');

        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id FROM hf_usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        if ($stmt->fetch()) jsonError('Este correo ya está registrado');

        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $ins  = $pdo->prepare('INSERT INTO hf_usuarios (nombre, finca, correo, clave) VALUES (?,?,?,?)');
        $ins->execute([$nombre, $finca, $correo, $hash]);

        jsonOk(null, 'Cuenta creada exitosamente');
    }

    jsonError('Acción no reconocida');
}

if ($method === 'PUT') {
    $user   = requireAuth();
    $nombre = trim($body['nombre'] ?? '');
    $correo = trim(strtolower($body['correo'] ?? ''));
    $finca  = trim($body['finca']  ?? '');
    $clave  = $body['clave'] ?? '';

    if (!$nombre || !$correo) jsonError('Nombre y correo son requeridos');
    if ($clave && strlen($clave) < 6) jsonError('La contraseña debe tener mínimo 6 caracteres');

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id FROM hf_usuarios WHERE correo = ? AND id != ?');
    $stmt->execute([$correo, $user['id']]);
    if ($stmt->fetch()) jsonError('Ese correo ya está en uso');

    if ($clave) {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE hf_usuarios SET nombre=?, finca=?, correo=?, clave=? WHERE id=?')
            ->execute([$nombre, $finca, $correo, $hash, $user['id']]);
    } else {
        $pdo->prepare('UPDATE hf_usuarios SET nombre=?, finca=?, correo=? WHERE id=?')
            ->execute([$nombre, $finca, $correo, $user['id']]);
    }

    $_SESSION['usuario']['nombre'] = $nombre;
    $_SESSION['usuario']['finca']  = $finca;
    $_SESSION['usuario']['correo'] = $correo;

    jsonOk($_SESSION['usuario'], 'Perfil actualizado');
}

jsonError('Método no permitido', 405);