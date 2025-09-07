(function () {
    var CART_KEY = 'cart_items';

    function getCart() {
        try {
            var raw = localStorage.getItem(CART_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            return {};
        }
    }

    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        dispatchCartChange();
    }

    function dispatchCartChange() {
        try {
            var event = new Event('cart:changed');
            window.dispatchEvent(event);
        } catch (e) {
            // IE fallback no necesario aquí
        }
    }

    function addToCart(product) {

        if (!product || !product.id) return;
        var cart = getCart();
        var id = String(product.id);
        if (!cart[id]) {
            cart[id] = { id: id, name: product.name || 'Producto', price: Number(product.price) || 0, qty: 0 };
        }
        cart[id].qty += 1;
        saveCart(cart);
        return cart[id];
    }

    function removeFromCart(id) {
        var cart = getCart();
        id = String(id);
        if (cart[id]) {
            delete cart[id];
            saveCart(cart);
        }
    }

    function clearCart() {
        localStorage.removeItem(CART_KEY);
        dispatchCartChange();
    }

    function getCartCount() {
        var cart = getCart();
        var total = 0;
        Object.keys(cart).forEach(function (k) {
            total += Number(cart[k].qty || 0);
        });
        return total;
    }

    function getCartDetails() {
        var cart = getCart();
        var items = [];
        var total = 0;
        Object.keys(cart).forEach(function (k) {
            var p = cart[k];
            var sub = Number(p.price) * Number(p.qty);
            total += sub;
            items.push({ id: p.id, name: p.name, price: Number(p.price), qty: Number(p.qty), subtotal: sub });
        });
        return { items: items, total: total };
    }

    var badgeElId = null;

    function updateCartBadge() {
        if (!badgeElId) return;
        var el = document.getElementById(badgeElId);
        if (el) el.textContent = String(getCartCount());
    }

    function initCartBadge(elementId) {
        badgeElId = elementId;
        updateCartBadge();
        window.addEventListener('cart:changed', updateCartBadge);
        window.addEventListener('storage', function (e) { if (e.key === CART_KEY) updateCartBadge(); });
    }

    function renderCartTable(tbodyOrId, totalOrId) {
        var tbody = typeof tbodyOrId === 'string' ? document.getElementById(tbodyOrId) : tbodyOrId;
        var totalEl = typeof totalOrId === 'string' ? document.getElementById(totalOrId) : totalOrId;
        if (!tbody) return;
        var data = getCartDetails();
        tbody.innerHTML = '';
        data.items.forEach(function (p) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(p.name) + '</td>' +
                '<td class="text-end">$' + p.price.toFixed(2) + '</td>' +
                '<td class="text-center">' + p.qty + '</td>' +
                '<td class="text-end">$' + p.subtotal.toFixed(2) + '</td>' +
                '<td class="text-end"><button data-id="' + p.id + '" class="btn-remove">Eliminar</button></td>';
            tbody.appendChild(tr);
        });
        if (totalEl) totalEl.textContent = '$' + data.total.toFixed(2);

        // bind remove
        tbody.querySelectorAll('.btn-remove').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                removeFromCart(id);
                renderCartTable(tbody, totalEl);
            });
        });
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];
        });
    }

    // Exponer en window para que las vistas del iframe llamen con parent.*
    window.addToCart = addToCart;
    window.removeFromCart = removeFromCart;
    window.clearCart = clearCart;
    window.getCartCount = getCartCount;
    window.getCartDetails = getCartDetails;
    window.initCartBadge = initCartBadge;
    window.renderCartTable = renderCartTable;
})();
