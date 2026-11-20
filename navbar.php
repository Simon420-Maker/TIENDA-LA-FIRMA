<?php
/**
 * Barra de navegación común a todas las páginas.
 * $activa: 'menu' | 'almuerzos' | 'pedidos' | ''
 */
require_once __DIR__ . '/auth.php';

function navbar(string $activa = ''): void
{
    $u = usuario_actual();
    $actual = fn (string $p) => $activa === $p ? ' aria-current="page"' : '';
    ?>
<header>
    <div class="navbar">
        <a href="index.php" class="logo">
            <img src="img/logo.png" alt="Tienda la firma">
        </a>

        <nav class="menu" aria-label="Navegación principal">
            <a href="menu.php"<?= $actual('menu') ?>>Cafeteria</a>
            <a href="almuerzos.php"<?= $actual('almuerzos') ?>>Menu del día</a>
            <?php if ($u): ?>
                <a href="pedidos.php"<?= $actual('pedidos') ?>>Mis pedidos</a>
            <?php endif; ?>
        </nav>

        <form class="search-box" action="buscar.php" method="get" role="search">
            <input type="search" name="q" placeholder="Buscar comida..." aria-label="Buscar comida"
                   maxlength="60" value="<?= e(basename($_SERVER['SCRIPT_NAME']) === 'buscar.php' ? ($_GET['q'] ?? '') : '') ?>">
        </form>

        <?php if ($u): ?>
            <div class="user-box">
                <span class="user-name">Hola, <?= e($u['usuario']) ?></span>
                <a href="logout.php" class="login-btn">Salir</a>
            </div>
        <?php else: ?>
            <a href="login.php" class="login-btn">Login</a>
        <?php endif; ?>
    </div>
</header>
<?php
}
