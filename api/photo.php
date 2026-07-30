<?php
declare(strict_types=1);
require __DIR__ . '/../config/db.php';

$codigo = trim((string)($_GET['codigo'] ?? ''));
$stmt = db()->prepare('SELECT foto, foto_tipo FROM comunicadores WHERE codigo = ? LIMIT 1');
$stmt->execute([$codigo]);
$foto = $stmt->fetch();
if (!$foto) {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $foto['foto_tipo']);
header('Cache-Control: private, max-age=3600');
echo $foto['foto'];
