<?php
// =========================================================
// Conexión a la base de datos con PDO
// En local usa los datos de XAMPP. En el hosting se usan los datos
// de config/credenciales.php (ese archivo no se sube a GitHub).
// =========================================================

$config = [
    'host' => 'localhost',
    'name' => 'agenda_contactos',
    'user' => 'root',
    'pass' => '',
];

if (is_file(__DIR__ . '/credenciales.php')) {
    $config = array_merge($config, require __DIR__ . '/credenciales.php');
}

$dsn = $config['dsn'] ?? "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4";

try {
    $pdo = new PDO(
        $dsn,
        $config['user'],
        $config['pass'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lanza errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // resultados como arreglos asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                 // consultas preparadas reales
        ]
    );
} catch (PDOException $e) {
    // No se muestra el error técnico al usuario para no revelar información sensible
    error_log('Error de conexión: ' . $e->getMessage());
    die('No se pudo conectar con la base de datos. Intente más tarde.');
}

unset($config);
