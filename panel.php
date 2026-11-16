<?php
// =========================================================
// Panel principal (página privada)
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
requerir_sesion();

// Solo se consultan los contactos del usuario que inició sesión
$stmt = $pdo->prepare(
    'SELECT id, nombre, telefono, correo, ciudad
       FROM contactos
      WHERE id_usuario = ?
   ORDER BY nombre'
);
$stmt->execute([$_SESSION['usuario_id']]);
$contactos = $stmt->fetchAll();

$titulo = 'Mis contactos';
require __DIR__ . '/includes/header.php';
?>
<section class="tarjeta">
    <h1>Mi perfil</h1>
    <p><strong>Nombre:</strong> <?= e($_SESSION['usuario_nombre']) ?></p>
    <p><strong>Correo:</strong> <?= e($_SESSION['usuario_correo']) ?></p>
</section>

<section class="tarjeta">
    <div class="encabezado-seccion">
        <h2>Mis contactos (<?= count($contactos) ?>)</h2>
        <a href="contacto_nuevo.php" class="boton">+ Nuevo contacto</a>
    </div>

    <?php if (!$contactos): ?>
        <p class="vacio">Todavía no tiene contactos. Agregue el primero con el botón "Nuevo contacto".</p>
    <?php else: ?>
        <input type="search" id="buscador" class="buscador" placeholder="Buscar por nombre o ciudad...">

        <div class="tabla-responsiva">
            <table class="tabla" id="tabla-contactos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Ciudad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contactos as $c): ?>
                    <tr>
                        <td><?= e($c['nombre']) ?></td>
                        <td><?= e($c['telefono']) ?></td>
                        <td><?= e($c['correo']) ?></td>
                        <td><?= e($c['ciudad']) ?></td>
                        <td class="acciones">
                            <a href="contacto_editar.php?id=<?= (int) $c['id'] ?>" class="boton boton-pequeno">Editar</a>

                            <!-- Eliminar por POST con confirmación previa (ver assets/js/app.js) -->
                            <form method="post" action="contacto_eliminar.php" class="form-eliminar"
                                  data-nombre="<?= e($c['nombre']) ?>">
                                <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                <button type="submit" class="boton boton-peligro boton-pequeno">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
