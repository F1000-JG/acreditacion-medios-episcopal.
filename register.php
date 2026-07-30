<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config/db.php';

function fail(string $message): never
{
    http_response_code(422);
    echo '<!doctype html><html lang="es"><meta charset="utf-8"><title>Error</title>';
    echo '<body style="font-family:Arial;padding:40px"><h1>No se pudo completar el registro</h1>';
    echo '<p>' . htmlspecialchars($message) . '</p><a href="index.php">Volver al formulario</a></body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
    fail('La sesión del formulario venció. Recargá la página e intentá nuevamente.');
}

$nombre = trim((string)($_POST['nombre_completo'] ?? ''));
$edad = filter_var($_POST['edad'] ?? null, FILTER_VALIDATE_INT);
$medio = trim((string)($_POST['medio_comunicacion'] ?? ''));
$cargo = trim((string)($_POST['cargo_funcion'] ?? ''));
$telefono = trim((string)($_POST['telefono'] ?? ''));
$correo = strtolower(trim((string)($_POST['correo'] ?? '')));
$dui = trim((string)($_POST['dui'] ?? ''));
$tipo = trim((string)($_POST['tipo_medio'] ?? ''));
$tipos = ['Televisión', 'Radio', 'Prensa escrita', 'Medio digital', 'Otro'];

if (!$nombre || !$medio || !$cargo || !$telefono || !$correo || !$dui || !$tipo) {
    fail('Todos los campos son obligatorios.');
}
if ($edad === false || $edad < 18 || $edad > 100) {
    fail('La edad debe estar entre 18 y 100 años.');
}
if (!preg_match('/^\d{8}-\d$/', $dui)) {
    fail('El DUI debe tener el formato 00000000-0.');
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !in_array($tipo, $tipos, true)) {
    fail('Revisá el correo electrónico y el tipo de medio.');
}
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    fail('Seleccioná una fotografía válida.');
}
if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
    fail('La fotografía no puede superar los 5 MB.');
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['foto']['tmp_name']);
if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
    fail('La fotografía debe ser JPG, PNG o WEBP.');
}

$foto = file_get_contents($_FILES['foto']['tmp_name']);
$codigo = 'PRENSA-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));

try {
    $stmt = db()->prepare(
        'INSERT INTO comunicadores
        (codigo, nombre_completo, edad, medio_comunicacion, cargo_funcion, telefono, correo, dui, tipo_medio, foto, foto_tipo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$codigo, $nombre, $edad, $medio, $cargo, $telefono, $correo, $dui, $tipo, $foto, $mime]);
} catch (PDOException $e) {
    if ((string)$e->getCode() === '23000') {
        fail('Ya existe un comunicador registrado con este DUI.');
    }
    fail('Ocurrió un problema al guardar los datos. Intentá nuevamente.');
}

$_SESSION['registro_exito'] = ['codigo' => $codigo, 'nombre' => $nombre, 'cargo' => $cargo, 'medio' => $medio];
header('Location: index.php');
exit;
