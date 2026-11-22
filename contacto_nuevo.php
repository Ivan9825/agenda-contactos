<?php
// =========================================================
// Registrar un nuevo contacto
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
requerir_sesion();

$errores    = [];
$categorias = obtener_categorias($pdo);
$contacto   = [
    'nombre' => '', 'telefono' => '', 'correo' => '', 'ciudad' => '',
    'id_categoria' => 0, 'favorito' => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contacto = datos_contacto_post();
    $errores  = validar_contacto($contacto, $categorias);

    if (!validar_csrf()) {
        $errores[] = 'La sesión del formulario expiró. Vuelva a intentarlo.';
    }

    if (!$errores) {
        // El propietario se toma de la sesión, nunca del formulario
        $stmt = $pdo->prepare(
            'INSERT INTO contactos (id_usuario, id_categoria, nombre, telefono, correo, ciudad, favorito)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $_SESSION['usuario_id'],
            $contacto['id_categoria'],
            $contacto['nombre'],
            $contacto['telefono'],
            $contacto['correo'],
            $contacto['ciudad'],
            $contacto['favorito'],
        ]);

        flash('exito', 'Contacto agregado correctamente.');
        header('Location: panel.php');
        exit;
    }
}

$titulo  = 'Nuevo contacto';
$boton   = 'Guardar contacto';
require __DIR__ . '/includes/header.php';
?>
<section class="tarjeta tarjeta-angosta">
    <h1>Nuevo contacto</h1>
    <?php require __DIR__ . '/includes/form_contacto.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
