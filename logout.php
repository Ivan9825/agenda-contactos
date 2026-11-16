<?php
// =========================================================
// Cierre de sesión
// =========================================================
require_once __DIR__ . '/includes/funciones.php';

// Vaciar las variables de sesión
$_SESSION = [];

// Borrar la cookie de sesión del navegador
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destruir la sesión en el servidor
session_destroy();

// Iniciar una sesión nueva solo para mostrar el mensaje
session_start();
flash('exito', 'Sesión cerrada correctamente.');

header('Location: login.php');
exit;
