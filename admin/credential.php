<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../config/db.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$stmt = db()->prepare('SELECT * FROM comunicadores WHERE id = ? LIMIT 1');
$stmt->execute([$id ?: 0]);
$registro = $stmt->fetch();

if (!$registro) {
    http_response_code(404);
    exit('Registro no encontrado.');
}

if ($registro['estado'] !== 'Aprobada') {
    http_response_code(403);
    exit('La credencial solo está disponible para solicitudes aprobadas.');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Credencial | <?= htmlspecialchars($registro['nombre_completo']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="credential-page">
<header class="topbar">
    <a class="brand" href="index.php"><img src="../assets/escudo-diocesis.jpg" alt=""><span><b>Panel de acreditaciones</b><small>Logística de medios</small></span></a>
    <a class="admin-link" href="index.php">Volver al panel</a>
</header>
<main class="credential-page-main">
    <section class="credential-dialog">
        <div class="credential" id="credential" data-file="<?= htmlspecialchars('credencial-' . $registro['codigo']) ?>">
            <header class="credential-header"><img src="../assets/escudo-diocesis.jpg" alt=""><div><b>Diócesis de Zacatecoluca</b><span>Logística de medios</span></div></header>
            <div class="credential-title"><span>Acreditación oficial</span><h2>Ordenación Episcopal</h2><p>Monseñor Ramiro Landaverde</p></div>
            <img class="credential-photo" src="../api/photo.php?codigo=<?= urlencode($registro['codigo']) ?>" alt="Fotografía de <?= htmlspecialchars($registro['nombre_completo']) ?>">
            <div class="credential-person">
                <h3><?= htmlspecialchars($registro['nombre_completo']) ?></h3>
                <p><?= htmlspecialchars($registro['cargo_funcion']) ?></p>
                <b><?= htmlspecialchars($registro['medio_comunicacion']) ?></b>
                <span><?= htmlspecialchars($registro['tipo_medio']) ?></span>
            </div>
            <div class="credential-code"><?= htmlspecialchars($registro['codigo']) ?></div>
            <footer class="credential-footer"><span>15 DE AGOSTO DE 2026</span><b>PRENSA</b></footer>
        </div>
        <div class="credential-actions">
            <button class="action-button print-credential" type="button">Imprimir credencial</button>
            <button class="action-button gold download-credential" type="button">Descargar PDF</button>
        </div>
    </section>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="../assets/app.js"></script>
</body>
</html>
