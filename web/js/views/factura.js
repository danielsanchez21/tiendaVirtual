import {crear_factura_url, listar_persona_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    window.$('#guardar_factura').on('click', function (event) {
        event.preventDefault();
        crearRegistrofactura();
    });
    listarcliente();
});

function crearRegistrofactura() {
    var data = new FormData(window.$('#docform')[0]);


    window.$.ajax({
        type: 'POST',
        url: crear_factura_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            console.log("Respuesta backend:", data);
            if (data.success) {
                alert("Persona creado con éxito ✅");
                window.$('#docform')[0].reset();
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error en la peticion:", xhr.responseText);
            alert("Ocurrió un error al guardar la persona ❌");
        }
    });
}

/**
 * permite obtener todos los documentos de la db
 */



function listarcliente() {
    $.post(
        listar_persona_url,
        data => {
            $.each(data, function (key, value) {
                $("#factura-fk_persona").append("<option value='" + value.id_persona + "'>" + value.nombre + ' '+value.apellido+ "</option>");
            });

        }
    ).fail((xhr, status, error) => {
        unBlockUI();
        console.error(error);
        toastr['error'](obtenerCadena("Error consultar las listas"));
    });
}