export function getBackEnd() {
    const url = new URL(window.location.href);
    // Tomamos hasta /web/
    let path = url.origin + "/tiendaVirtual/web/";
    return path + "index.php?r=";
}
