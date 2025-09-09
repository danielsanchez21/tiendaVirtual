
import {
    crear_documento_url, estado_documento_url,
    listar_documento_url, obtener_documento_url,
    tabla_documento_url,
} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#lista_documento').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_documento_url, // 👈 tu endpoint PHP
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
    window.$('#guardar_doc').on('click', function(event) {
        event.preventDefault();
        crearRegistroDocumento();
    });

    $(document).on('click', '[id^="estado_desactivar_doc_"], [id^="estado_activar_doc_"]', function () {
        const Id = $(this).data('id');
        const Estado = $(this).data('estado');
        estadoDocumento(Id, Estado);
    });

    $(document).on('click', '[id^="editar_plan_"]', function(event) {
        event.preventDefault();
        const planId = $(this).data('id');
        editarDocumento(planId);
    });

    const modaldocumento = document.getElementById('modaldocumento');
    const form_doc = document.getElementById('form_doc');

    modaldocumento.addEventListener('hidden.bs.modal', function () {
        // Limpia todos los campos del formulario
        form_doc.reset();

        // Si usas validaciones de Bootstrap, también quitas estilos
        form_doc.querySelectorAll('.is-valid, .is-invalid')
            .forEach(el => el.classList.remove('is-valid', 'is-invalid'));
    });
});

function crearRegistroDocumento() {
    var data = new FormData(window.$('#form_doc')[0]);


    window.$.ajax({
        type: 'POST',
        url: crear_documento_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            if(data.success){
                alert("Documento creado con éxito ✅");
                window.$('#form_doc')[0].reset();
                let modal = bootstrap.Modal.getInstance(document.getElementById('modaldocumento'));
                modal.hide();
                $('#lista_documento').DataTable().ajax.reload();
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

function estadoDocumento(id_documento, estado) {
    $.ajax({
        url: estado_documento_url,
        type: "POST",
        data: {'documento_id': id_documento, 'estado_documento': estado},
        success: response => {
            $('#lista_documento').DataTable().ajax.reload();
            if (id_documento === 1) {
                alert("Activo", "COMPLETADO");
            } else {
                alert("Inactivo", "COMPLETADO");
            }
        }
    });
}
function editarDocumento(id_documento) {

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: obtener_documento_url,
        data: {id_documento: id_documento},
        cache: false,
        success: function (data) {
            setearIdDocumento(data.id_documento);
            cargarDatosActividad(data);
            $('#btn_guardar_plan-act').hide();
            $('#btn_actualizar_plan-act').show();
            $('#modaldocumento').modal('show');

        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error al obtener documento:", xhr.responseText);
        }
    });
}

function setearIdDocumento(documento) {
    $('input[name="documento_id"][type="hidden"]').val(documento);
}
function cargarDatosActividad(data) {
    $('#nombre_documento').val(data['nombre']).trigger('change');
    $('#abreviatura').val(data['abreviatura']).trigger('change');
}
