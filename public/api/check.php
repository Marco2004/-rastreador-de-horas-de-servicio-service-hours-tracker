<?php
/**
 * Registro de entrada/salida desde el kiosco (public/index.php).
 *
 * Este endpoint es intencionalmente anonimo: cualquier persona frente al
 * kiosco puede escribir una matricula sin haber iniciado sesion, asi que
 * no lleva token CSRF (no hay una sesion privilegiada que proteger aqui).
 *
 * Logica de negocio (sin cambios respecto al sistema original):
 *   1. Si el alumno no tiene ninguna entrada previa, se registra una entrada.
 *   2. Si la ultima entrada tiene 12 horas o mas de antiguedad, se asume que
 *      el alumno olvido registrar su salida (sesion abandonada) y se abre
 *      una entrada nueva en vez de cerrar la anterior.
 *   3. Si la ultima entrada ya tiene una salida asociada, se abre una nueva
 *      entrada (nueva sesion de trabajo).
 *   4. En cualquier otro caso, se registra la salida correspondiente a la
 *      ultima entrada abierta.
 */

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/security.php';

send_security_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metodo no permitido.');
}

$conn = get_db_connection();

$matricula = strtoupper(trim($_POST['matricula'] ?? ''));

$stmt = $conn->prepare('SELECT id, nombre_completo FROM registro WHERE matricula = ? LIMIT 1');
$stmt->bind_param('s', $matricula);
$stmt->execute();
$alumno = $stmt->get_result()->fetch_assoc();

if (!$alumno) {
    echo 'Error: Usuario no registrado.';
    exit;
}

$registro_id = (int) $alumno['id'];
$nombre = $alumno['nombre_completo'];

$stmt = $conn->prepare(
    'SELECT id, hora_entrada FROM registro_entrada WHERE registro_id = ? ORDER BY id DESC LIMIT 1'
);
$stmt->bind_param('i', $registro_id);
$stmt->execute();
$ultima_entrada = $stmt->get_result()->fetch_assoc();

/**
 * Inserta una nueva entrada para el alumno y muestra el mensaje de
 * confirmacion con su hora.
 */
function registrar_nueva_entrada(mysqli $conn, int $registro_id, string $matricula, string $nombre): void
{
    $stmt = $conn->prepare('INSERT INTO registro_entrada (registro_id) VALUES (?)');
    $stmt->bind_param('i', $registro_id);
    $stmt->execute();

    // Se lee con insert_id en vez de un SELECT ... ORDER BY id DESC LIMIT 1
    // adicional, evitando una condicion de carrera teorica si dos
    // solicitudes llegaran casi al mismo tiempo para el mismo alumno.
    $entrada_id = $conn->insert_id;
    $stmt = $conn->prepare('SELECT hora_entrada FROM registro_entrada WHERE id = ?');
    $stmt->bind_param('i', $entrada_id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    echo "Matrícula: $matricula\nNombre: $nombre\nHora de entrada: {$fila['hora_entrada']}";
}

if (!$ultima_entrada) {
    // Primera vez que este alumno usa el kiosco.
    registrar_nueva_entrada($conn, $registro_id, $matricula, $nombre);
    exit;
}

$entrada_id = (int) $ultima_entrada['id'];
$ahora = new DateTime();
$hora_entrada_dt = new DateTime($ultima_entrada['hora_entrada']);
$intervalo = $ahora->diff($hora_entrada_dt);

if ($intervalo->days > 0 || $intervalo->h >= 12) {
    // Sesion abandonada: nunca se registro la salida y ya paso demasiado
    // tiempo. Se trata como una entrada nueva en vez de forzar una salida
    // con datos poco confiables.
    registrar_nueva_entrada($conn, $registro_id, $matricula, $nombre);
    exit;
}

$stmt = $conn->prepare(
    'SELECT id FROM registro_salida WHERE registro_id = ? AND entrada_id = ? ORDER BY id DESC LIMIT 1'
);
$stmt->bind_param('ii', $registro_id, $entrada_id);
$stmt->execute();
$ultima_salida = $stmt->get_result()->fetch_assoc();

if ($ultima_salida) {
    // Ya se cerro esa entrada antes: esto es el inicio de una sesion nueva.
    registrar_nueva_entrada($conn, $registro_id, $matricula, $nombre);
    exit;
}

// Aun no hay salida para la entrada abierta: se registra ahora.
$stmt = $conn->prepare('INSERT INTO registro_salida (registro_id, entrada_id) VALUES (?, ?)');
$stmt->bind_param('ii', $registro_id, $entrada_id);
$stmt->execute();

$salida_id = $conn->insert_id;
$stmt = $conn->prepare('SELECT hora_salida FROM registro_salida WHERE id = ?');
$stmt->bind_param('i', $salida_id);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();

echo "Matrícula: $matricula\nNombre: $nombre\nHora de salida: {$fila['hora_salida']}";
