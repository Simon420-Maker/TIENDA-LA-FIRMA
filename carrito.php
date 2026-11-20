<?php
/**
 * Panel del carrito + scripts. Se incluye al final de menu, almuerzos y buscar.
 * $variante: 'menu' (Carrito) | 'almuerzos' (Mi pedido)
 */
require_once __DIR__ . '/auth.php';

function render_carrito(string $variante = 'menu'): void
{
    $esAlmuerzos = $variante === 'almuerzos';
    $titulo = $esAlmuerzos ? 'Mi pedido' : 'Carrito';
    $vacio  = $esAlmuerzos ? 'Aún no agregas platos.' : 'Tu carrito está vacío.';
    ?>
<button class="cart-button" id="cartButton" type="button" aria-label="Abrir <?= e(strtolower($titulo)) ?>">
    <span aria-hidden="true">🛒</span>
    <span><?= e($titulo) ?></span>
    <span class="cart-count" id="cartCount">0</span>
</button>

<div class="cart-overlay" id="cartOverlay"></div>

<aside class="cart-panel" id="cartPanel" aria-label="<?= e($titulo) ?>">
    <div class="cart-header">
        <h2><?= e($titulo) ?></h2>
        <button type="button" class="cart-close" id="closeCart" aria-label="Cerrar">×</button>
    </div>

    <div class="cart-items" id="cartItems"></div>
    <div class="cart-empty" id="cartEmpty"><?= e($vacio) ?></div>

    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-total">
            <span>Total:</span>
            <strong id="cartTotal">$0</strong>
        </div>
        <div class="cart-actions">
            <button type="button" class="clear-btn" id="clearCart">Vaciar</button>
            <button type="button" class="checkout-btn" id="checkoutBtn">Pagar</button>
        </div>
    </div>
</aside>

<script>
    window.TIENDA = {
        csrf: <?= json_encode(csrf_token()) ?>,
        logueado: <?= usuario_actual() ? 'true' : 'false' ?>,
        pagina: <?= json_encode(basename($_SERVER['SCRIPT_NAME'])) ?>
    };
</script>
<script src="js/carrito.js"></script>
<?php
}
