import {
    crear_persona_url,
    listar_documento_url, obtener_persona_url,
    obtener_producto_url,
    tabla_persona_url,
    tabla_producto_url
} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#lista_persona').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_persona_url,
            type: 'POST'
        },
        columns: [
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable': true, className: "text-left"},
            {'searchable':false, className: "text-left","sortable": false}
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    }).on("draw", function () {
        $('.previous').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-left" style="margin-right: 15%;margin-top: 10%;font-size: x-large; color: black"></i>').css({"background":"transparent", "box-shadow":"none"});
        $('.next').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-right" style="margin-right: 50%;margin-top: 10%;font-size: x-large; color: black"></i>').css({"background":"transparent", "box-shadow":"none"});
    });
    window.$('#guardar_persona').on('click', function(event) {
        event.preventDefault();
        crearRegistroCliente();
    });
    obtenerDocumento();
    $(document).on('click', '[id^="editar_persona_"]', function(event) {
        event.preventDefault();
        const planId = $(this).data('id');
        editarProducto(planId);
    });
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
                alert("Persona creado con éxito ");
                $('#lista_persona').DataTable().ajax.reload();
                window.$('#persona_form')[0].reset();
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalclientes'));
                modal.hide();
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
function editarProducto(id_persona) {

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: obtener_persona_url,
        data: {idpersona: id_persona},
        cache: false,
        success: function (data) {
            setearIdProducto(data.id_persona);
            cargarDatosActividad(data);
            $(':input, select').removeClass('error');

        },
        error: function (xhr, ajaxOptions, thrownError) {
        }
    });
}

function setearIdProducto(producto) {
    $('input[name="id_persona"][type="hidden"]').val(producto);
}
function cargarDatosActividad(data) {
    $('#persona-nombre').val(data['nombre']).trigger('change');
    $('#persona-apellido').val(data['apellido']).trigger('change');
    $('#tipo_documento').val(data['id_documento']).trigger('change');
    $('#numero_documento').val(data['num_documento']).trigger('change');
    $('#persona-telefono').val(data['telefono']).trigger('change');
    $('#persona-direccion').val(data['direccion']).trigger('change');
    $('#persona-genero').val(data['genero']).trigger('change');
}