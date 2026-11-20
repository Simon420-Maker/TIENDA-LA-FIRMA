/**
 * Menú del día: fecha de hoy y "Agregar al pedido" con ingredientes personalizados.
 * Cada ingrediente que se quita descuenta $500 (el servidor lo vuelve a calcular).
 */
document.addEventListener('DOMContentLoaded', () => {
    const fecha = document.getElementById('menuDate');
    if (fecha) {
        fecha.textContent = new Intl.DateTimeFormat('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long'
        }).format(new Date());
    }

    const contenedor = document.getElementById('productsContainer');
    if (!contenedor) return;

    contenedor.addEventListener('click', (event) => {
        const boton = event.target.closest('.buy-btn');
        if (!boton || boton.disabled) return;

        const card = boton.closest('.product-card');
        const feedback = card.querySelector('.selection-feedback');
        const todos = card.querySelectorAll('.ingredient-picker input');
        const elegidos = Array.from(card.querySelectorAll('.ingredient-picker input:checked'), (i) => i.value);

        if (todos.length > 0 && elegidos.length === 0) {
            feedback.textContent = 'Selecciona al menos un ingrediente para agregar este plato.';
            feedback.className = 'selection-feedback error';
            return;
        }

        const id = Number(card.dataset.id);
        const base = Number(card.dataset.price);
        const descuento = Number(card.dataset.descuento);
        const quitados = todos.length - elegidos.length;
        const precio = Math.max(0, base - quitados * descuento);
        const clave = id + '|' + [...elegidos].sort().join(',');

        const res = window.Carrito.add({
            key: clave,
            id,
            name: card.dataset.name + (elegidos.length ? ' (' + elegidos.join(', ') + ')' : ''),
            price: precio,
            stock: Number(card.dataset.stock),
            ingredientes: elegidos
        });

        if (res.ok) {
            feedback.textContent = 'Plato personalizado agregado por ' + window.Carrito.formatPrice(precio) + '.';
            feedback.className = 'selection-feedback success';
        } else {
            feedback.textContent = res.mensaje;
            feedback.className = 'selection-feedback error';
        }
    });
});
