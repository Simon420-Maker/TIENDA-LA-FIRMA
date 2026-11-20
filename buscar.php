<?php
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/productos.php';
require_once __DIR__ . '/includes/carrito.php';

$q = mb_substr(trim($_GET['q'] ?? ''), 0, 60);
$resultados = $q !== '' ? buscar_productos($q) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buscar - Tienda Escolar</title>
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="css/comun.css">
</head>

<body>

<?php navbar(); ?>

<h1 class="page-title"><?= $q === '' ? 'Buscar comida' : 'Resultados para "' . e($q) . '"' ?></h1>

<?php if ($q === ''): ?>
    <p class="page-title" style="font-size:18px;color:#555;padding-top:15px">Escribe el nombre de un producto en el buscador.</p>
<?php elseif (!$resultados): ?>
    <p class="page-title" style="font-size:18px;color:#555;padding-top:15px">
        No encontramos productos con ese nombre. Prueba con otra palabra o revisa la
        <a href="menu.php" style="color:#8B0000">Cafeteria</a> y el <a href="almuerzos.php" style="color:#8B0000">Menu del día</a>.
    </p>
<?php else: ?>
    <section class="products-container">
        <?php foreach ($resultados as $p) {
            $p['seccion'] === 'almuerzo' ? tarjeta_almuerzo_enlace($p) : tarjeta_cafeteria($p);
        } ?>
    </section>
<?php endif; ?>

<?php render_carrito('menu'); ?>

</body>
</html>
