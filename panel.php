<?php
// =========================================================
// Panel principal (página privada)
// Versión 2: búsqueda, ordenamiento, categorías y favoritos
// =========================================================
require_once __DIR__ . '/includes/funciones.php';
requerir_sesion();

$categorias = obtener_categorias($pdo);

// ---------- Leer los filtros enviados por GET ----------
$campos = ['todos' => 'Todos los campos', 'nombre' => 'Nombre', 'correo' => 'Correo', 'ciudad' => 'Ciudad'];
$ordenes = [
    'nombre' => ['texto' => 'Nombre',            'sql' => 'c.nombre'],
    'ciudad' => ['texto' => 'Ciudad',            'sql' => 'c.ciudad'],
    'fecha'  => ['texto' => 'Fecha de creación', 'sql' => 'c.fecha_creacion'],
];

$q         = trim($_GET['q'] ?? '');
$campo     = isset($campos[$_GET['campo'] ?? '']) ? $_GET['campo'] : 'todos';
$categoria = (int) ($_GET['categoria'] ?? 0);
$categoria = isset($categorias[$categoria]) ? $categoria : 0;
$soloFav   = ($_GET['favoritos'] ?? '') === '1';
$orden     = isset($ordenes[$_GET['orden'] ?? '']) ? $_GET['orden'] : 'nombre';
$dir       = ($_GET['dir'] ?? '') === 'desc' ? 'DESC' : 'ASC';

// ---------- Armar la consulta ----------
// Siempre se filtra por el usuario de la sesión.
$where  = ['c.id_usuario = ?'];
$params = [$_SESSION['usuario_id']];

if ($q !== '') {
    $patron = escapar_like($q);
    if ($campo === 'todos') {
        $where[] = "(c.nombre LIKE ? ESCAPE '!' OR c.correo LIKE ? ESCAPE '!' OR c.ciudad LIKE ? ESCAPE '!')";
        array_push($params, $patron, $patron, $patron);
    } else {
        // $campo solo puede ser nombre, correo o ciudad (lista cerrada de arriba)
        $where[]  = "c.$campo LIKE ? ESCAPE '!'";
        $params[] = $patron;
    }
}
if ($categoria) {
    $where[]  = 'c.id_categoria = ?';
    $params[] = $categoria;
}
if ($soloFav) {
    $where[] = 'c.favorito = 1';
}

// La columna de orden sale de la lista $ordenes, nunca directo del usuario
$sqlOrden = $ordenes[$orden]['sql'] . ' ' . $dir . ($orden !== 'nombre' ? ', c.nombre ASC' : '');

$stmt = $pdo->prepare(
    'SELECT c.id, c.nombre, c.telefono, c.correo, c.ciudad, c.favorito, c.fecha_creacion,
            cat.nombre AS categoria
       FROM contactos c
       JOIN categorias cat ON cat.id = c.id_categoria
      WHERE ' . implode(' AND ', $where) . '
   ORDER BY ' . $sqlOrden
);
$stmt->execute($params);
$contactos = $stmt->fetchAll();

// Total de contactos del usuario (sin filtros)
$stmt = $pdo->prepare('SELECT COUNT(*) FROM contactos WHERE id_usuario = ?');
$stmt->execute([$_SESSION['usuario_id']]);
$total = (int) $stmt->fetchColumn();

$hayFiltros = $q !== '' || $categoria || $soloFav;

// Filtros actuales, para volver al mismo listado después de marcar un favorito
$filtrosActuales = http_build_query(array_filter([
    'q' => $q, 'campo' => $campo, 'categoria' => $categoria ?: '',
    'favoritos' => $soloFav ? '1' : '', 'orden' => $orden, 'dir' => strtolower($dir),
]));

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
        <h2>Mis contactos (<?= $total ?>)</h2>
        <a href="contacto_nuevo.php" class="boton">+ Nuevo contacto</a>
    </div>

    <?php if (!$total): ?>
        <p class="vacio">Todavía no tiene contactos. Agregue el primero con el botón "Nuevo contacto".</p>
    <?php else: ?>
        <form method="get" class="filtros" id="form-filtros">
            <div class="filtro filtro-busqueda">
                <label for="q">Buscar</label>
                <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Escriba un nombre, correo o ciudad...">
            </div>
            <div class="filtro">
                <label for="campo">Buscar en</label>
                <select id="campo" name="campo" data-auto>
                    <?php foreach ($campos as $valor => $texto): ?>
                        <option value="<?= $valor ?>" <?= $campo === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filtro">
                <label for="categoria">Categoría</label>
                <select id="categoria" name="categoria" data-auto>
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $idCat => $nombreCat): ?>
                        <option value="<?= $idCat ?>" <?= $categoria === $idCat ? 'selected' : '' ?>><?= e($nombreCat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filtro">
                <label for="orden">Ordenar por</label>
                <select id="orden" name="orden" data-auto>
                    <?php foreach ($ordenes as $valor => $o): ?>
                        <option value="<?= $valor ?>" <?= $orden === $valor ? 'selected' : '' ?>><?= e($o['texto']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filtro">
                <label for="dir">Sentido</label>
                <select id="dir" name="dir" data-auto>
                    <option value="asc" <?= $dir === 'ASC' ? 'selected' : '' ?>>Ascendente</option>
                    <option value="desc" <?= $dir === 'DESC' ? 'selected' : '' ?>>Descendente</option>
                </select>
            </div>
            <label class="check filtro-check">
                <input type="checkbox" name="favoritos" value="1" data-auto <?= $soloFav ? 'checked' : '' ?>>
                Solo favoritos ★
            </label>
            <div class="botones-filtro">
                <button type="submit" class="boton">Buscar</button>
                <?php if ($hayFiltros): ?>
                    <a href="panel.php" class="boton boton-secundario">Limpiar filtros</a>
                <?php endif; ?>
            </div>
        </form>

        <p class="resumen">Mostrando <?= count($contactos) ?> de <?= $total ?> contactos.</p>

        <?php if (!$contactos): ?>
            <p class="vacio">No se encontraron contactos con esos filtros.</p>
        <?php else: ?>
            <div class="tabla-responsiva">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Fav.</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Ciudad</th>
                            <th>Categoría</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($contactos as $c): ?>
                        <tr>
                            <td>
                                <form method="post" action="contacto_favorito.php">
                                    <input type="hidden" name="csrf" value="<?= e(token_csrf()) ?>">
                                    <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                    <input type="hidden" name="volver" value="<?= e($filtrosActuales) ?>">
                                    <button type="submit" class="estrella <?= $c['favorito'] ? 'activa' : '' ?>"
                                            title="<?= $c['favorito'] ? 'Quitar de favoritos' : 'Marcar como favorito' ?>">
                                        <?= $c['favorito'] ? '★' : '☆' ?>
                                    </button>
                                </form>
                            </td>
                            <td><?= e($c['nombre']) ?></td>
                            <td><?= e($c['telefono']) ?></td>
                            <td><?= e($c['correo']) ?></td>
                            <td><?= e($c['ciudad']) ?></td>
                            <td><span class="etiqueta etiqueta-<?= e(strtolower($c['categoria'])) ?>"><?= e($c['categoria']) ?></span></td>
                            <td><?= e(date('d/m/Y', strtotime($c['fecha_creacion']))) ?></td>
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
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
