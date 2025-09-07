import {listar_producto_url} from "../const.js";
import {unBlockUI} from "../commons.js";


document.addEventListener("DOMContentLoaded", () => {
    obtenerProducto();



});

function obtenerProducto() {
    $.post(
        listar_producto_url,
        data => {
            console.log(data);

            const grid = document.getElementById("grid");
            grid.innerHTML = ""; // limpiar productos anteriores

            data.forEach(prod => {
                // usar imagen si existe, si no, poner placeholder
                const img = prod.imagen
                    ? prod.imagen
                    : `/uploads/productos/68bdaa46744e3_Captura de pantalla 2024-03-14 113007.png`;

                // plantilla de tarjeta
                const card = `
                    <div class="card">
                        <img src="${img}" alt="${prod.nombre}" width="15px" height="20px">
                        <div class="body">
                            <div>${prod.nombre}</div>
                            <div class="price">$${prod.precio_venta.toLocaleString()}</div>
                            <div class="actions">
                                <button class="btn" 
                                    onclick="addToCart({
                                        id:${prod.id_Producto},
                                        name:'${prod.nombre}',
                                        price:${prod.precio_venta}
                                    });">
                                    Agregar al carrito
                                </button>

                            </div>
                        </div>
                    </div>
                `;

                grid.insertAdjacentHTML("beforeend", card);
            });
        }
    ).fail((xhr, status, error) => {
        unBlockUI();
        console.error(error);
    });
}
