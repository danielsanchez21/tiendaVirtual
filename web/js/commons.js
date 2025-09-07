export function getBackEnd() {
    const url = new URL(window.location.href);
    // Tomamos hasta /web/
    let path = url.origin + "/tiendaVirtual/web/";
    return path + "index.php?r=";
}
export function unBlockUI()
{
    $.unblockUI();
}
export function blockUI()
{
    $.blockUI({
        message: '<h1>Procesando!</h1>',
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .5,
            color: '#fff'
        }
    });
}