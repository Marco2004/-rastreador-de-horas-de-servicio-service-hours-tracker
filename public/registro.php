<?php
require_once __DIR__ . '/../src/security.php';
send_security_headers();
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"
        integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card">
                    <h1 class="text-center mb-4">Registro</h1>
                    <form id="registrationForm" action="api/register.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                        <div class="mb-3 position-relative">
                            <label for="fullname" class="form-label">Nombre Completo:</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="matricula" class="form-label">Matricula:</label>
                            <input type="text" class="form-control" id="matricula" name="matricula" required pattern="[A-Za-z]{3}[0-9]{6}" title="La matrícula debe comenzar con 3 letras seguidas de 6 números">
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="phone" class="form-label">Número telefónico:</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required pattern="[0-9]+" title="Por favor ingrese solo números" maxlength="10">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="index.php" class="btn btn-secondary">Ir a Home</a>
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                        <div class="mb-3 text-center">
                            <p><a href="login.php" target="_blank">Inicia sesión aquí</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/flash-alert.js"></script>
</body>

</html>
