import {crear_categoria_url, generar_factura_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    render();

    // Escuchar cambios del carrito en el padre y eventos de storage
    if (parent && typeof parent.addEventListener === "function") {
        parent.addEventListener("cart:changed", render);
    }
    window.addEventListener("storage", function (e) {
        if (e.key === "cart_items") render();
    });

    // Botón generar factura
    document.getElementById("factura").addEventListener("click", function (event) {
        event.preventDefault();
        submitOrder();
    });

    // Botón vaciar carrito
    document.getElementById("vaciar").addEventListener("click", function () {
        event.preventDefault();
        parent.clearCart();
        render();
    });
});

// Render de la tabla
function render() {
    if (!parent || typeof parent.renderCartTable !== "function") return;
    const tbody = document.getElementById("cartBody");
    const totalEl = document.getElementById("cartTotal");
    parent.renderCartTable(tbody, totalEl);

    const details = parent.getCartDetails ? parent.getCartDetails() : { items: [] };
    const empty = !details.items || details.items.length === 0;
    document.getElementById("emptyBox").style.display = empty ? "block" : "none";
    document.getElementById("tableBox").style.display = empty ? "none" : "block";
}

// Construir payload de orden
function buildOrderPayload() {
    const details = parent && typeof parent.getCartDetails === "function"
        ? parent.getCartDetails()
        : { items: [], total: 0 };

    const items = (details.items || []).map(p => ({
        product_id: String(p.id),
        qty: Number(p.qty) || 0,
    }));

    return {
        items,
        total: Number(details.total) || 0,
        createdAt: new Date().toISOString(),
    };
}

// Enviar orden al backend
function submitOrder() {
    const payload = buildOrderPayload();
    if (!payload.items.length) {
        alert("El carrito está vacío.");
        return;
    }
    const formData = new FormData();
    formData.append("payload", JSON.stringify(payload));
    $.ajax({
        type: 'POST',
        url: generar_factura_url,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (data) {
            if (data.success) {
                alert("Orden creada correctamente");
                parent.clearCart();
                render();
                parent.document.querySelector("iframe[name='contentFrame']").src = "html/views/productos.html";
            } else {
                console.error("Respuesta backend:", data);
                alert("No se pudo crear la orden.");
            }
        },
        error: function (xhr, status, error) {
            console.error("XHR:", xhr.responseText); // 👈 para ver lo que realmente llega
            console.error("Status:", status);
            console.error("Error:", error);
            alert("Error al enviar la orden.");
        },
    });
}
