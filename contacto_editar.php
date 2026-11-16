<?php
// =========================================================
// Editar un contacto
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
requerir_sesion();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Buscar el contacto SOLO si pertenece al usuario de la sesión.
// Si alguien cambia el id en la URL por el de otro usuario, no se encuentra nada.
$stmt = $pdo->prepare(
    'SELECT id, nombre, telefono, correo, ciudad FROM contactos WHERE id = ? AND id_usuario = ?'
);
$stmt->execute([$id, $_SESSION['usuario_id']]);
$contacto = $stmt->fetch();

if (!$contacto) {
    // Mismo mensaje si no existe o si es de otro usuario: no se revela información
    flash('error', 'El contacto no existe o no tiene permiso para verlo.');
    header('Location: panel.php');
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contacto = array_merge($contacto, datos_contacto_post());
    $errores  = validar_contacto($contacto);

    if (!validar_csrf()) {
        $errores[] = 'La sesión del formulario expiró. Vuelva a intentarlo.';
    }

    if (!$errores) {
        // El WHERE vuelve a comprobar que el contacto sea del usuario
        $stmt = $pdo->prepare(
            'UPDATE contactos
                SET nombre = ?, telefono = ?, correo = ?, ciudad = ?
              WHERE id = ? AND id_usuario = ?'
        );
        $stmt->execute([
            $contacto['nombre'],
            $contacto['telefono'],
            $contacto['correo'],
            $contacto['ciudad'],
            $id,
            $_SESSION['usuario_id'],
        ]);

        flash('exito', 'Contacto actualizado correctamente.');
        header('Location: panel.php');
        exit;
    }
}

$titulo = 'Editar contacto';
$boton  = 'Guardar cambios';
require __DIR__ . '/includes/header.php';
?>
<section class="tarjeta tarjeta-angosta">
    <h1>Editar contacto</h1>
    <?php require __DIR__ . '/includes/form_contacto.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
