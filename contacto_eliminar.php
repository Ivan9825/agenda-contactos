<?php
// =========================================================
// Eliminar un contacto
// Solo acepta POST. La confirmación se pide antes en el navegador
// (evento submit en assets/js/app.js).
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
requerir_sesion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf()) {
    flash('error', 'Solicitud no válida.');
    header('Location: panel.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Solo se borra si el contacto pertenece al usuario de la sesión
$stmt = $pdo->prepare('DELETE FROM contactos WHERE id = ? AND id_usuario = ?');
$stmt->execute([$id, $_SESSION['usuario_id']]);

if ($stmt->rowCount() === 1) {
    flash('exito', 'Contacto eliminado correctamente.');
} else {
    flash('error', 'El contacto no existe o no tiene permiso para eliminarlo.');
}

header('Location: panel.php');
exit;
