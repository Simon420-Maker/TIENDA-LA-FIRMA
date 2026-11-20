<?php
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/productos.php';
require_once __DIR__ . '/includes/carrito.php';

$dias = listar_almuerzos_por_dia();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu del dia - Tienda Escolar</title>
<link rel="stylesheet" href="css/almuerzos.css">
<link rel="stylesheet" href="css/comun.css">
</head>

<body>

<?php navbar('almuerzos'); ?>

<main>
    <section class="menu-intro">
        <h1>Menú de almuerzos</h1>
        <time class="today-date" id="menuDate"></time>
    </section>

    <section class="products-container" id="productsContainer" aria-label="Platos disponibles">
        <?php foreach ($dias as $dia => $platos): ?>
            <section class="day-menu" aria-labelledby="dia-<?= e(strtolower(strtr($dia, ['é' => 'e']))) ?>">
                <h2 class="day-heading" id="dia-<?= e(strtolower(strtr($dia, ['é' => 'e']))) ?>"><?= e($dia) ?></h2>
                <div class="day-products">
                    <?php foreach ($platos as $p) tarjeta_almuerzo($p); ?>
                </div>
            </section>
        <?php endforeach; ?>
    </section>
</main>

<?php render_carrito('almuerzos'); ?>
<script src="js/almuerzos.js"></script>

</body>
</html>
