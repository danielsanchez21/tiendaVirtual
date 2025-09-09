import {login_url} from "../const.js";

let AUTH = null; // variable en memoria
window.AUTH = AUTH; // expuesta globalmente

document.addEventListener("DOMContentLoaded", () => {
    // Cargar AUTH desde sessionStorage si ya existe
    const stored = sessionStorage.getItem('AUTH');
    if (stored) {
        try {
            AUTH = JSON.parse(stored);
            window.AUTH = AUTH;
            // Configurar headers por defecto si hay token
            if (AUTH && AUTH.token) {
                window.$.ajaxSetup({
                    headers: {
                        'Authorization': 'Bearer ' + AUTH.token,
                        'X-Auth-Token': AUTH.token
                    }
                });
            }
        } catch (e) {
            sessionStorage.removeItem('AUTH');
        }
    }

    // Enlazar envío del formulario de login
    window.$('#login').on('click', function(event) {
        event.preventDefault();
        doLogin();
    });
});



// Lógica de autenticación
function doLogin(){
    var data = new FormData(window.$('#loginForm')[0]);
    window.$.ajax({
        type: 'POST',
        url: login_url,
        data: data,
        contentType: false,
        dataType: 'JSON',
        cache: false,
        processData: false,
        success: function (resp) {
            if (resp && resp.success) {
               console.log(resp);
                AUTH = {
                    token: resp.token || null,
                    is_admin: !!resp.is_admin,
                    user: resp.data ? {
                        id: resp.data.id_user || null,
                        name: resp.data.nombre || '',
                        role_id: resp.data.id_rol || null,
                        role_name: resp.data.rol_nombre || ''
                    } : null,
                    allowed: resp.allowed || (resp.is_admin ? 'all' : 'producto')
                };
                window.AUTH = AUTH;
                sessionStorage.setItem('AUTH', JSON.stringify(AUTH));

                // Headers por defecto con token para futuras peticiones
                if (AUTH.token) {
                    window.$.ajaxSetup({
                        headers: {
                            'Authorization': 'Bearer ' + AUTH.token,
                            'X-Auth-Token': AUTH.token
                        }
                    });
                }

                // Refrescar header/topbar del padre inmediatamente
                try {
                    if (parent && parent.configureMenuByAuth) {
                        parent.configureMenuByAuth();
                    }
                } catch (e) {}

                // Redirección según permisos
                if (AUTH.is_admin || AUTH.allowed === 'all') {
                    parent.document.querySelector("iframe[name='contentFrame']").src = "html/views/productos.html";
                } else {
                    // Solo puede ver Producto.html
                    parent.document.querySelector("iframe[name='contentFrame']").src = "html/views/productos.html";
                }
            } else {
                alert("Error: " + (resp && resp.message ? resp.message : 'No se pudo iniciar sesión'));
            }s
        },
        error: function (xhr) {
            console.error("Error en la petición:", xhr.responseText);
            alert("Ocurrió un error al iniciar sesión ❌");
        }
    });
}

// Helper para leer AUTH desde otras vistas
export function getAuth(){
    if (AUTH) return AUTH;
    const stored = sessionStorage.getItem('AUTH');
    if (stored) {
        try {
            AUTH = JSON.parse(stored);
            window.AUTH = AUTH;
        } catch (e) {
            AUTH = null;
        }
    }
    return AUTH;
}