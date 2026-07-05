<?php
require_once __DIR__ . '/../src/security.php';
send_security_headers();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Horas - Kiosco</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"
        integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <main role="main" class="col-md-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">BIENVENIDO</h1>
                    <a href="registro.php" class="btn btn-primary">Registro</a>
                </div>

                <div class="content">
                    <div class="card">
                        <div class="card-body">
                            <form id="registroForm">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Matrícula" aria-label="Matrícula" aria-describedby="button-search" name="matricula" required autofocus>
                                    <button class="btn btn-outline-secondary" type="submit" id="button-search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" id="dataTextArea" rows="8" readonly></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <script src="assets/js/kiosk.js"></script>
</body>

</html>
