import { crear_usuario_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    window.$('#guardar_usuario').on('click', function(event) {
        event.preventDefault();
        crearRegistroCliente();
    });
});

function crearRegistroCliente() {
    var data = new FormData(window.$('#userform')[0]);
    window.$.ajax({
        type: 'POST',
        url: crear_usuario_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            console.log("Respuesta backend:", data);
            if(data.success){
                alert("usuario creado con éxito ✅");
                window.$('#usuario_form')[0].reset();
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error en la peticion:", xhr.responseText);
            alert("Ocurrió un error al guardar el usuario ❌");
        }
    });
}
