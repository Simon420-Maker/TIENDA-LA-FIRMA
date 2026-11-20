<?php
require_once __DIR__ . '/includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tienda Escolar</title>
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/comun.css">
</head>

<body>

<?php navbar(); ?>

<section class="slider">
    <div class="slides">
        <a href="menu.php">
            <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1600" alt="">
        </a>
        <a href="menu.php">
            <img src="img/productos/pastel-de-pollo.jpeg" alt="">
        </a>
        <a href="menu.php">
            <img src="img/productos/arepa-burguer.webp" alt="">
        </a>
        <a href="menu.php">
            <img src="img/helado.webp" alt="">
        </a>
        <a href="menu.php">
            <img src="img/productos/agua-saborizada.jpg" alt="">
        </a>
    </div>
</section>

<section class="info-container">
    <div class="info-box">
        <h2>¿Cómo funciona la página?</h2>
        <p>
            Nuestra tienda escolar virtual permite a los estudiantes consultar
            rápidamente los productos disponibles y almuerzos del día.
        </p>
    </div>

    <div class="info-box">
        <h2>¿Qué puedes encontrar?</h2>
        <p>
            En esta plataforma podrás encontrar snacks, bebidas, almuerzos,
            combos especiales y mucho más. Además, el buscador te ayudará a
            encontrar rápidamente cualquier producto disponible dentro del menú.
        </p>
    </div>
</section>

<section class="team-section">
    <h2>Programadores</h2>

    <div class="team-container">
        <div class="profile">
            <img src="img/carrasco.jpeg" alt="Samuel Gonzalez Gutierrez">
            <h3>Samuel Gonzalez Gutierrez</h3>
        </div>

        <div class="profile">
            <img src="img/sofia.jpg" alt="Sofia Vasquez Castaño">
            <h3>Sofia Vasquez Castaño</h3>
        </div>
    </div>
</section>

<footer>
    © 2026 Tienda Escolar - Proyecto Escolar
</footer>

</body>
</html>
