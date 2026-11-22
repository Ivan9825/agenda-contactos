<?php
// =========================================================
// Funciones comunes: sesión, seguridad y utilidades
// =========================================================

// Configuración segura de la cookie de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,   // JavaScript no puede leer la cookie de sesión
        'samesite' => 'Lax',  // ayuda a prevenir peticiones de otros sitios
    ]);
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/**
 * Verifica que exista una sesión activa.
 * Se llama al inicio de todas las páginas protegidas.
 */
function requerir_sesion(): void
{
    if (empty($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Si el usuario ya inició sesión, lo envía al panel.
 * Se usa en login y registro.
 */
function redirigir_si_hay_sesion(): void
{
    if (!empty($_SESSION['usuario_id'])) {
        header('Location: panel.php');
        exit;
    }
}

/**
 * Escapa texto antes de mostrarlo en HTML (evita XSS).
 */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Genera (una sola vez por sesión) un token CSRF para los formularios.
 */
function token_csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Comprueba que el token CSRF enviado sea el correcto.
 */
function validar_csrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

/**
 * Guarda un mensaje para mostrarlo en la siguiente página.
 */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/**
 * Muestra y borra el mensaje guardado con flash().
 */
function mostrar_flash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="alerta alerta-' . e($f['tipo']) . '">' . e($f['mensaje']) . '</div>';
}

/**
 * Devuelve las categorías como arreglo [id => nombre].
 */
function obtener_categorias(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, nombre FROM categorias ORDER BY id');
    $categorias = [];
    foreach ($stmt as $fila) {
        $categorias[(int) $fila['id']] = $fila['nombre'];
    }
    return $categorias;
}

/**
 * Valida los datos de un contacto en el servidor.
 * Devuelve un arreglo con los errores encontrados (vacío si todo está bien).
 */
function validar_contacto(array $d, array $categorias): array
{
    $errores = [];

    if ($d['nombre'] === '' || mb_strlen($d['nombre']) > 100) {
        $errores[] = 'El nombre es obligatorio (máximo 100 caracteres).';
    }
    if (!preg_match('/^[0-9+\-\s]{7,20}$/', $d['telefono'])) {
        $errores[] = 'El teléfono debe tener entre 7 y 20 dígitos (puede incluir +, - y espacios).';
    }
    if (!filter_var($d['correo'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['correo']) > 150) {
        $errores[] = 'Ingrese un correo electrónico válido.';
    }
    if ($d['ciudad'] === '' || mb_strlen($d['ciudad']) > 80) {
        $errores[] = 'La ciudad es obligatoria (máximo 80 caracteres).';
    }
    if (!isset($categorias[$d['id_categoria']])) {
        $errores[] = 'Seleccione una categoría válida.';
    }

    return $errores;
}

/**
 * Lee y limpia los campos de un contacto enviados por POST.
 */
function datos_contacto_post(): array
{
    return [
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'correo'   => trim($_POST['correo'] ?? ''),
        'ciudad'   => trim($_POST['ciudad'] ?? ''),
        'id_categoria' => (int) ($_POST['id_categoria'] ?? 0),
        'favorito'     => isset($_POST['favorito']) ? 1 : 0,
    ];
}

/**
 * Prepara un texto para usarlo dentro de LIKE.
 * Los comodines % y _ se escapan con ! (ver ESCAPE '!' en panel.php).
 */
function escapar_like(string $texto): string
{
    return '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $texto) . '%';
}
