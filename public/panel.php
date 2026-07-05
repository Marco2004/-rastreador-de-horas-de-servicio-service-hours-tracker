<?php
require_once __DIR__ . '/../src/security.php';
require_once __DIR__ . '/../src/auth.php';

send_security_headers();
require_admin_login();
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"
        integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha384-5IbgsdqrjF6rAX1mxBZkKRyUOgEr0/xCGkteJIaRKpvW0Ag0tf6lru4oL2ZhcMvo" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <main role="main" class="col-md-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Panel de control</h1>
                    <a href="api/logout.php" class="btn btn-danger">Cerrar sesión</a>
                </div>

                <div class="content">
                    <div class="card">
                        <div class="card-body">
                            <form id="registroForm">
                                <input type="hidden" id="csrfToken" value="<?= htmlspecialchars($csrf, ENT_QUOTES) ?>">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Matrícula" aria-label="Matrícula" aria-describedby="button-search" name="matricula" required autofocus>
                                    <div class="input-group-append">
                                        <input type="text" class="form-control" id="dateRangePicker" placeholder="Seleccionar rango de fechas" autocomplete="off" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" id="dataTextArea" rows="8" readonly></textarea>
                                </div>
                                <button type="button" class="btn btn-primary" id="searchButton">Buscar</button>
                                <button type="button" class="btn btn-secondary" id="generatePdfButton">Generar PDF</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"
        integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"
        integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha384-duAtk5RV7s42V6Zuw+tRBFcqD8RjRKw6RFnxmxIj1lUGAQJyum/vtcUQX8lqKQjp" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js"
        integrity="sha384-iO46lWIREYImaEcgCJWPFSrSpys/xrefXKXie8J43T8Eg5gEiX6+ZF3PmfZpN3te" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"
        integrity="sha384-15uoZT5zN6lseew0GeeAygJQoFCwK/ZzDGIsQag3BmLoWPik5wQ/+BW9YduMQhEX" crossorigin="anonymous"></script>
    <script src="assets/js/panel.js"></script>
</body>

</html>
