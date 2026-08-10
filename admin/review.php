<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
if (!hash_equals($_SESSION['admin_csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mail.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$action = (string)($_POST['action'] ?? '');
if ($id === false || !in_array($action, ['approve', 'reject'], true)) {
    http_response_code(422);
    exit('Datos incompletos.');
}

$stmt = db()->prepare('SELECT * FROM comunicadores WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$registro = $stmt->fetch();
if (!$registro || $registro['estado'] !== 'Pendiente') {
    $_SESSION['admin_flash'] = ['type' => 'error', 'message' => 'La solicitud ya fue revisada o no existe.'];
    header('Location: index.php');
    exit;
}

$approved = $action === 'approve';
$newState = $approved ? 'Aprobada' : 'Rechazada';
$update = db()->prepare('UPDATE comunicadores SET estado = ? WHERE id = ? AND estado = ?');
$update->execute([$newState, $id, 'Pendiente']);

$name = htmlspecialchars($registro['nombre_completo'], ENT_QUOTES, 'UTF-8');
if ($approved) {
    $baseUrl = rtrim((string)(getenv('APP_URL') ?: ('https://' . ($_SERVER['HTTP_HOST'] ?? ''))), '/');
    $token = credentialToken($registro['codigo']);
    $downloadUrl = $baseUrl . '/acreditacion.php?codigo=' . rawurlencode($registro['codigo']) . '&token=' . rawurlencode($token);
    $subject = 'Tu acreditación de medios fue aprobada';
    $html = '<div style="font-family:Arial,sans-serif;max-width:620px;margin:auto;color:#173225">'
        . '<h2 style="color:#073d2b">Solicitud aprobada</h2>'
        . '<p>Hola, <strong>' . $name . '</strong>.</p>'
        . '<p>Tu acreditación para la Ordenación Episcopal de Monseñor Ramiro Landaverde fue aprobada.</p>'
        . '<p><a href="' . htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:14px 20px;background:#073d2b;color:#fff;text-decoration:none">Descargar acreditación</a></p>'
        . '<p>Logística de Medios · Diócesis de Zacatecoluca</p></div>';
} else {
    $subject = 'Respuesta a tu solicitud de acreditación de medios';
    $html = '<div style="font-family:Arial,sans-serif;max-width:620px;margin:auto;color:#173225">'
        . '<h2 style="color:#073d2b">Solicitud revisada</h2>'
        . '<p>Hola, <strong>' . $name . '</strong>.</p>'
        . '<p>Luego de revisar la información, tu solicitud de acreditación para la Ordenación Episcopal no fue aprobada.</p>'
        . '<p>Logística de Medios · Diócesis de Zacatecoluca</p></div>';
}

$mail = sendNotificationEmail(
    $registro['correo'],
    $subject,
    $html,
    'media-accreditation-' . $registro['id'] . '-' . strtolower($newState)
);

if ($mail['sent']) {
    $_SESSION['admin_flash'] = [
        'type' => 'success',
        'message' => "Solicitud {$newState} y correo enviado correctamente.",
    ];
} else {
    // Si el proveedor de correo falla, se conserva Pendiente para permitir un nuevo intento.
    $rollback = db()->prepare('UPDATE comunicadores SET estado = ? WHERE id = ? AND estado = ?');
    $rollback->execute(['Pendiente', $id, $newState]);
    $_SESSION['admin_flash'] = [
        'type' => 'error',
        'message' => 'No se cambió el estado porque el correo no pudo enviarse: ' . $mail['error'],
    ];
}

header('Location: index.php');
exit;
