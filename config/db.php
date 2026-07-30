<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';
    $name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'acreditacion_medios';
    $user = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS comunicadores (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(24) NOT NULL UNIQUE,
            nombre_completo VARCHAR(150) NOT NULL,
            edad TINYINT UNSIGNED NOT NULL,
            medio_comunicacion VARCHAR(150) NOT NULL,
            cargo_funcion VARCHAR(100) NOT NULL,
            telefono VARCHAR(30) NOT NULL,
            correo VARCHAR(150) NOT NULL,
            dui VARCHAR(10) NOT NULL UNIQUE,
            tipo_medio ENUM('Televisión','Radio','Prensa escrita','Medio digital','Otro') NOT NULL,
            foto LONGBLOB NOT NULL,
            foto_tipo VARCHAR(50) NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_nombre (nombre_completo),
            INDEX idx_medio (medio_comunicacion)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    return $pdo;
}
