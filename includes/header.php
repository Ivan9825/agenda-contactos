<?php
// Encabezado común. Antes de incluirlo, cada página define $titulo.
$titulo = $titulo ?? 'Agenda de Contactos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | Agenda de Contactos</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<header class="barra">
    <a class="marca" href="index.php">📒 Agenda de Contactos</a>
    <?php if (!empty($_SESSION['usuario_id'])): ?>
        <nav>
            <span class="saludo">Hola, <?= e($_SESSION['usuario_nombre']) ?></span>
            <a href="panel.php">Mis contactos</a>
            <a href="logout.php" class="boton boton-secundario">Cerrar sesión</a>
        </nav>
    <?php endif; ?>
</header>
<main class="contenedor">
<?= mostrar_flash() ?>
