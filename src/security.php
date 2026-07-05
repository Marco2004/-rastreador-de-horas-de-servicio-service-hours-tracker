<?php
/**
 * Utilidades de seguridad compartidas: headers defensivos, sesion
 * endurecida, tokens CSRF y rate limiting del login de administrador.
 */

require_once __DIR__ . '/../config.php';

/**
 * Envia headers HTTP defensivos. Se llama al inicio de cada script PHP
 * como refuerzo de los mismos headers definidos en public/.htaccess
 * (por si el hosting no respeta .htaccess, p. ej. detras de Nginx).
 */
function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header(
        "Content-Security-Policy: default-src 'self'; " .
        "script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
        "style-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
        "img-src 'self' data:; " .
        "font-src 'self' https://cdnjs.cloudflare.com; " .
        "connect-src 'self'; " .
        "frame-ancestors 'none'"
    );
}

/**
 * Inicia una sesion con cookies endurecidas (httponly, samesite, secure
 * condicional a HTTPS real). Debe llamarse antes de cualquier otro uso
 * de $_SESSION.
 */
function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = APP_FORCE_HTTPS || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        // 'Lax' (no 'Strict') porque el flujo login.php -> panel.php
        // depende de un redirect normal tras el POST de login.
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Genera (o reutiliza) el token CSRF de la sesion actual.
 */
function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token CSRF recibido en el POST coincida con el de la
 * sesion. Usa hash_equals() para evitar timing attacks.
 */
function verify_csrf(): bool
{
    start_secure_session();
    $sent = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && $sent !== '' && hash_equals($_SESSION['csrf_token'], $sent);
}

/**
 * IP del cliente tal como la ve el servidor. En un despliegue detras de
 * un proxy/balanceador se ajustaria para leer X-Forwarded-For validando
 * la lista de proxies confiables; aqui se asume conexion directa.
 */
function get_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * True si la IP dada ya alcanzo el limite de intentos fallidos de login
 * dentro de la ventana de tiempo configurada.
 */
function is_login_rate_limited(mysqli $conn, string $ip): bool
{
    $stmt = $conn->prepare(
        'SELECT COUNT(*) AS attempts FROM login_attempts
         WHERE ip_address = ? AND attempted_at > (NOW() - INTERVAL ? SECOND)'
    );
    $window = LOGIN_LOCKOUT_SECONDS;
    $stmt->bind_param('si', $ip, $window);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    return (int) $row['attempts'] >= LOGIN_MAX_ATTEMPTS;
}

/**
 * Registra un intento fallido de login para la IP dada.
 */
function register_failed_login(mysqli $conn, string $ip): void
{
    $stmt = $conn->prepare('INSERT INTO login_attempts (ip_address) VALUES (?)');
    $stmt->bind_param('s', $ip);
    $stmt->execute();

    // Limpieza oportunista de intentos viejos, sin depender de un cron job.
    if (random_int(1, 100) === 1) {
        $conn->query('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');
    }
}

/**
 * Limpia los intentos fallidos de una IP (se llama tras un login exitoso).
 */
function clear_login_attempts(mysqli $conn, string $ip): void
{
    $stmt = $conn->prepare('DELETE FROM login_attempts WHERE ip_address = ?');
    $stmt->bind_param('s', $ip);
    $stmt->execute();
}
