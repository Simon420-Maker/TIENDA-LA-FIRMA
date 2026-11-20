<?php
/**
 * Consultas de productos y tarjetas HTML reutilizables.
 */
require_once __DIR__ . '/auth.php';

const COLUMNAS_PRODUCTO =
    'Id_Producto AS id, Nombre AS nombre, Precio AS precio, Stock AS stock,
     Imagen AS imagen, Seccion AS seccion, Dia_Semana AS dia';

function listar_cafeteria(): array
{
    return db()->query(
        "SELECT " . COLUMNAS_PRODUCTO . "
           FROM productos
          WHERE Seccion = 'cafeteria' AND Activo = 1
          ORDER BY Orden, Id_Producto"
    )->fetchAll();
}

/** Devuelve ['Lunes' => [plato + 'ingredientes' => [...]], 'Martes' => ...] */
function listar_almuerzos_por_dia(): array
{
    $platos = db()->query(
        "SELECT " . COLUMNAS_PRODUCTO . "
           FROM productos
          WHERE Seccion = 'almuerzo' AND Activo = 1
          ORDER BY Orden, Id_Producto"
    )->fetchAll();

    $ingredientes = [];
    foreach (db()->query('SELECT Id_Producto, Ingrediente FROM plato_ingredientes ORDER BY Id_Ingrediente') as $f) {
        $ingredientes[(int) $f['Id_Producto']][] = $f['Ingrediente'];
    }

    $porDia = array_fill_keys(DIAS_MENU, []);
    foreach ($platos as $p) {
        $p['ingredientes'] = $ingredientes[(int) $p['id']] ?? [];
        $porDia[$p['dia'] ?? DIAS_MENU[0]][] = $p;
    }
    return array_filter($porDia);
}

function buscar_productos(string $texto): array
{
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $texto) . '%';
    $st = db()->prepare(
        "SELECT " . COLUMNAS_PRODUCTO . "
           FROM productos
          WHERE Activo = 1 AND Nombre LIKE ?
          ORDER BY Seccion, Orden, Nombre"
    );
    $st->execute([$like]);
    return $st->fetchAll();
}

function img_producto(array $p): string
{
    return '<img src="' . e(ruta_imagen($p['imagen'])) . '" alt="' . e($p['nombre']) . '" loading="lazy"'
         . ' onerror="this.onerror=null;this.src=\'img/sin-imagen.svg\'">';
}

/** Tarjeta simple de cafetería (botón Comprar). */
function tarjeta_cafeteria(array $p): void
{
    $agotado = (int) $p['stock'] <= 0; ?>
    <div class="product-card"
         data-id="<?= (int) $p['id'] ?>"
         data-name="<?= e($p['nombre']) ?>"
         data-price="<?= (int) $p['precio'] ?>"
         data-stock="<?= (int) $p['stock'] ?>">
        <?= img_producto($p) ?>
        <div class="product-info">
            <h3><?= e($p['nombre']) ?></h3>
            <div class="stock"><?= $agotado ? 'Agotado' : 'Quedan: ' . (int) $p['stock'] ?></div>
            <div class="price"><?= formato_precio($p['precio']) ?></div>
            <?php if ($agotado): ?>
                <span class="buy-btn agotado">Agotado</span>
            <?php else: ?>
                <a href="#buy" class="buy-btn">Comprar</a>
            <?php endif; ?>
        </div>
    </div>
<?php }

/** Tarjeta de almuerzo con selector de ingredientes. */
function tarjeta_almuerzo(array $p): void
{
    $agotado = (int) $p['stock'] <= 0; ?>
    <article class="product-card" id="plato-<?= (int) $p['id'] ?>"
             data-id="<?= (int) $p['id'] ?>"
             data-name="<?= e($p['nombre']) ?>"
             data-price="<?= (int) $p['precio'] ?>"
             data-stock="<?= (int) $p['stock'] ?>"
             data-descuento="<?= DESCUENTO_INGREDIENTE ?>">
        <?= img_producto($p) ?>
        <div class="product-info">
            <div class="product-heading"><h2><?= e($p['nombre']) ?></h2><span class="price"><?= formato_precio($p['precio']) ?></span></div>
            <p class="stock"><?= $agotado ? 'Agotado' : 'Quedan: ' . (int) $p['stock'] ?></p>
            <?php if ($p['ingredientes']): ?>
            <fieldset class="ingredient-picker">
                <legend>Ingredientes incluidos</legend>
                <?php foreach ($p['ingredientes'] as $ing): ?>
                    <label><input type="checkbox" value="<?= e($ing) ?>" checked> <?= e($ing) ?></label>
                <?php endforeach; ?>
            </fieldset>
            <?php endif; ?>
            <?php if ($agotado): ?>
                <button class="buy-btn agotado" type="button" disabled>Agotado</button>
            <?php else: ?>
                <button class="buy-btn" type="button">Agregar al pedido</button>
            <?php endif; ?>
            <p class="selection-feedback" aria-live="polite"></p>
        </div>
    </article>
<?php }

/** Tarjeta para resultados de búsqueda de almuerzos: lleva a la página del menú del día. */
function tarjeta_almuerzo_enlace(array $p): void
{ ?>
    <div class="product-card">
        <?= img_producto($p) ?>
        <div class="product-info">
            <h3><?= e($p['nombre']) ?></h3>
            <div class="stock">Menu del día · <?= e($p['dia'] ?? '') ?></div>
            <div class="price"><?= formato_precio($p['precio']) ?></div>
            <a href="almuerzos.php#plato-<?= (int) $p['id'] ?>" class="buy-btn">Ver plato</a>
        </div>
    </div>
<?php }
