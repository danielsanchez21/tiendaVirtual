import {
    crear_usuariorol_url, listar_usuario_url,
     Obtener_rol_url,
    obtener_usuariorol_url,
    tabla_usuariorol_url
} from "../const.js";
import {unBlockUI} from "../commons.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#lista_usuariorol').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_usuariorol_url,
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

    window.$('#guardar_usuariorol').on('click', function (event) {
        event.preventDefault();
        crearRegistroUsuarioRol();
    });

    obtenerusuario();
    obtenerrol();

    $(document).on('click', '[id^="editar_usuario_"]', function (event) {
        event.preventDefault();
        const planId = $(this).data('id');
        editarUsuarioRol(planId);
    });


    const modalusuariorol = document.getElementById('modalusuariorol');
    const usuariorol_form = document.getElementById('usuariorol_form');

    modalusuariorol.addEventListener('hidden.bs.modal', function () {
        usuariorol_form.reset();
        usuariorol_form.querySelectorAll('.is-valid, .is-invalid')
            .forEach(el => el.classList.remove('is-valid', 'is-invalid'));
    });
});

function crearRegistroUsuarioRol() {
    var data = new FormData(window.$('#usuariorol_form')[0]);
    window.$.ajax({
        type: 'POST',
        url: crear_usuariorol_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        processData: false,
        success: function (data) {
            if (data.success) {
                alert("Asignación guardada ");
                window.$('#usuariorol_form')[0].reset();
                $('#lista_usuariorol').DataTable().ajax.reload();
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalusuariorol'));
                modal.hide()
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error en la petición:", xhr.responseText);
            alert("Ocurrió un error al guardar el usuario ❌");
        }
    });
}

function obtenerusuario() {
    $.post(
        listar_usuario_url,
        data => {
            $.each(data, function (key, value) {
                $("#id_usuario").append("<option value='" + value.id_usuario + "'>" + value.nombre + "</option>");
            });
        }
    ).fail((xhr, status, error) => {
        unBlockUI();
        console.error(error);
        toastr['error'](obtenerCadena("Error consultar las listas"));
    });
}

function obtenerrol() {
    $.post(
        Obtener_rol_url,
        data => {
            $.each(data, function (key, value) {
                $("#id_rol").append("<option value='" + value.id_rol + "'>" + value.nombre + "</option>");
            });
        }
    ).fail((xhr, status, error) => {
        unBlockUI();
        console.error(error);
        toastr['error'](obtenerCadena("Error consultar las listas"));
    });
}

function editarUsuarioRol(id_user_rol) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: obtener_usuariorol_url,
        data: {id_user_rol: id_user_rol},
        cache: false,
        success: function (data) {
            setearIdusuarioRol(data.id_user_rol);
            cargarDatosActividad(data);

        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error obtener usuario_rol:", xhr.responseText);
        }
    });
}

function setearIdusuarioRol(usuario_rol) {
    $('input[name="id_user_rol"][type="hidden"]').val(usuario_rol);

}

function cargarDatosActividad(data) {
    $('#id_usuario').val(data['id_usuario']).trigger('change');
    $('#id_rol').val(data['id_rol']).trigger('change');
}