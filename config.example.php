<?php
/**
 * Plantilla de configuracion publica.
 *
 * Copia este archivo como "config.php" (mismo directorio) y reemplaza los
 * valores por los de tu entorno. config.php esta excluido via .gitignore:
 * nunca debe subirse al repositorio con datos reales.
 */

// --- Base de datos ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'serviciosocial');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// --- Entorno de ejecucion ---
// 'local' para desarrollo, 'production' para servidor real.
define('APP_ENV', 'local');

// Forzar cookies de sesion "secure" (solo viajan por HTTPS).
// Debe ser true en cualquier despliegue real servido con HTTPS.
define('APP_FORCE_HTTPS', false);

// --- Rate limiting del login de administrador ---
// Maximo de intentos fallidos permitidos dentro de la ventana de tiempo.
define('LOGIN_MAX_ATTEMPTS', 5);
// Duracion de la ventana de bloqueo, en segundos.
define('LOGIN_LOCKOUT_SECONDS', 300);
