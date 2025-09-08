import {crear_usuario_url, obtener_persona_url, obtener_usuario_url, tabla_usuario_url} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#lista_usuarios').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_usuario_url, // 👈 tu endpoint PHP
            type: 'POST'
        },
        columns: [
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable': false, className: "text-left", "sortable": false}
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    }).on("draw", function () {
        $('.previous').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-left" style="margin-right: 15%;margin-top: 10%;font-size: x-large; color: black"></i>').css({
            "background": "transparent",
            "box-shadow": "none"
        });
        $('.next').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-right" style="margin-right: 50%;margin-top: 10%;font-size: x-large; color: black"></i>').css({
            "background": "transparent",
            "box-shadow": "none"
        });
    });
    window.$('#guardar_usuario').on('click', function(event) {
        event.preventDefault();
        crearRegistroCliente();
    });
    $(document).on('click', '[id^="editar_usuario_"]', function(event) {
        event.preventDefault();
        const planId = $(this).data('id');
        editarUsuario(planId);
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

            if(data.success){
                alert("usuario creado con éxito ✅");
                $('#lista_usuarios').DataTable().ajax.reload();
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalusuario'));
                modal.hide();
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
function editarUsuario(id_usuario) {

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: obtener_usuario_url,
        data: {idusuario: id_usuario},
        cache: false,
        success: function (data) {
            setearIdProducto(data.id_usuario);
            cargarDatosActividad(data);
            $(':input, select').removeClass('error');

        },
        error: function (xhr, ajaxOptions, thrownError) {
        }
    });
}

function setearIdProducto(user) {
    $('input[name="id_user"][type="hidden"]').val(user);
}
function cargarDatosActividad(data) {
    $('#usuario-nombre').val(data['nombre']).trigger('change');
    $('#usuario-correo').val(data['correo']).trigger('change');
    $('#usuario-contrasena').val(data['contrasena']).trigger('change');

}
