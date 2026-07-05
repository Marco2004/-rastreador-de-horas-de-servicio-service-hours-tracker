<?php
/**
 * Autenticacion del panel de administracion.
 */

require_once __DIR__ . '/security.php';

/**
 * Corta la ejecucion y redirige a login.php si no hay una sesion de
 * administrador activa. Se usa como guarda al inicio de cualquier
 * pagina/endpoint restringido al panel (panel.php, api/check_hours.php).
 */
function require_admin_login(): void
{
    start_secure_session();
    if (!isset($_SESSION['usuario'])) {
        header('Location: /login.php');
        exit();
    }
}

/**
 * Valida usuario/contraseña contra cuentas_admin usando password_verify().
 * Devuelve el registro del admin si las credenciales son correctas, o
 * false en caso contrario.
 */
function verify_admin_credentials(mysqli $conn, string $usuario, string $password)
{
    $stmt = $conn->prepare('SELECT usuario, contraseña AS password_hash FROM cuentas_admin WHERE usuario = ? LIMIT 1');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row && password_verify($password, $row['password_hash'])) {
        return $row;
    }

    return false;
}

/**
 * Marca la sesion como autenticada. Regenera el ID de sesion para
 * evitar ataques de session fixation (un atacante que hubiera fijado
 * un ID de sesion antes del login no puede reutilizarlo despues).
 */
function login_admin(string $usuario): void
{
    start_secure_session();
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario;
}

/**
 * Cierra la sesion de administrador por completo.
 */
function logout_admin(): void
{
    start_secure_session();
    $_SESSION = [];
    session_destroy();
}
