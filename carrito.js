/**
 * Carrito compartido por Cafetería, Menú del día y Buscar.
 * Se guarda en localStorage ("tiendaCart") y se envía al servidor al pulsar "Pagar".
 * El servidor vuelve a calcular precios y stock: lo que se ve aquí es solo informativo.
 *
 * Cada ítem: { key, id, name, price, stock, quantity, ingredientes }
 */
(function () {
    const STORAGE_KEY = 'tiendaCart';
    const $ = (id) => document.getElementById(id);

    const cartButton = $('cartButton'), cartPanel = $('cartPanel'), cartOverlay = $('cartOverlay');
    const cartItems = $('cartItems'), cartCount = $('cartCount'), cartTotal = $('cartTotal');
    const cartEmpty = $('cartEmpty'), cartFooter = $('cartFooter'), checkoutBtn = $('checkoutBtn');

    let cart = [];
    try {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        // Se descartan ítems del formato anterior (sin id/key)
        cart = Array.isArray(saved) ? saved.filter((i) => i && i.id && i.key) : [];
    } catch (e) { cart = []; }

    const save = () => localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    const formatPrice = (v) => new Intl.NumberFormat('es-CO', {
        style: 'currency', currency: 'COP', maximumFractionDigits: 0
    }).format(v);

    const unidadesDe = (id) => cart.filter((i) => i.id === id).reduce((s, i) => s + i.quantity, 0);

    function setOpen(open) {
        cartPanel.classList.toggle('open', open);
        cartOverlay.classList.toggle('open', open);
        cartPanel.setAttribute('aria-hidden', String(!open));
    }

    function render() {
        const items = cart.reduce((s, i) => s + i.quantity, 0);
        const total = cart.reduce((s, i) => s + i.price * i.quantity, 0);
        cartCount.textContent = items;
        cartTotal.textContent = formatPrice(total);
        cartEmpty.style.display = cart.length ? 'none' : 'block';
        cartFooter.style.display = cart.length ? 'block' : 'none';
        cartItems.replaceChildren();

        cart.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'cart-item';

            const info = document.createElement('div');
            info.className = 'cart-item-info';
            const name = document.createElement('strong');
            name.textContent = item.name;
            const price = document.createElement('span');
            price.textContent = formatPrice(item.price);
            info.append(name, price);

            const controls = document.createElement('div');
            controls.className = 'cart-item-controls';
            const minus = document.createElement('button');
            minus.type = 'button'; minus.textContent = '−';
            minus.dataset.index = index; minus.dataset.amount = '-1';
            minus.setAttribute('aria-label', 'Quitar una unidad de ' + item.name);
            const qty = document.createElement('span');
            qty.textContent = item.quantity;
            const plus = document.createElement('button');
            plus.type = 'button'; plus.textContent = '+';
            plus.dataset.index = index; plus.dataset.amount = '1';
            plus.setAttribute('aria-label', 'Agregar una unidad de ' + item.name);
            controls.append(minus, qty, plus);

            row.append(info, controls);
            cartItems.append(row);
        });
    }

    /** Agrega un ítem. Devuelve { ok, mensaje }. */
    function add(product) {
        if (unidadesDe(product.id) + 1 > product.stock) {
            return { ok: false, mensaje: 'No hay más unidades disponibles de este producto.' };
        }
        const existing = cart.find((i) => i.key === product.key);
        if (existing) {
            existing.quantity += 1;
            existing.stock = product.stock;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        save(); render(); setOpen(true);
        return { ok: true };
    }

    function changeQuantity(index, amount) {
        const item = cart[index];
        if (!item) return;
        if (amount > 0 && unidadesDe(item.id) + 1 > item.stock) {
            alert('No hay más unidades disponibles de este producto.');
            return;
        }
        item.quantity += amount;
        if (item.quantity <= 0) cart.splice(index, 1);
        save(); render();
    }

    // Tarjetas simples (Cafetería / Buscar)
    document.querySelectorAll('.product-card a.buy-btn[href="#buy"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const card = link.closest('.product-card');
            const res = add({
                key: card.dataset.id,
                id: Number(card.dataset.id),
                name: card.dataset.name,
                price: Number(card.dataset.price),
                stock: Number(card.dataset.stock),
                ingredientes: null
            });
            if (!res.ok) alert(res.mensaje);
        });
    });

    cartItems.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-index]');
        if (button) changeQuantity(Number(button.dataset.index), Number(button.dataset.amount));
    });

    cartButton.addEventListener('click', () => setOpen(true));
    $('closeCart').addEventListener('click', () => setOpen(false));
    cartOverlay.addEventListener('click', () => setOpen(false));
    $('clearCart').addEventListener('click', () => { cart = []; save(); render(); });

    checkoutBtn.addEventListener('click', async () => {
        if (cart.length === 0) return;

        if (!window.TIENDA.logueado) {
            alert('Debes iniciar sesión para pagar tu pedido.');
            window.location.href = 'login.php?next=' + encodeURIComponent(window.TIENDA.pagina);
            return;
        }

        checkoutBtn.disabled = true;
        checkoutBtn.textContent = 'Procesando...';
        try {
            const res = await fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.TIENDA.csrf },
                body: JSON.stringify({
                    items: cart.map((i) => ({ id: i.id, cantidad: i.quantity, ingredientes: i.ingredientes }))
                })
            });
            const data = await res.json();

            if (data.ok) {
                alert('Gracias por tu compra. Pedido #' + data.pedido + ' por ' + formatPrice(data.total) +
                      '. Tu pedido está en preparación.');
                cart = []; save(); render(); setOpen(false);
                window.location.reload(); // refresca el stock mostrado
            } else {
                alert(data.mensaje || 'No se pudo procesar el pedido.');
                if (data.login) window.location.href = 'login.php?next=' + encodeURIComponent(window.TIENDA.pagina);
            }
        } catch (err) {
            alert('Error de conexión con el servidor.');
        } finally {
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = 'Pagar';
        }
    });

    window.Carrito = { add, formatPrice };
    render();
})();
