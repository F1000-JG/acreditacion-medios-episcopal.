<?php
declare(strict_types=1);
session_start();
$exito = $_SESSION['registro_exito'] ?? null;
unset($_SESSION['registro_exito']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Acreditación de medios para la Ordenación Episcopal de Monseñor Ramiro Landaverde.">
    <title>Acreditación de Medios | Ordenación Episcopal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <img src="assets/escudo-diocesis.jpg" alt="Escudo de la Diócesis de Zacatecoluca">
        <span><b>Pastoral de Comunicaciones</b><small>Diócesis de Zacatecoluca</small></span>
    </a>
    <a class="admin-link" href="admin/login.php">Acceso administrativo</a>
</header>

<main>
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">Acreditación oficial de prensa</span>
            <h1>Ordenación Episcopal de <em>Monseñor Ramiro Landaverde</em></h1>
            <div class="event-meta">
                <div><span>Fecha</span><b>15 de agosto de 2026</b></div>
                <div><span>Organiza</span><b>Pastoral de Comunicaciones</b></div>
            </div>
        </div>
        <div class="seal-wrap">
            <div class="gold-ring"></div>
            <img src="assets/escudo-diocesis.jpg" alt="">
        </div>
    </section>

    <section class="form-section" id="registro">
        <aside class="form-intro">
            <span class="section-number">01</span>
            <span class="eyebrow dark">Registro de medios</span>
            <h2>Datos del comunicador</h2>
            <p>Completá la información solicitada para participar en la cobertura de esta celebración episcopal.</p>
            <div class="privacy-note">
                <span>✦</span>
                <p>Los datos serán utilizados únicamente para la organización y acreditación de los medios.</p>
            </div>
        </aside>

        <form class="registration-card" action="register.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] = bin2hex(random_bytes(24))) ?>">
            <div class="photo-upload">
                <label class="photo-preview" for="foto">
                    <img id="fotoPreview" alt="" hidden>
                    <span id="fotoIcon">+</span>
                </label>
                <div>
                    <b>Fotografía del comunicador</b>
                    <p>Imagen clara y de frente. JPG, PNG o WEBP, máximo 5 MB.</p>
                    <label class="file-button" for="foto">Seleccionar fotografía</label>
                    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" required>
                </div>
            </div>

            <div class="field-grid">
                <label class="field full">
                    <span>Nombre completo del comunicador</span>
                    <input name="nombre_completo" required maxlength="150" autocomplete="name" placeholder="Escribí nombres y apellidos">
                </label>
                <label class="field">
                    <span>Edad</span>
                    <input name="edad" type="number" required min="18" max="100" placeholder="Ej. 28">
                </label>
                <label class="field">
                    <span>Número de DUI</span>
                    <input id="dui" name="dui" required maxlength="10" pattern="\d{8}-\d" placeholder="00000000-0">
                </label>
                <label class="field full">
                    <span>Nombre del medio de comunicación</span>
                    <input name="medio_comunicacion" required maxlength="150" placeholder="Nombre oficial del medio">
                </label>
                <label class="field">
                    <span>Cargo o función</span>
                    <input name="cargo_funcion" required maxlength="100" placeholder="Fotógrafo, periodista…">
                </label>
                <label class="field">
                    <span>Tipo de medio</span>
                    <select name="tipo_medio" required>
                        <option value="">Seleccioná una opción</option>
                        <option>Televisión</option>
                        <option>Radio</option>
                        <option>Prensa escrita</option>
                        <option>Medio digital</option>
                        <option>Otro</option>
                    </select>
                </label>
                <label class="field">
                    <span>Número de teléfono</span>
                    <input name="telefono" type="tel" required maxlength="30" autocomplete="tel" placeholder="0000-0000">
                </label>
                <label class="field">
                    <span>Correo electrónico</span>
                    <input name="correo" type="email" required maxlength="150" autocomplete="email" placeholder="nombre@medio.com">
                </label>
            </div>

            <button class="primary-button" type="submit">Completar registro <span>→</span></button>
        </form>
    </section>
</main>

<?php if ($exito): ?>
<div class="success-modal" role="dialog" aria-modal="true">
    <article class="receipt">
        <button type="button" class="modal-close" aria-label="Cerrar">×</button>
        <img src="assets/escudo-diocesis.jpg" alt="">
        <span class="eyebrow dark">Registro completado</span>
        <h2><?= htmlspecialchars($exito['nombre']) ?></h2>
        <p><?= htmlspecialchars($exito['cargo']) ?> · <?= htmlspecialchars($exito['medio']) ?></p>
        <div class="registration-code"><?= htmlspecialchars($exito['codigo']) ?></div>
        <small>Conservá este código como comprobante.</small>
        <button class="primary-button print-receipt" type="button">Imprimir comprobante</button>
    </article>
</div>
<?php endif; ?>

<footer>
    <img src="assets/escudo-diocesis.jpg" alt="">
    <div><b>Diócesis de Zacatecoluca</b><span>Nuestra Señora de los Pobres</span></div>
    <p>Pastoral de Comunicaciones · 2026</p>
</footer>
<script src="assets/app.js"></script>
</body>
</html>
