<?php
/**
 * Procesa el formulario de login del administrador (public/login.php).
 */

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/security.php';
require_once __DIR__ . '/../../src/auth.php';

send_security_headers();
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo no permitido.');
}

if (!verify_csrf()) {
    header('Location: ../login.php?error=csrf');
    exit;
}

$conn = get_db_connection();
$ip = get_client_ip();

if (is_login_rate_limited($conn, $ip)) {
    header('Location: ../login.php?error=rate_limited');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

$admin = verify_admin_credentials($conn, $usuario, $password);

if ($admin) {
    clear_login_attempts($conn, $ip);
    login_admin($admin['usuario']);
    header('Location: ../panel.php');
    exit;
}

register_failed_login($conn, $ip);
header('Location: ../login.php?error=invalid');
exit;
