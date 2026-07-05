$(document).ready(function () {
    $('#registroForm').submit(function (event) {
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'api/check.php',
            data: $(this).serialize(),
            success: function (response) {
                $('#dataTextArea').val(response);

                // Limpiar el formulario despues de unos segundos para que
                // el kiosco quede listo para el siguiente alumno.
                setTimeout(function () {
                    $('#registroForm')[0].reset();
                    $('#dataTextArea').val('');
                }, 3000);
            }
        });
    });
});
