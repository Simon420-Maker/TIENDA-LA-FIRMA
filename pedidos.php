<?php
require_once __DIR__ . '/includes/navbar.php';

$u = usuario_actual();
if (!$u) {
    header('Location: login.php?next=pedidos.php');
    exit;
}

$pedidos = [];
if ($u['id_cliente']) {
    $st = db()->prepare(
        'SELECT p.Id_Pedido, p.Fecha, p.Total, p.Estado,
                pr.Nombre, v.Cantidad, v.Precio_Unitario, v.Personalizacion
           FROM pedidos p
           JOIN ventas v     ON v.Id_Pedido = p.Id_Pedido
           JOIN productos pr ON pr.Id_Producto = v.Id_Producto
          WHERE p.Id_Cliente = ?
          ORDER BY p.Id_Pedido DESC, v.Id_Venta'
    );
    $st->execute([$u['id_cliente']]);
    foreach ($st as $f) {
        $id = (int) $f['Id_Pedido'];
        $pedidos[$id]['info'] = ['fecha' => $f['Fecha'], 'total' => $f['Total'], 'estado' => $f['Estado']];
        $pedidos[$id]['lineas'][] = $f;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis pedidos - Tienda Escolar</title>
<link rel="stylesheet" href="css/menu.css">
<link rel="stylesheet" href="css/comun.css">
<link rel="stylesheet" href="css/pedidos.css">
</head>

<body>

<?php navbar('pedidos'); ?>

<h1 class="page-title">Mis pedidos</h1>

<section class="pedidos-lista">
    <?php if (!$pedidos): ?>
        <p class="pedidos-vacio">Todavía no has hecho pedidos. Mira la <a href="menu.php">Cafeteria</a>
        o el <a href="almuerzos.php">Menu del día</a>.</p>
    <?php endif; ?>

    <?php foreach ($pedidos as $id => $ped): ?>
        <article class="pedido">
            <div class="pedido-cabecera">
                <div>
                    <strong>Pedido #<?= $id ?></strong>
                    <span class="pedido-fecha"><?= e(date('d/m/Y g:i a', strtotime($ped['info']['fecha']))) ?></span>
                </div>
                <span class="pedido-estado estado-<?= e($ped['info']['estado']) ?>"><?= e(ucfirst($ped['info']['estado'])) ?></span>
            </div>

            <ul class="pedido-lineas">
                <?php foreach ($ped['lineas'] as $l): ?>
                    <li>
                        <span>
                            <?= (int) $l['Cantidad'] ?> × <?= e($l['Nombre']) ?>
                            <?php if ($l['Personalizacion']): ?>
                                <small>(<?= e($l['Personalizacion']) ?>)</small>
                            <?php endif; ?>
                        </span>
                        <span><?= formato_precio($l['Precio_Unitario'] * $l['Cantidad']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="pedido-total">
                <span>Total</span>
                <strong><?= formato_precio($ped['info']['total']) ?></strong>
            </div>
        </article>
    <?php endforeach; ?>
</section>

</body>
</html>
