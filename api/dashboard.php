<?php
// api/dashboard.php — métricas del panel principal
require_once __DIR__ . '/../config/db.php';

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
$user = requireAuth();
$uid  = $user['id'];
$pdo  = getDB();

$mes = date('Y-m');

// Total animales
$stmt = $pdo->prepare('SELECT COUNT(*) FROM hf_animales WHERE id_usuario = ?');
$stmt->execute([$uid]);
$totalAnimales = (int)$stmt->fetchColumn();

// Gastos del mes actual
$stmt = $pdo->prepare(
    "SELECT COALESCE(SUM(monto),0) FROM hf_gastos
     WHERE id_usuario = ? AND DATE_FORMAT(fecha,'%Y-%m') = ?"
);
$stmt->execute([$uid, $mes]);
$gastosMes = (float)$stmt->fetchColumn();

// Registros de salud
$stmt = $pdo->prepare('SELECT COUNT(*) FROM hf_salud_animal WHERE id_usuario = ?');
$stmt->execute([$uid]);
$totalSalud = (int)$stmt->fetchColumn();

// Controles pendientes (próximos 7 días)
$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM hf_salud_animal
     WHERE id_usuario = ? AND proximo_control IS NOT NULL
     AND proximo_control <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
);
$stmt->execute([$uid]);
$pendientes = (int)$stmt->fetchColumn();

// Alertas (próximos 7 días o vencidos)
$stmt = $pdo->prepare(
    "SELECT animal_ref AS animal, tipo,
            DATE_FORMAT(proximo_control,'%Y-%m-%d') AS prox,
            medicamento AS med,
            (proximo_control < CURDATE()) AS vencido
     FROM hf_salud_animal
     WHERE id_usuario = ? AND proximo_control IS NOT NULL
     AND proximo_control <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
     ORDER BY proximo_control ASC LIMIT 5"
);
$stmt->execute([$uid]);
$alertas = $stmt->fetchAll();

// Últimos 5 animales
$stmt = $pdo->prepare(
    'SELECT identificacion AS id, nombre, raza, estado
     FROM hf_animales WHERE id_usuario = ? ORDER BY fecha_registro DESC LIMIT 5'
);
$stmt->execute([$uid]);
$ultimosAnimales = $stmt->fetchAll();

jsonOk([
    'totalAnimales'   => $totalAnimales,
    'gastosMes'       => $gastosMes,
    'totalSalud'      => $totalSalud,
    'pendientes'      => $pendientes,
    'alertas'         => $alertas,
    'ultimosAnimales' => $ultimosAnimales,
]);