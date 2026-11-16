<?php
// Punto de entrada: envía al panel si hay sesión, si no al login.
require_once __DIR__ . '/includes/funciones.php';

header('Location: ' . (empty($_SESSION['usuario_id']) ? 'login.php' : 'panel.php'));
exit;
