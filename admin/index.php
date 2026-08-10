<?php
declare(strict_types=1);
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require __DIR__ . '/../config/db.php';
$_SESSION['admin_csrf'] ??= bin2hex(random_bytes(24));
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);
$publicFormUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . '/registro.php';
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = db()->prepare('SELECT * FROM comunicadores WHERE nombre_completo LIKE ? OR medio_comunicacion LIKE ? OR tipo_medio LIKE ? ORDER BY id DESC');
    $like = "%{$q}%";
    $stmt->execute([$like, $like, $like]);
    $registros = $stmt->fetchAll();
} else {
    $registros = db()->query('SELECT * FROM comunicadores ORDER BY id DESC')->fetchAll();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Panel de Medios | Pastoral de Comunicaciones</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard-page">
<header class="dashboard-header">
    <div class="brand"><img src="../assets/escudo-diocesis.jpg" alt=""><span><b>Panel de acreditaciones</b><small>Pastoral de Comunicaciones</small></span></div>
    <div class="dashboard-actions"><button class="print-dashboard" type="button">Imprimir</button><button class="download-dashboard" type="button">Descargar PDF</button><a href="logout.php">Cerrar sesión</a></div>
</header>
<main class="dashboard dashboard-report">
    <?php if ($flash): ?><div class="admin-flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
    <section class="dashboard-title">
        <div><span class="eyebrow dark">Directorio oficial</span><h1>Medios registrados</h1><p>Ordenación Episcopal · 15 de agosto de 2026</p></div>
        <div class="stat"><b><?= count($registros) ?></b><span>comunicadores</span></div>
    </section>
    <section class="public-link-card">
        <div>
            <span class="eyebrow dark">Enlace público</span>
            <h2>Formulario para comunicadores</h2>
            <p>Compartí este enlace para que cada comunicador pueda completar su propia inscripción.</p>
        </div>
        <div class="public-link-control">
            <input id="publicFormLink" value="<?= htmlspecialchars($publicFormUrl) ?>" readonly>
            <button class="copy-public-link" type="button">Copiar enlace</button>
            <a href="../registro.php" target="_blank" rel="noopener">Abrir</a>
        </div>
    </section>
    <form class="search-form" method="get">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por nombre, medio o tipo…">
        <button type="submit">Buscar</button>
        <?php if ($q): ?><a href="index.php">Limpiar</a><?php endif; ?>
    </form>
    <div class="table-shell">
        <table>
            <thead><tr><th>Comunicador</th><th>Medio</th><th>Función</th><th>Tipo</th><th>Contacto</th><th>DUI</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($registros as $r): ?>
                <tr>
                    <td><div class="person"><img src="../api/photo.php?codigo=<?= urlencode($r['codigo']) ?>" alt=""><span><b><?= htmlspecialchars($r['nombre_completo']) ?></b><small><?= htmlspecialchars($r['codigo']) ?> · <?= (int)$r['edad'] ?> años</small></span></div></td>
                    <td><?= htmlspecialchars($r['medio_comunicacion']) ?></td>
                    <td><?= htmlspecialchars($r['cargo_funcion']) ?></td>
                    <td><span class="media-badge"><?= htmlspecialchars($r['tipo_medio']) ?></span></td>
                    <td><?= htmlspecialchars($r['telefono']) ?><small><?= htmlspecialchars($r['correo']) ?></small></td>
                    <td><?= htmlspecialchars($r['dui']) ?></td>
                    <td><span class="status-badge status-<?= strtolower($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
                    <td>
                        <div class="row-actions">
                            <?php if ($r['estado'] === 'Pendiente'): ?>
                                <form method="post" action="review.php">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['admin_csrf']) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button class="approve-button" type="submit">Aprobar</button>
                                </form>
                                <form method="post" action="review.php">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['admin_csrf']) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button class="reject-button" type="submit">Rechazar</button>
                                </form>
                            <?php elseif ($r['estado'] === 'Aprobada'): ?>
                                <a href="credential.php?id=<?= (int)$r['id'] ?>">Credencial</a>
                            <?php endif; ?>
                            <form method="post" action="delete.php" onsubmit="return confirm('¿Seguro que querés borrar este registro? Esta acción no se puede deshacer.');">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['admin_csrf']) ?>">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit">Borrar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$registros): ?><tr><td class="empty" colspan="8">No hay registros para mostrar.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="../assets/app.js"></script>
</body>
</html>
