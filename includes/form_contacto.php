<?php
// Formulario reutilizado para crear y editar contactos.
// Usa las variables $errores, $contacto, $categorias y $boton de la página que lo incluye.
?>
<?php if ($errores): ?>
    <div class="alerta alerta-error">
        <ul><?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="formulario" data-validar novalidate>
    <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">

    <label for="nombre">Nombre</label>
    <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?= e($contacto['nombre']) ?>">

    <label for="telefono">Teléfono</label>
    <input type="tel" id="telefono" name="telefono" maxlength="20" required
           pattern="[0-9+\-\s]{7,20}" value="<?= e($contacto['telefono']) ?>">

    <label for="correo">Correo electrónico</label>
    <input type="email" id="correo" name="correo" maxlength="150" required value="<?= e($contacto['correo']) ?>">

    <label for="ciudad">Ciudad</label>
    <input type="text" id="ciudad" name="ciudad" maxlength="80" required value="<?= e($contacto['ciudad']) ?>">

    <label for="id_categoria">Categoría</label>
    <select id="id_categoria" name="id_categoria" required>
        <option value="">Seleccione...</option>
        <?php foreach ($categorias as $idCat => $nombreCat): ?>
            <option value="<?= $idCat ?>" <?= (int) $contacto['id_categoria'] === $idCat ? 'selected' : '' ?>>
                <?= e($nombreCat) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label class="check">
        <input type="checkbox" name="favorito" value="1" <?= !empty($contacto['favorito']) ? 'checked' : '' ?>>
        Marcar como favorito ★
    </label>

    <div class="botones">
        <button type="submit" class="boton"><?= e($boton) ?></button>
        <a href="panel.php" class="boton boton-secundario">Cancelar</a>
    </div>
</form>
