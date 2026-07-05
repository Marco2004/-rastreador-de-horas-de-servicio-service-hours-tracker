/**
 * Muestra una alerta a partir de un parametro ?error=codigo en la URL.
 *
 * Reemplaza el patron anterior de echo "<script>alert('...')</script>"
 * directamente desde PHP, que no es compatible con una Content-Security-
 * Policy que no permite script-src 'unsafe-inline'. El comportamiento que
 * ve el usuario (la misma alerta) no cambia.
 */
document.addEventListener('DOMContentLoaded', function () {
    const mensajes = {
        invalid: 'Usuario o contraseña incorrectos.',
        rate_limited: 'Demasiados intentos fallidos. Intenta de nuevo en unos minutos.',
        duplicate: 'Este usuario ya ha sido registrado.',
        csrf: 'Tu sesión de formulario expiró, intenta de nuevo.',
    };

    const params = new URLSearchParams(window.location.search);
    const codigo = params.get('error');

    if (codigo && mensajes[codigo]) {
        alert(mensajes[codigo]);
        // Limpiar el parametro de la URL para que no se repita la alerta
        // si el usuario recarga la pagina.
        history.replaceState({}, '', window.location.pathname);
    }
});
