<?php
/**
 * Procesa el alta unica de un alumno (public/registro.php).
 */

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/security.php';

send_security_headers();
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo no permitido.');
}

if (!verify_csrf()) {
    header('Location: ../registro.php?error=csrf');
    exit;
}

$conn = get_db_connection();

$fullname = strtoupper(trim($_POST['fullname'] ?? ''));
$matricula = strtoupper(trim($_POST['matricula'] ?? ''));
$phone = trim($_POST['phone'] ?? '');

$stmt = $conn->prepare('SELECT id FROM registro WHERE matricula = ? LIMIT 1');
$stmt->bind_param('s', $matricula);
$stmt->execute();
$existe = $stmt->get_result()->fetch_assoc();

if ($existe) {
    header('Location: ../registro.php?error=duplicate');
    exit;
}

try {
    $stmt = $conn->prepare('INSERT INTO registro (nombre_completo, matricula, telefono) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $fullname, $matricula, $phone);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    // 1062 = entrada duplicada. Puede ocurrir por una condicion de carrera
    // si dos registros con la misma matricula llegan casi al mismo tiempo,
    // ya que la tabla tiene una restriccion UNIQUE ademas de la validacion
    // de arriba.
    if ($e->getCode() === 1062) {
        header('Location: ../registro.php?error=duplicate');
        exit;
    }
    throw $e;
}

header('Location: ../index.php');
exit;
