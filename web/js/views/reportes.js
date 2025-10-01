import {reporte_factura_url, } from "../const.js";

document.addEventListener("DOMContentLoaded", () => {
    $('#ventas').DataTable({
        processing: true,
        serverSide: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '📊 Exportar a Excel'
            },
            {
                extend: 'pdfHtml5',
                text: '📄 Exportar a PDF'
            }
        ],
        ajax: {
            url: reporte_factura_url,
            type: 'POST'
        },
        columns: [
            {data: 0, searchable: true, className: "text-left"},
            {data: 1, searchable: true, className: "text-left"},
            {data: 2, searchable: true, className: "text-left"},
            {data: 3, searchable: true, className: "text-left"}
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    }).on("draw", function () {
        $('.previous').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-left" style="margin-right: 15%;margin-top: 10%;font-size: x-large; color: black"></i>').css({"background":"transparent", "box-shadow":"none"});
        $('.next').removeClass('paginate_button').addClass('mfb-component__button--child tooltipedit').html('<i class="zmdi zmdi-chevron-right" style="margin-right: 50%;margin-top: 10%;font-size: x-large; color: black"></i>').css({"background":"transparent", "box-shadow":"none"});
    });

});