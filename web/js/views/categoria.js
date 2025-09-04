import {
    crear_categoria_url,
    estado_categoria_url,
    obtener_categoria_url,
    tabla_categoria_url,
} from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#lista_categoria').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_categoria_url, // 👈 tu endpoint PHP
            type: 'POST'
        },
        columns: [
            {'searchable': true, className: "text-left"},
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
    window.$('#guardar_categoria').on('click', function(event) {
        event.preventDefault();
        crearRegistrocategoria();
    });
    $(document).on('click', '[id^="estado_activar_categoria_"], [id^="estado_desactivar_categoria_"]', function () {
        const Id = $(this).data('id');
        const Estado = $(this).data('estado');
        estadocategoria(Id, Estado);
    });
    $(document).on('click', '[id^="editar_plan_"]', function(event) {
        event.preventDefault();
        const planId = $(this).data('id');
        editarCategoria(planId);
    });
});
function crearRegistrocategoria() {
    var data = new FormData(window.$('#categoria_form')[0]);
    window.$.ajax({
        type: 'POST',
        url: crear_categoria_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {
            console.log("Respuesta backend:", data);
            if(data.success){
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalcategoria'));
                modal.hide();
                alert(" creado con éxito ✅");
                window.$('#categoria_form')[0].reset();
                $('#lista_categoria').DataTable().ajax.reload();
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
function estadocategoria(id_categoria, estado) {
    $.ajax({
        url: estado_categoria_url,
        type: "POST",
        data: {'categoria': id_categoria, 'estado_categoria': estado},
        success: response => {
            $('#lista_categoria').DataTable().ajax.reload();
            if (id_categoria === 1) {
                alert("Activo", "COMPLETADO");
            } else {
                alert("Inactivo", "COMPLETADO");
            }
        }
    });
}

function editarCategoria(id_categoria) {

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: obtener_categoria_url,
        data: {idcategoria: id_categoria},
        cache: false,
        success: function (data) {
            setearIdProducto(data.id_categoria);
            cargarDatosActividad(data);
            $(':input, select').removeClass('error');

        },
        error: function (xhr, ajaxOptions, thrownError) {
        }
    });
}

function setearIdProducto(producto) {
    $('input[name="categoria_id"][type="hidden"]').val(producto);
}
function cargarDatosActividad(data) {
    $('#categoria-nom_categoria').val(data['nom_categoria']).trigger('change');
    $('#categoria-des_categoria').val(data['des_categoria']).trigger('change');
    $('#categoria-abreviatura').val(data['abreviatura']).trigger('change');

}