<?php
// =========================================================
// Conexión a la base de datos con PDO
// Cambia estos datos según tu instalación (XAMPP por defecto).
// =========================================================

$DB_HOST = 'localhost';
$DB_NAME = 'agenda_contactos';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
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
