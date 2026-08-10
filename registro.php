<?php
declare(strict_types=1);
session_start();
$exito = $_SESSION['registro_exito'] ?? null;
unset($_SESSION['registro_exito']);
$_SESSION['csrf'] = bin2hex(random_bytes(24));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#073d2b">
    <title>Formulario de acreditación | Ordenación Episcopal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="registration-page">
<header class="topbar">
    <a class="brand" href="index.php"><img src="assets/escudo-diocesis.jpg" alt="Escudo diocesano"><span><b>Pastoral de Comunicaciones</b><small>Diócesis de Zacatecoluca</small></span></a>
    <a class="admin-link" href="index.php">Volver al inicio</a>
</header>
<main>
    <section class="form-banner">
        <span class="eyebrow">Logística de medios</span>
        <h1>Formulario de acreditación</h1>
        <p>Ordenación Episcopal de Monseñor Ramiro Landaverde</p>
        <b>DIÓCESIS DE ZACATECOLUCA</b>
    </section>
    <section class="form-section">
        <aside class="form-intro">
            <span class="section-number">01</span><span class="eyebrow dark">Registro de medios</span>
            <h2>Datos del comunicador</h2>
            <p>Completá la información para formar parte de la cobertura oficial del evento.</p>
            <div class="steps-card">
                <div><span>1</span><p>Ingresá tus datos.</p></div>
                <div><span>2</span><p>Subí una fotografía clara.</p></div>
                <div><span>3</span><p>Esperá la respuesta por correo.</p></div>
            </div>
            <div class="privacy-note"><span>✦</span><p>Los datos se utilizarán únicamente para organización, acreditación y logística de medios.</p></div>
        </aside>
        <form class="registration-card" action="register.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <div class="form-card-header"><span>Paso único</span><h2>Información personal y profesional</h2><p>Todos los campos son obligatorios.</p></div>
            <div class="photo-upload">
                <label class="photo-preview" for="foto"><img id="fotoPreview" alt="Vista previa de la fotografía" hidden><span id="fotoIcon">+</span></label>
                <div><b>Fotografía del comunicador</b><p>Imagen clara y de frente. JPG, PNG o WEBP, máximo 5 MB.</p><label class="file-button" for="foto">Seleccionar fotografía</label><input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" required></div>
            </div>
            <div class="field-grid">
                <label class="field full"><span>Nombre completo del comunicador</span><input name="nombre_completo" required maxlength="150" autocomplete="name" placeholder="Nombres y apellidos"></label>
                <label class="field"><span>Edad</span><input name="edad" type="number" required min="18" max="100" inputmode="numeric" placeholder="Ej. 28"></label>
                <label class="field"><span>Número de DUI</span><input id="dui" name="dui" required maxlength="10" pattern="\d{8}-\d" inputmode="numeric" placeholder="00000000-0"></label>
                <label class="field full"><span>Nombre del medio de comunicación</span><input name="medio_comunicacion" required maxlength="150" placeholder="Nombre oficial del medio"></label>
                <label class="field"><span>Cargo o función</span><input name="cargo_funcion" required maxlength="100" placeholder="Fotógrafo, periodista…"></label>
                <label class="field"><span>Tipo de medio</span><select name="tipo_medio" required><option value="">Seleccioná una opción</option><option>Televisión</option><option>Radio</option><option>Prensa escrita</option><option>Medio digital</option><option>Otro</option></select></label>
                <label class="field"><span>Número de teléfono</span><input name="telefono" type="tel" required maxlength="30" autocomplete="tel" inputmode="tel" placeholder="0000-0000"></label>
                <label class="field"><span>Correo electrónico</span><input name="correo" type="email" required maxlength="150" autocomplete="email" inputmode="email" placeholder="nombre@medio.com"></label>
            </div>
            <button class="primary-button" type="submit">Enviar solicitud <span>→</span></button>
        </form>
    </section>
</main>
<?php if ($exito): ?>
<div class="success-modal" role="dialog" aria-modal="true">
    <section class="credential-dialog">
        <button type="button" class="modal-close" aria-label="Cerrar">×</button>
        <div class="credential request-confirmation">
            <header class="credential-header"><img src="assets/escudo-diocesis.jpg" alt=""><div><b>Diócesis de Zacatecoluca</b><span>Logística de medios</span></div></header>
            <div class="credential-title"><span>Solicitud recibida</span><h2>Registro pendiente de revisión</h2><p>Ordenación Episcopal</p></div>
            <div class="request-confirmation-body">
                <h3><?= htmlspecialchars($exito['nombre']) ?></h3>
                <p>Tu información fue guardada correctamente.</p>
                <p>El equipo de Logística de Medios revisará la solicitud y enviará la respuesta a:</p>
                <b><?= htmlspecialchars($exito['correo']) ?></b>
            </div>
            <footer class="credential-footer"><span>15 DE AGOSTO DE 2026</span><b>PENDIENTE</b></footer>
        </div>
    </section>
</div>
<?php endif; ?>
<footer><img src="assets/escudo-diocesis.jpg" alt=""><div><b>Diócesis de Zacatecoluca</b><span>Nuestra Señora de los Pobres</span></div><p>Logística de medios · 2026</p></footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
