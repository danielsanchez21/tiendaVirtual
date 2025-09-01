import {crear_persona_url, listar_documento_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    window.$('#guardar_persona').on('click', function(event) {
        event.preventDefault();
        crearRegistroCliente();
    });
    obtenerDocumento();
});

function crearRegistroCliente() {
    var data = new FormData(window.$('#persona_form')[0]);


    window.$.ajax({
        type: 'POST',
        url: crear_persona_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            console.log("Respuesta backend:", data);
            if(data.success){
                alert("Persona creado con éxito ✅");
                window.$('#persona_form')[0].reset();
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
function obtenerDocumento(){
    $.post(
        listar_documento_url,
        data => {
            $.each(data, function (key, value) {
                $("#tipo_documento").append("<option value='" + value.id_documento + "'>" + value.nombre + "</option>");
            });
        }
    ).fail((xhr, status, error) => {
        console.error(error);
        alert("Error consultar las listas");
    });
}