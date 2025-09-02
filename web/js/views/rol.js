import { crear_rol_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    window.$('#guardar_rol').on('click', function(event) {
        event.preventDefault();
        crearRegistrorol();
    });
});
function crearRegistrorol() {
    var data = new FormData(window.$('#rol_form')[0]);
    window.$.ajax({
        type: 'POST',
        url: crear_rol_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            console.log("Respuesta backend:", data);
            if(data.success){
                alert("rol creado con éxito ✅");
                window.$('#rol_form')[0].reset();
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error en la peticion:", xhr.responseText);
            alert("Ocurrió un error al guardar el rol ❌");
        }
    });
}

