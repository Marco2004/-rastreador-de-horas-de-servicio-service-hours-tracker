$(document).ready(function () {
    var selectedStartDate = null;
    var selectedEndDate = null;

    var dateRangePicker = $('#dateRangePicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: false,
        todayHighlight: true,
        clearBtn: true,
        multidate: true,
        language: 'es'
    }).on('changeDate', function (e) {
        var selectedDates = e.dates;

        if (selectedDates.length > 2) {
            selectedStartDate = selectedDates[0];
            selectedEndDate = selectedDates[1];
            dateRangePicker.datepicker('setDates', [selectedStartDate, selectedEndDate]);
        } else if (selectedDates.length === 2) {
            selectedStartDate = new Date(Math.min(selectedDates[0], selectedDates[1]));
            selectedEndDate = new Date(Math.max(selectedDates[0], selectedDates[1]));
        } else {
            selectedStartDate = null;
            selectedEndDate = null;
        }

        var days = $('.datepicker-days tbody').find('td.day');
        days.removeClass('start-date end-date');
        if (selectedStartDate && selectedEndDate) {
            days.filter(function () {
                var currentDate = new Date($(this).data('date'));
                return currentDate >= selectedStartDate && currentDate <= selectedEndDate;
            }).addClass('range-date').css({
                'background-color': 'rgba(0, 0, 255, 0.1)',
                'border-radius': '0'
            });
            $(days[0]).addClass('start-date');
            $(days[days.length - 1]).addClass('end-date');
        }
    });

    function contarHoras() {
        var matricula = $('[name="matricula"]').val();

        if (matricula.trim() === '') {
            alert('Por favor, introduzca la matrícula.');
            return;
        }

        if (!selectedStartDate || !selectedEndDate) {
            alert('Por favor, seleccione un rango de fechas.');
            return;
        }

        var dateRange = selectedStartDate.getFullYear() + '-' + ('0' + (selectedStartDate.getMonth() + 1)).slice(-2) + '-' + ('0' + selectedStartDate.getDate()).slice(-2) +
            ',' + selectedEndDate.getFullYear() + '-' + ('0' + (selectedEndDate.getMonth() + 1)).slice(-2) + '-' + ('0' + selectedEndDate.getDate()).slice(-2);

        $.ajax({
            type: 'POST',
            url: 'api/check_hours.php',
            data: {
                matricula: matricula,
                dateRange: dateRange,
                csrf_token: $('#csrfToken').val()
            },
            success: function (response) {
                $('#dataTextArea').val(response);
            }
        });
    }

    $('#searchButton').click(contarHoras);

    $('#registroForm').submit(function (event) {
        event.preventDefault();
        contarHoras();
    });

    $('[name="matricula"]').keypress(function (event) {
        if (event.which === 13) {
            event.preventDefault();
            contarHoras();
        }
    });

    // Generacion de PDF: toma tal cual el contenido ya mostrado en el
    // textarea (resultado de la busqueda) y lo vuelca a un PDF simple.
    $('#generatePdfButton').on('click', function () {
        var jsPDF = window.jspdf.jsPDF;
        var doc = new jsPDF();

        var data = $('#dataTextArea').val();
        var matricula = $('[name="matricula"]').val().trim();

        if (data.trim() === '') {
            alert('No se ha realizado ninguna busqueda, favor de realizar una busqueda antes de generar el pdf');
            return;
        }

        doc.setFont('Arial');
        doc.setFontSize(16);

        doc.text('Datos:', 20, 20);
        doc.setFontSize(14);
        doc.text(data, 20, 30, { maxWidth: 170 });

        doc.setFontSize(18);
        doc.setTextColor(40);
        doc.text('Reporte de Horas', 105, 10, null, null, 'center');

        doc.setFontSize(10);
        doc.text('Página 1', 105, 290, null, null, 'center');

        var filename = 'reporte_' + matricula + '.pdf';
        doc.save(filename);
    });
});
