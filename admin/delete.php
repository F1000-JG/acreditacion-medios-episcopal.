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

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id !== false && $id > 0) {
    require __DIR__ . '/../config/db.php';
    $stmt = db()->prepare('DELETE FROM comunicadores WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
