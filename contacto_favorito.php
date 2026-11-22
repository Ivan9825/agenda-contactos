<?php
// =========================================================
// Marcar o desmarcar un contacto como favorito
// Solo acepta POST (con token CSRF), igual que eliminar.
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
requerir_sesion();

// Volver al panel con los mismos filtros que tenía el usuario
$filtros = $_POST['volver'] ?? '';
$destino = 'panel.php' . (preg_match('/^[\w=&%+.\-]*$/', $filtros) && $filtros !== '' ? '?' . $filtros : '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validar_csrf()) {
    flash('error', 'Solicitud no válida.');
    header('Location: ' . $destino);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Cambia 0 por 1 o 1 por 0, solo si el contacto es del usuario de la sesión
$stmt = $pdo->prepare('UPDATE contactos SET favorito = 1 - favorito WHERE id = ? AND id_usuario = ?');
$stmt->execute([$id, $_SESSION['usuario_id']]);

if ($stmt->rowCount() !== 1) {
    flash('error', 'El contacto no existe o no tiene permiso para modificarlo.');
}

header('Location: ' . $destino);
exit;
