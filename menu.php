<?php
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/productos.php';
require_once __DIR__ . '/includes/carrito.php';

$productos = listar_cafeteria();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menú - Tienda Escolar</title>
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="css/comun.css">
</head>

<body>

<?php navbar('menu'); ?>

<h1 class="page-title">Cafeteria</h1>

<section class="products-container">
    <?php foreach ($productos as $p) tarjeta_cafeteria($p); ?>
</section>

<?php render_carrito('menu'); ?>

</body>
</html>
