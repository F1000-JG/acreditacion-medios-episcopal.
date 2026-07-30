<?php
declare(strict_types=1);
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require __DIR__ . '/../config/db.php';
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
    <div><button type="button" onclick="window.print()">Imprimir / PDF</button><a href="logout.php">Cerrar sesión</a></div>
</header>
<main class="dashboard">
    <section class="dashboard-title">
        <div><span class="eyebrow dark">Directorio oficial</span><h1>Medios registrados</h1><p>Ordenación Episcopal · 15 de agosto de 2026</p></div>
        <div class="stat"><b><?= count($registros) ?></b><span>comunicadores</span></div>
    </section>
    <form class="search-form" method="get">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por nombre, medio o tipo…">
        <button type="submit">Buscar</button>
        <?php if ($q): ?><a href="index.php">Limpiar</a><?php endif; ?>
    </form>
    <div class="table-shell">
        <table>
            <thead><tr><th>Comunicador</th><th>Medio</th><th>Función</th><th>Tipo</th><th>Contacto</th><th>DUI</th></tr></thead>
            <tbody>
            <?php foreach ($registros as $r): ?>
                <tr>
                    <td><div class="person"><img src="../api/photo.php?codigo=<?= urlencode($r['codigo']) ?>" alt=""><span><b><?= htmlspecialchars($r['nombre_completo']) ?></b><small><?= htmlspecialchars($r['codigo']) ?> · <?= (int)$r['edad'] ?> años</small></span></div></td>
                    <td><?= htmlspecialchars($r['medio_comunicacion']) ?></td>
                    <td><?= htmlspecialchars($r['cargo_funcion']) ?></td>
                    <td><span class="media-badge"><?= htmlspecialchars($r['tipo_medio']) ?></span></td>
                    <td><?= htmlspecialchars($r['telefono']) ?><small><?= htmlspecialchars($r['correo']) ?></small></td>
                    <td><?= htmlspecialchars($r['dui']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$registros): ?><tr><td class="empty" colspan="6">No hay registros para mostrar.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
