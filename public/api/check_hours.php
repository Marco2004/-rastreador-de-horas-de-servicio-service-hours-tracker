<?php
/**
 * Calculo de horas acumuladas por matricula, para el panel de
 * administracion (public/panel.php).
 *
 * Antes, este endpoint no verificaba ninguna sesion: cualquiera que
 * conociera (o adivinara) una matricula podia consultar sus horas y
 * nombre completo haciendo POST directo aqui, sin pasar por el login.
 * require_admin_login() cierra ese acceso sin cambiar el flujo normal
 * del panel, que ya pasa por el login.
 */

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/security.php';
require_once __DIR__ . '/../../src/auth.php';

send_security_headers();
require_admin_login();

if (!verify_csrf()) {
    http_response_code(403);
    echo 'Token de seguridad inválido. Recarga la página e intenta de nuevo.';
    exit;
}

$conn = get_db_connection();

/**
 * Formatea una fecha "Y-m-d" como "05 de julio de 2026", sin depender de
 * setlocale()/strftime() (deprecado desde PHP 8.1 y ademas poco confiable
 * entre distintos sistemas operativos/hosting).
 */
function formatear_fecha_es(string $fecha): string
{
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];
    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return $fecha;
    }
    return date('d', $timestamp) . ' de ' . $meses[(int) date('n', $timestamp)] . ' de ' . date('Y', $timestamp);
}

if (!isset($_POST['matricula'], $_POST['dateRange'])) {
    echo 'Error: No se recibió la matrícula o el rango de fechas.';
    exit;
}

$matricula = strtoupper(trim($_POST['matricula']));
$dateRange = explode(',', $_POST['dateRange']);

$stmt = $conn->prepare('SELECT COUNT(*) AS matricula_count FROM registro WHERE matricula = ?');
$stmt->bind_param('s', $matricula);
$stmt->execute();
$existe = (int) $stmt->get_result()->fetch_assoc()['matricula_count'] > 0;

if (!$existe) {
    echo "Error: La matrícula $matricula no está registrada en la base de datos.";
    exit;
}

$start_date_str = '';
$end_date_str = '';

if (count($dateRange) === 2) {
    [$start_date, $end_date] = $dateRange;
    $start_date_str = formatear_fecha_es($start_date);
    $end_date_str = formatear_fecha_es($end_date);

    $sql = 'SELECT re.registro_id, r.matricula, r.nombre_completo,
                   SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, re.hora_entrada, rs.hora_salida))) AS total_horas
            FROM registro_entrada re
            JOIN registro_salida rs ON re.id = rs.entrada_id
            JOIN registro r ON re.registro_id = r.id
            WHERE r.matricula = ? AND re.hora_entrada BETWEEN ? AND ?
            GROUP BY re.registro_id, r.matricula, r.nombre_completo';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $matricula, $start_date, $end_date);
} else {
    $sql = 'SELECT re.registro_id, r.matricula, r.nombre_completo,
                   SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, re.hora_entrada, rs.hora_salida))) AS total_horas
            FROM registro_entrada re
            JOIN registro_salida rs ON re.id = rs.entrada_id
            JOIN registro r ON re.registro_id = r.id
            WHERE r.matricula = ?
            GROUP BY re.registro_id, r.matricula, r.nombre_completo';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $matricula);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $output = '';
    while ($row = $result->fetch_assoc()) {
        $output .= 'Matrícula: ' . $row['matricula'] . "\n\n";
        $output .= 'Nombre Completo: ' . $row['nombre_completo'] . "\n\n";
        $output .= "Total de horas (del $start_date_str al $end_date_str): " . $row['total_horas'] . "\n\n";
    }
    echo $output;
} else {
    echo "No se encontraron registros para la matrícula: $matricula en el rango de fechas del $start_date_str al $end_date_str.";
}

$stmt = $conn->prepare(
    'SELECT SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, re.hora_entrada, rs.hora_salida))) AS total_horas
     FROM registro_entrada re
     JOIN registro_salida rs ON re.id = rs.entrada_id
     JOIN registro r ON re.registro_id = r.id
     WHERE r.matricula = ?'
);
$stmt->bind_param('s', $matricula);
$stmt->execute();
$total_result = $stmt->get_result();

$totaldehorastotales = '0';
if ($total_result->num_rows > 0) {
    $fila = $total_result->fetch_assoc();
    if ($fila['total_horas'] !== null) {
        $totaldehorastotales = $fila['total_horas'];
    }
}

echo "Total de horas acumuladas para la matrícula $matricula: $totaldehorastotales";
