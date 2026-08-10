<?php
declare(strict_types=1);
require __DIR__ . '/config/db.php';
require __DIR__ . '/config/mail.php';

$codigo = trim((string)($_GET['codigo'] ?? ''));
$token = trim((string)($_GET['token'] ?? ''));
if ($codigo === '' || $token === '' || !hash_equals(credentialToken($codigo), $token)) {
    http_response_code(403);
    exit('Enlace de acreditación no válido.');
}

$stmt = db()->prepare('SELECT * FROM comunicadores WHERE codigo = ? AND estado = ? LIMIT 1');
$stmt->execute([$codigo, 'Aprobada']);
$registro = $stmt->fetch();
if (!$registro) {
    http_response_code(404);
    exit('La acreditación no está disponible.');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Acreditación | <?= htmlspecialchars($registro['nombre_completo']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="credential-page">
<main class="credential-page-main">
    <section class="credential-dialog">
        <div class="credential" id="credential" data-file="<?= htmlspecialchars('credencial-' . $registro['codigo']) ?>">
            <header class="credential-header"><img src="assets/escudo-diocesis.jpg" alt=""><div><b>Diócesis de Zacatecoluca</b><span>Logística de medios</span></div></header>
            <div class="credential-title"><span>Acreditación oficial</span><h2>Ordenación Episcopal</h2><p>Monseñor Ramiro Landaverde</p></div>
            <img class="credential-photo" src="api/photo.php?codigo=<?= urlencode($registro['codigo']) ?>" alt="Fotografía de <?= htmlspecialchars($registro['nombre_completo']) ?>">
            <div class="credential-person"><h3><?= htmlspecialchars($registro['nombre_completo']) ?></h3><p><?= htmlspecialchars($registro['cargo_funcion']) ?></p><b><?= htmlspecialchars($registro['medio_comunicacion']) ?></b><span><?= htmlspecialchars($registro['tipo_medio']) ?></span></div>
            <div class="credential-code"><?= htmlspecialchars($registro['codigo']) ?></div>
            <footer class="credential-footer"><span>15 DE AGOSTO DE 2026</span><b>PRENSA</b></footer>
        </div>
        <div class="credential-actions"><button class="action-button print-credential" type="button">Imprimir credencial</button><button class="action-button gold download-credential" type="button">Descargar PDF</button></div>
    </section>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
