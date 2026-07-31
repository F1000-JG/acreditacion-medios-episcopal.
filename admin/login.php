<?php
declare(strict_types=1);
session_start();
if (!empty($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string)($_POST['password'] ?? '');
    $adminPassword = getenv('ADMIN_PASSWORD') ?: '';
    if ($adminPassword !== '' && hash_equals($adminPassword, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Contraseña incorrecta.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Administración | Acreditación de Medios</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="login-page">
<main class="login-card">
    <img src="../assets/escudo-diocesis.jpg" alt="Escudo diocesano">
    <span class="eyebrow dark">Área privada</span>
    <h1>Pastoral de Comunicaciones</h1>
    <p>Ingresá para consultar los medios registrados.</p>
    <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label class="field"><span>Contraseña administrativa</span><input name="password" type="password" required autofocus></label>
        <button class="primary-button" type="submit">Ingresar</button>
    </form>
    <a href="../index.php">← Volver al inicio</a>
</main>
</body>
</html>
