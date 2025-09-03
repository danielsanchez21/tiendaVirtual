import {crear_producto_url, listar_categoria_url, tabla_producto_url} from "../const.js";

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
            {'searchable':false, className: "text-left","sortable": false}
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    }).on("draw", function () {
        $('.previous').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-left" style="margin-right: 15%;margin-top: 10%;font-size: x-large; color: black"></i>').css({"background":"transparent", "box-shadow":"none"});
        $('.next').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-right" style="margin-right: 50%;margin-top: 10%;font-size: x-large; color: black"></i>').css({"background":"transparent", "box-shadow":"none"});
    });
    window.$('#guardar_pdt').on('click', function(event) {
        event.preventDefault();
        crearRegistroClienteInyeccion();
    });
    obtenerCategoria();
});

function crearRegistroClienteInyeccion() {
    var data = new FormData(window.$('#form_plan_beneficio')[0]);


    window.$.ajax({
        type: 'POST',
        url: crear_producto_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (data) {

            if(data.success){
                alert("Producto creado con éxito ✅");
                window.$('#form_plan_beneficio')[0].reset();
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