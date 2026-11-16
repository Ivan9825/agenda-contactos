<?php
// =========================================================
// Inicio de sesión
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
redirigir_si_hay_sesion();

$error  = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!validar_csrf()) {
        $error = 'La sesión del formulario expiró. Vuelva a intentarlo.';
    } elseif ($correo === '' || $password === '') {
        $error = 'Ingrese su correo y su contraseña.';
    } else {
        // Buscar al usuario por su correo
        $stmt = $pdo->prepare('SELECT id, nombre, correo, password FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        // Comprobar la contraseña contra el hash guardado
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Nuevo ID de sesión para evitar la fijación de sesión
            session_regenerate_id(true);

            $_SESSION['usuario_id']     = (int) $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];

            header('Location: panel.php');
            exit;
        }

        // Mensaje genérico: no revela si el correo existe o no
        $error = 'Correo o contraseña incorrectos.';
    }
}

$titulo = 'Iniciar sesión';
require __DIR__ . '/includes/header.php';
?>
<section class="tarjeta tarjeta-angosta">
    <h1>Iniciar sesión</h1>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="formulario" data-validar novalidate>
        <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" required value="<?= e($correo) ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="boton">Ingresar</button>
    </form>

    <p class="enlace-alterno">¿No tiene cuenta? <a href="registro.php">Regístrese aquí</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
