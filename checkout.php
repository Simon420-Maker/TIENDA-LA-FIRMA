<?php
/**
 * Recibe el carrito (JSON), lo valida contra la base de datos y registra el pedido.
 *
 *  - Los precios SIEMPRE salen de la base de datos, nunca del navegador.
 *  - En los almuerzos, cada ingrediente quitado descuenta DESCUENTO_INGREDIENTE.
 *  - El stock se descuenta de forma atómica; si no alcanza, se cancela todo el pedido.
 */
define('API_REQUEST', true);
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

function responder(int $codigo, array $datos): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
}

$usuario = usuario_actual();
if (!$usuario) {
    responder(401, ['ok' => false, 'login' => true, 'mensaje' => 'Debes iniciar sesión para pagar.']);
}
if (empty($usuario['id_cliente'])) {
    responder(409, ['ok' => false, 'mensaje' => 'Tu cuenta no está ligada a un cliente. Contacta al administrador.']);
}
if (!csrf_valido($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    responder(403, ['ok' => false, 'mensaje' => 'Sesión inválida. Recarga la página e inténtalo de nuevo.']);
}

// ---- 1. Leer y validar la entrada ----
$entrada = json_decode(file_get_contents('php://input'), true);
$items   = is_array($entrada['items'] ?? null) ? $entrada['items'] : [];

if (!$items || count($items) > 60) {
    responder(422, ['ok' => false, 'mensaje' => 'Tu carrito está vacío o es demasiado grande.']);
}

$lineas = [];
foreach ($items as $it) {
    $id       = filter_var($it['id'] ?? null, FILTER_VALIDATE_INT);
    $cantidad = filter_var($it['cantidad'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || !$cantidad || $cantidad < 1 || $cantidad > 100) {
        responder(422, ['ok' => false, 'mensaje' => 'El carrito contiene datos inválidos.']);
    }

    $ing = $it['ingredientes'] ?? null;
    if ($ing !== null) {
        if (!is_array($ing) || count($ing) > 30) {
            responder(422, ['ok' => false, 'mensaje' => 'Ingredientes inválidos.']);
        }
        $ing = array_values(array_unique(array_filter($ing, fn ($x) => is_string($x) && $x !== '' && mb_strlen($x) <= 80)));
        sort($ing);
    }
    $lineas[] = ['id' => $id, 'cantidad' => $cantidad, 'ingredientes' => $ing];
}

// ---- 2. Registrar el pedido ----
try {
    $pdo = db();
    $pdo->beginTransaction();

    $buscarProducto = $pdo->prepare(
        'SELECT Nombre, Precio, Seccion FROM productos WHERE Id_Producto = ? AND Activo = 1 FOR UPDATE'
    );
    $buscarIngredientes = $pdo->prepare('SELECT Ingrediente FROM plato_ingredientes WHERE Id_Producto = ?');
    $descontarStock = $pdo->prepare(
        'UPDATE productos SET Stock = Stock - ? WHERE Id_Producto = ? AND Stock >= ?'
    );

    $pdo->prepare('INSERT INTO pedidos (Id_Cliente) VALUES (?)')->execute([$usuario['id_cliente']]);
    $idPedido = (int) $pdo->lastInsertId();

    $insertarVenta = $pdo->prepare(
        'INSERT INTO ventas (Id_Cliente, Id_Producto, Cantidad, Id_Pedido, Precio_Unitario, Personalizacion)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    $total = 0;
    foreach ($lineas as $l) {
        $buscarProducto->execute([$l['id']]);
        $p = $buscarProducto->fetch();
        if (!$p) {
            throw new DomainException('Uno de los productos ya no está disponible.');
        }

        $precio = (int) $p['Precio'];
        $personalizacion = null;

        if ($p['Seccion'] === 'almuerzo') {
            $buscarIngredientes->execute([$l['id']]);
            $incluidos = $buscarIngredientes->fetchAll(PDO::FETCH_COLUMN);

            $elegidos = $l['ingredientes'] ?? $incluidos;         // sin lista = plato completo
            if ($incluidos && (!$elegidos || array_diff($elegidos, $incluidos))) {
                throw new DomainException('Los ingredientes elegidos para "' . $p['Nombre'] . '" no son válidos.');
            }
            $quitados = count($incluidos) - count($elegidos);
            $precio   = max(0, $precio - $quitados * DESCUENTO_INGREDIENTE);
            $personalizacion = $elegidos ? implode(', ', $elegidos) : null;
        }

        $descontarStock->execute([$l['cantidad'], $l['id'], $l['cantidad']]);
        if ($descontarStock->rowCount() === 0) {
            throw new DomainException('No hay suficiente stock de "' . $p['Nombre'] . '".');
        }

        $insertarVenta->execute([$usuario['id_cliente'], $l['id'], $l['cantidad'], $idPedido, $precio, $personalizacion]);
        $total += $precio * $l['cantidad'];
    }

    $pdo->prepare('UPDATE pedidos SET Total = ? WHERE Id_Pedido = ?')->execute([$total, $idPedido]);
    $pdo->commit();

    responder(200, ['ok' => true, 'pedido' => $idPedido, 'total' => $total]);
} catch (DomainException $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    responder(409, ['ok' => false, 'mensaje' => $e->getMessage()]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log('checkout: ' . $e->getMessage());
    responder(500, ['ok' => false, 'mensaje' => 'No se pudo procesar el pedido. Inténtalo más tarde.']);
}
