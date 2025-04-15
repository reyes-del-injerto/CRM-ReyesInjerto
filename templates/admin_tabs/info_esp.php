<?php
// Mostrar todos los errores (útil para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye la conexión a la base de datos
require_once "../../scripts/common/connection_db.php";

try {
    // Obtener fechas de inicio y fin del mes actual
    $currentDate = new DateTime();
    $startDate = new DateTime($currentDate->format('Y-m-01'));
    $endDate = new DateTime($currentDate->format('Y-m-t'));
    $startDateSQL = $startDate->format('Y-m-d 00:00:00');
    $endDateSQL = $endDate->format('Y-m-d 23:59:59');


    // Obtener el nombre del mes en español
    function getMonthNameInSpanish($monthNumber)
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        return $months[$monthNumber];
    }
    $monthNumber = $currentDate->format('n');
    $monthNameSpanish = getMonthNameInSpanish($monthNumber);
} catch (Exception $e) {
    // Manejo de errores
    echo "Ocurrió un error: " . $e->getMessage();
}
?>



<div class="contenedor_gral_esp">
    <h2>Informe de procedimietos - <?php echo isset($monthNameSpanish) ? htmlspecialchars($monthNameSpanish) : 'N/A'; ?></h2>
    <div class="col-sm-12 text-end"> <!-- Alineación del botón a la derecha -->
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" id="clinicButton" data-bs-toggle="dropdown" aria-expanded="false">
                Clinica
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item item_clinic" href="#" data-clinic="Santa Fe">Santa Fe</a></li>
                <li><a class="dropdown-item item_clinic" href="#" data-clinic="Queretaro">Queretaro</a></li>
            </ul>
        </div>
    </div>


    <div id="tabla-procedimientos"></div>
</div>

<script>
    $(document).ready(function() {
        // Obtener el mes actual en formato numérico
        var mesActual = <?php echo json_encode($monthNumber); ?>;

        $(document).on('click', '.item_clinic', function(e) {
            e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
            const clinica = $(this).data('clinic'); // Obtener el valor de la clínica

            // Actualizar el texto del botón con la clínica seleccionada
            $('#clinicButton').text(clinica);
            // Remover la clase 'active' de todos los elementos y añadirla al seleccionado
            $('.item_clinic').removeClass('active'); // Quitar la clase active de todos
            $(this).addClass('active'); // Agregar clase active al elemento seleccionado

            // Llama a la función para cargar pacientes con el valor de la clínica seleccionada
            loadEspecialistByClinic(clinica);
        });

        let clinica_prederteminada = localStorage.getItem("clinica")
        loadEspecialistByClinic(clinica_prederteminada)

        if (clinica_prederteminada) {
            // Actualizar el texto del botón con la clínica guardada
            $('#clinicButton').text(clinica_prederteminada);

            // Marcar como activo el item correspondiente en el dropdown
            $('.item_clinic').each(function() {
                if ($(this).data('clinic') === clinica_prederteminada) {
                    $(this).addClass('active'); // Agregar clase active al elemento correspondiente
                } else {
                    $(this).removeClass('active'); // Quitar clase active de otros elementos
                }
            });
        }

        function loadEspecialistByClinic(clinica) {
            // Realizar la petición AJAX
            $.ajax({
                url: 'scripts/Admin/especialist/load_procedures.php',
                method: 'POST',
                data: {
                    month: mesActual,
                    clinica: clinica

                },
                dataType: 'json',
                success: function(response) {
                    // Verifica si la respuesta contiene procedimientos
                    if (response.data && response.data.length > 0) {
                        console.log(response.data);
                        // Agrupa los procedimientos por especialista
                        var procedimientosPorEspecialista = {};
                        response.data.forEach(function(procedimiento) {
                            if (!procedimientosPorEspecialista[procedimiento.specialist]) {
                                procedimientosPorEspecialista[procedimiento.specialist] = [];
                            }
                            procedimientosPorEspecialista[procedimiento.specialist].push(procedimiento);
                        });

                        // Construir la tabla HTML
                        var tablaHtml = '';
                        $.each(procedimientosPorEspecialista, function(especialista, procedimientos) {
                            // Obtén el total de procedimientos para este especialista
                            var totalProcedimientos = procedimientos.length;

                            // Modifica esta línea para incluir el total al lado del nombre del especialista
                            tablaHtml += '<h3>' + especialista + ' (Total: ' + totalProcedimientos + ')</h3>';

                            // Añadir el botón de descarga
                            //tablaHtml += ' <button class="btn btn-primary descargar-btn" data-especialista="' + especialista + '">Descargar</button></h3>';
                            tablaHtml += '<img src="assets/img/excel.svg" alt="Descargar Excel" class=" descargar-btn" style="width: 20px; height: 20px;" data-especialista="' + especialista + '">';

                            tablaHtml += '<table class="table" cellpadding="5">';
                            tablaHtml += '<tr><th>Nombre del Paciente</th><th>Número de exp</th><th>Fecha</th><th>Tipo de Procedimiento</th></tr>';

                            procedimientos.forEach(function(procedimiento) {
                                tablaHtml += '<tr>';
                                tablaHtml += '<td>' + procedimiento.name + '</td>';
                                tablaHtml += '<td>' + procedimiento.num_med + '</td>';
                                tablaHtml += '<td>' + procedimiento.procedure_date + '</td>';
                                tablaHtml += '<td>' + procedimiento.procedure_type + '</td>';
                                tablaHtml += '</tr>';
                            });

                            tablaHtml += '</table><br>';
                        });

                        // Insertar la tabla en el div
                        $('#tabla-procedimientos').html(tablaHtml);

                        // Agregar el evento click a los botones de descarga
                        $('.descargar-btn').click(function() {
                            var especialista = $(this).data('especialista');

                            // Hacer la petición AJAX para obtener el enlace de descarga
                            $.ajax({
                                url: 'scripts/Admin/especialist/get_download_link.php', // Cambia esto al script que genera el enlace de descarga
                                method: 'POST',
                                data: {
                                    especialista: especialista,
                                    month: mesActual
                                },
                                success: function(response) {
                                    // Abre una nueva pestaña con el enlace obtenido
                                    if (response.url) {
                                        window.open(response.url, '_blank');
                                    } else {
                                        console.error('No se pudo obtener el enlace de descarga.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error al obtener el enlace de descarga:', error);
                                }
                            });
                        });

                    } else {
                        $('#tabla-procedimientos').html('<p>No se encontraron procedimientos para este mes.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar los procedimientos:', error);
                }
            });

        }



    });
</script>