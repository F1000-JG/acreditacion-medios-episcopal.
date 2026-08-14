<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../config/db.php';
$_SESSION['admin_csrf'] ??= bin2hex(random_bytes(24));

$id = filter_var($_POST['id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    http_response_code(422);
    exit('Registro no válido.');
}

$stmt = db()->prepare('SELECT * FROM comunicadores WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$registro = $stmt->fetch();
if (!$registro) {
    http_response_code(404);
    exit('Registro no encontrado.');
}

$tipos = ['Televisión', 'Radio', 'Prensa escrita', 'Medio digital', 'Otro'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['admin_csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    $nombre = trim((string)($_POST['nombre_completo'] ?? ''));
    $edad = filter_var($_POST['edad'] ?? null, FILTER_VALIDATE_INT);
    $medio = trim((string)($_POST['medio_comunicacion'] ?? ''));
    $cargo = trim((string)($_POST['cargo_funcion'] ?? ''));
    $telefono = trim((string)($_POST['telefono'] ?? ''));
    $correo = strtolower(trim((string)($_POST['correo'] ?? '')));
    $dui = trim((string)($_POST['dui'] ?? ''));
    $tipo = trim((string)($_POST['tipo_medio'] ?? ''));

    $registro = array_merge($registro, [
        'nombre_completo' => $nombre,
        'edad' => $edad,
        'medio_comunicacion' => $medio,
        'cargo_funcion' => $cargo,
        'telefono' => $telefono,
        'correo' => $correo,
        'dui' => $dui,
        'tipo_medio' => $tipo,
    ]);

    if ($nombre === '' || $medio === '' || $cargo === '' || $telefono === '' || $correo === '' || $dui === '' || $tipo === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($edad === false || $edad < 18 || $edad > 100) {
        $error = 'La edad debe estar entre 18 y 100 años.';
    } elseif (!preg_match('/^\d{8}-\d$/', $dui)) {
        $error = 'El DUI debe tener el formato 00000000-0.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !in_array($tipo, $tipos, true)) {
        $error = 'Revisá el correo electrónico y el tipo de medio.';
    }

    $foto = null;
    $fotoTipo = null;
    if ($error === '' && isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = 'No se pudo cargar la nueva fotografía.';
        } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            $error = 'La fotografía no puede superar los 5 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $fotoTipo = $finfo->file($_FILES['foto']['tmp_name']);
            if (!in_array($fotoTipo, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                $error = 'La fotografía debe ser JPG, PNG o WEBP.';
            } else {
                $foto = file_get_contents($_FILES['foto']['tmp_name']);
            }
        }
    }

    if ($error === '') {
        try {
            if ($foto !== null && $fotoTipo !== null) {
                $update = db()->prepare(
                    'UPDATE comunicadores SET nombre_completo = ?, edad = ?, medio_comunicacion = ?,
                     cargo_funcion = ?, telefono = ?, correo = ?, dui = ?, tipo_medio = ?,
                     foto = ?, foto_tipo = ? WHERE id = ?'
                );
                $update->execute([$nombre, $edad, $medio, $cargo, $telefono, $correo, $dui, $tipo, $foto, $fotoTipo, $id]);
            } else {
                $update = db()->prepare(
                    'UPDATE comunicadores SET nombre_completo = ?, edad = ?, medio_comunicacion = ?,
                     cargo_funcion = ?, telefono = ?, correo = ?, dui = ?, tipo_medio = ? WHERE id = ?'
                );
                $update->execute([$nombre, $edad, $medio, $cargo, $telefono, $correo, $dui, $tipo, $id]);
            }

            $_SESSION['admin_flash'] = ['type' => 'success', 'message' => 'Los datos del comunicador fueron actualizados correctamente.'];
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = (string)$e->getCode() === '23000'
                ? 'Ese DUI ya pertenece a otro registro.'
                : 'No fue posible guardar los cambios.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#073d2b">
    <title>Editar comunicador | Panel de Medios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="registration-page">
<header class="topbar">
    <a class="brand" href="index.php"><img src="../assets/escudo-diocesis.jpg" alt="Escudo diocesano"><span><b>Panel de acreditaciones</b><small>Logística de medios</small></span></a>
    <a class="admin-link" href="index.php">Volver al panel</a>
</header>
<main>
    <section class="form-banner">
        <span class="eyebrow">Administración</span>
        <h1>Editar comunicador</h1>
        <p><?= htmlspecialchars($registro['codigo']) ?> · <?= htmlspecialchars($registro['estado']) ?></p>
        <b>DIÓCESIS DE ZACATECOLUCA</b>
    </section>
    <section class="form-section edit-form-section">
        <aside class="form-intro">
            <span class="section-number">✎</span><span class="eyebrow dark">Corrección de datos</span>
            <h2>Información registrada</h2>
            <p>Los cambios se reflejarán en el panel y en la credencial. El código y el estado de la solicitud se conservarán.</p>
            <div class="privacy-note"><span>✦</span><p>Dejá la fotografía sin seleccionar si querés conservar la actual.</p></div>
        </aside>
        <form class="registration-card" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['admin_csrf']) ?>">
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <?php if ($error !== ''): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <div class="form-card-header"><span>Edición administrativa</span><h2>Datos personales y profesionales</h2><p>Revisá cuidadosamente antes de guardar.</p></div>
            <div class="photo-upload">
                <label class="photo-preview" for="foto"><img id="fotoPreview" src="../api/photo.php?codigo=<?= urlencode($registro['codigo']) ?>&amp;v=<?= time() ?>" alt="Fotografía actual"><span id="fotoIcon" hidden>+</span></label>
                <div><b>Fotografía del comunicador</b><p>Seleccioná otra imagen solamente si necesitás reemplazarla. JPG, PNG o WEBP, máximo 5 MB.</p><label class="file-button" for="foto">Cambiar fotografía</label><input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp"></div>
            </div>
            <div class="field-grid">
                <label class="field full"><span>Nombre completo del comunicador</span><input name="nombre_completo" required maxlength="150" value="<?= htmlspecialchars($registro['nombre_completo']) ?>"></label>
                <label class="field"><span>Edad</span><input name="edad" type="number" required min="18" max="100" inputmode="numeric" value="<?= (int)$registro['edad'] ?>"></label>
                <label class="field"><span>Número de DUI</span><input id="dui" name="dui" required maxlength="10" pattern="\d{8}-\d" inputmode="numeric" value="<?= htmlspecialchars($registro['dui']) ?>"></label>
                <label class="field full"><span>Nombre del medio de comunicación</span><input name="medio_comunicacion" required maxlength="150" value="<?= htmlspecialchars($registro['medio_comunicacion']) ?>"></label>
                <label class="field"><span>Cargo o función</span><input name="cargo_funcion" required maxlength="100" value="<?= htmlspecialchars($registro['cargo_funcion']) ?>"></label>
                <label class="field"><span>Tipo de medio</span><select name="tipo_medio" required><?php foreach ($tipos as $opcion): ?><option <?= $registro['tipo_medio'] === $opcion ? 'selected' : '' ?>><?= htmlspecialchars($opcion) ?></option><?php endforeach; ?></select></label>
                <label class="field"><span>Número de teléfono</span><input name="telefono" type="tel" required maxlength="30" inputmode="tel" value="<?= htmlspecialchars($registro['telefono']) ?>"></label>
                <label class="field"><span>Correo electrónico</span><input name="correo" type="email" required maxlength="150" inputmode="email" value="<?= htmlspecialchars($registro['correo']) ?>"></label>
            </div>
            <button class="primary-button" type="submit">Guardar cambios <span>→</span></button>
        </form>
    </section>
</main>
<footer><img src="../assets/escudo-diocesis.jpg" alt=""><div><b>Diócesis de Zacatecoluca</b><span>Nuestra Señora de los Pobres</span></div><p>Logística de medios · 2026</p></footer>
<script src="../assets/app.js"></script>
</body>
</html>
