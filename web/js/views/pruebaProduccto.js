import {
    crear_producto_url,
    estado_producto_url,
    listar_categoria_url,
    obtener_producto_url,
    tabla_producto_url
} from "../const.js";


document.addEventListener("DOMContentLoaded", () => {
    $('#lista_productos').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: tabla_producto_url, // 👈 tu endpoint PHP
            type: 'POST'
        },
        columns: [
            {'searchable': true, className: "text-left"},
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
    window.$('#guardar_pdt').on('click', function (event) {
        event.preventDefault();
        crearRegistroClienteInyeccion();
    });
    obtenerCategoria();

    $(document).on('click', '[id^="estado_activar_pdt_"], [id^="estado_desactivar_pdt_"]', function () {
        const Id = $(this).data('id');
        const Estado = $(this).data('estado');
        estadoProducto(Id, Estado);
    });

    $(document).on('click', '[id^="editar_plan_"]', function(event) {
        event.preventDefault();
        const planId = $(this).data('id');
        editarProducto(planId);
    });

    const modalProducto = document.getElementById('modalProducto');
    const formProducto = document.getElementById('form_producto');

    modalProducto.addEventListener('hidden.bs.modal', function () {
        // Limpia todos los campos del formulario
        formProducto.reset();

        // Si usas validaciones de Bootstrap, también quitas estilos
        formProducto.querySelectorAll('.is-valid, .is-invalid')
            .forEach(el => el.classList.remove('is-valid', 'is-invalid'));
    });
});

function crearRegistroClienteInyeccion() {
    var data = new FormData(window.$('#form_producto')[0]);


    window.$.ajax({
        type: 'POST',
        url: crear_producto_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {

            if (data.success) {
                alert("Producto creado con éxito.");
                window.$('#form_producto')[0].reset();
                $('#lista_productos').DataTable().ajax.reload();
                let modal = bootstrap.Modal.getInstance(document.getElementById('modalProducto'));
                modal.hide();
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error("Error en la petición:", xhr.responseText);
            alert("Ocurrió un error al guardar el producto ❌");
        }
    });
}

function obtenerCategoria() {
    $.post(
        listar_categoria_url,
        data => {
            $.each(data, function (key, value) {
                $("#categoria").append("<option value='" + value.id_categoria + "'>" + value.nom_categoria + "</option>");
            });
        }
    ).fail((xhr, status, error) => {
        unBlockUI();
        console.error(error);
        toastr['error'](obtenerCadena("Error consultar las listas"));
    });
}

function estadoProducto(id_producto, estado) {
    $.ajax({
        url: estado_producto_url,
        type: "POST",
        data: {'producto': id_producto, 'estado_producto': estado},
        success: response => {
            $('#lista_productos').DataTable().ajax.reload();
            if (id_producto === 1) {
                alert("Activo", "COMPLETADO");
            } else {
                alert("Inactivo", "COMPLETADO");
            }
        }
    });
}
    function editarProducto(id_producto) {

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: obtener_producto_url,
            data: {idproducto: id_producto},
            cache: false,
            success: function (data) {
                setearIdProducto(data.id_Producto);
                cargarDatosActividad(data);
                $('#btn_guardar_plan-act').hide();
                $('#btn_actualizar_plan-act').show();
                $(':input, select').removeClass('error');

            },
            error: function (xhr, ajaxOptions, thrownError) {
            }
        });
    }

function setearIdProducto(producto) {
    $('input[name="pdt_id"][type="hidden"]').val(producto);
}
function cargarDatosActividad(data) {
    $('#nombreproducto').val(data['nombre']).trigger('change');
    $('#descripcion').val(data['descrip_producto']).trigger('change');
    $('#existencias').val(data['stock']).trigger('change');
    $('#pdtcosto').val(data['precio_costo']).trigger('change');
    $('#pdtventa').val(data['precio_venta']).trigger('change');
    $('#categoria').val(data['fk_categoria']).trigger('change');
}