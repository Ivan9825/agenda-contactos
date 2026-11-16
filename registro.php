<?php
// =========================================================
// Registro de nuevos usuarios
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
redirigir_si_hay_sesion();

$errores = [];
$nombre  = '';
$correo  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $correo    = trim($_POST['correo'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // --- Validación en el servidor ---
    if (!validar_csrf()) {
        $errores[] = 'La sesión del formulario expiró. Vuelva a intentarlo.';
    }
    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre es obligatorio (máximo 100 caracteres).';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 150) {
        $errores[] = 'Ingrese un correo electrónico válido.';
    }
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $password2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    // --- Verificar que el correo no exista previamente ---
    if (!$errores) {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        if ($stmt->fetch()) {
            $errores[] = 'Ese correo ya está registrado. Inicie sesión o use otro correo.';
        }
    }

    // --- Guardar el usuario con la contraseña cifrada ---
    if (!$errores) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)');
        $stmt->execute([$nombre, $correo, $hash]);

        flash('exito', 'Cuenta creada correctamente. Ya puede iniciar sesión.');
        header('Location: login.php');
        exit;
    }
}

$titulo = 'Registro';
require __DIR__ . '/includes/header.php';
?>
<section class="tarjeta tarjeta-angosta">
    <h1>Crear cuenta</h1>

    <?php if ($errores): ?>
        <div class="alerta alerta-error">
            <ul><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="formulario" data-validar novalidate>
        <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?= e($nombre) ?>">

        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" maxlength="150" required value="<?= e($correo) ?>">

        <label for="password">Contraseña (mínimo 8 caracteres)</label>
        <input type="password" id="password" name="password" minlength="8" required>

        <label for="password2">Repetir contraseña</label>
        <input type="password" id="password2" name="password2" minlength="8" required data-igual-a="password">

        <button type="submit" class="boton">Registrarme</button>
    </form>

    <p class="enlace-alterno">¿Ya tiene cuenta? <a href="login.php">Inicie sesión</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
