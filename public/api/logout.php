<?php
/**
 * Cierra la sesion del administrador y regresa al login.
 */

require_once __DIR__ . '/../../src/security.php';
require_once __DIR__ . '/../../src/auth.php';

send_security_headers();
logout_admin();

header('Location: ../login.php');
exit;
