<?php
/**
 * Conexion compartida a la base de datos.
 *
 * Antes, cada script PHP del proyecto abria su propia conexion mysqli
 * repitiendo host/usuario/password (incluidas credenciales reales
 * hardcodeadas). Ahora todo pasa por get_db_connection(), que lee los
 * datos de config.php (fuera del control de versiones).
 */

require_once __DIR__ . '/../config.php';

/**
 * Devuelve una conexion mysqli reutilizable para la peticion actual.
 */
function get_db_connection(): mysqli
{
    static $conn = null;

    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset(DB_CHARSET);
        } catch (mysqli_sql_exception $e) {
            // El detalle tecnico (host, credenciales invalidas, etc.) solo
            // se registra en el log del servidor; al cliente no se le
            // expone infraestructura interna.
            error_log('Error de conexion a la base de datos: ' . $e->getMessage());
            http_response_code(500);
            die('Error de conexion con la base de datos.');
        }
    }

    return $conn;
}
