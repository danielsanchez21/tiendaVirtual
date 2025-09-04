import {
    crear_producto_url,
    crear_rol_url,
    estado_producto_url,
    estado_rol_url, obtener_producto_url, Obtener_rol_url,
    tabla_producto_url,
    tabla_rol_url
} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#lista_rol').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_rol_url, // 👈 tu endpoint PHP
            type: 'POST'
        },
        columns: [
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


    window.$('#guardar_rol').on('click', function(event) {
        event.preventDefault();
        crearRegistrorol();
    });

    $(document).on('click', '[id^="estado_activar_rol_"], [id^="estado_desactivar_rol_"]', function () {
        const Id = $(this).data('id');
        const Estado = $(this).data('estado');
        estadoRol(Id, Estado);
    });
    $(document).on('click', '[id^="editar_rol_"]', function(event) {
        event.preventDefault();
        const id = $(this).data('id');
        editarRol(id);
    });
});
function crearRegistrorol() {
    var data = new FormData(window.$('#form_rol')[0]);
    window.$.ajax({
        type: 'POST',
        url: crear_rol_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            if(data.success){
                alert("rol creado con éxito ✅");
                window.$('#form_rol')[0].reset();
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalRol'));
                modal.hide();
                $('#lista_rol').DataTable().ajax.reload();
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

function estadoRol(id_rol, estado) {
    $.ajax({
        url: estado_rol_url,
        type: "POST",
        data: {'rol': id_rol, 'estado_rol': estado},
        success: response => {
            $('#lista_rol').DataTable().ajax.reload();
            if (id_rol === 1) {
                alert("Activo", "COMPLETADO");
            } else {
                alert("Inactivo", "COMPLETADO");
            }
        }
    });
}

function editarRol(id_rol) {

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: Obtener_rol_url,
        data: {idrol: id_rol},
        cache: false,
        success: function (data) {
            setearIdProducto(data.id_rol);
            cargarDatosActividad(data);
            $(':input, select').removeClass('error');

        },
        error: function (xhr, ajaxOptions, thrownError) {
        }
    });
}

function setearIdProducto(rol) {
    $('input[name="rol_id"][type="hidden"]').val(rol);
}
function cargarDatosActividad(data) {
    $('#rol_nombre').val(data['nombre']).trigger('change');

}