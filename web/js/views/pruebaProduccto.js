import {crear_producto_url, listar_categoria_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    window.$('#guardar_pdt').on('click', function(event) {
        event.preventDefault();
        console.log("Click detectado ✅");
        crearRegistroClienteInyeccion();
    });
});

function crearRegistroClienteInyeccion() {
    var data = new FormData(window.$('#form_plan_beneficio')[0]);
    console.log("Enviando AJAX a:", crear_producto_url);

    window.$.ajax({
        type: 'POST',
        url: crear_producto_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            console.log("Respuesta backend:", data);
            if(data.success){
                alert("Producto creado con éxito ✅");
                window.$('#form_plan_beneficio')[0].reset();
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error en la petición:", xhr.responseText);
            alert("Ocurrió un error al guardar el producto ❌");
        }
    });
}
$.post(
    listar_categoria_url,

    data => {



        $.each(data, function (key, value) {
            $("#categoria").append("<option value='" + value.id_categoria + "'>" + value.nom_categoria + "</option>");
        });

    }
).fail((xhr, status, error) => {
    unBlockUI();
    console.error(error);
    toastr['error'](obtenerCadena("Error consultar las listas"));
});