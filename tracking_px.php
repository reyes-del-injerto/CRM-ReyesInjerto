<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

session_start();
//$permissions_needed = array(3, 4, 5, 6);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <!-- <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png"> -->
    <title>Gastos diarios | ERP | Los Reyes del Injerto</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="assets/plugins/datatables/css/datatables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


    <style>
        .event_status {
            position: absolute;
            right: 10%;


        }


        .content_px {
            margin-top: 1.3rem;
        }

        #datepicker {
            border: 1px solid;
            max-width: 10%;
            position: absolute;
            left: 80%;
            top: 15%;
        }

        .ui-datepicker {
            background-color: #fff;
            /* Fondo blanco sólido */
            opacity: 1;
            /* Asegura que no haya transparencia */
            z-index: 1000;
            /* Asegura que esté por encima de otros elementos */
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 1rem;
            /* Agrega un borde para mejor visibilidad */
        }

        @media (max-width: 768px) {
            .accordion-button {
                font-size: 0.9rem;
                /* Texto más pequeño en móviles */
                padding: 0.75rem 1rem;
            }

            .accordion-body {
                font-size: 0.85rem;
                /* Ajustar tamaño de texto en el cuerpo */
            }

            .badge {
                /* font-size: 0.75rem;
                padding: 0.3em 0.6em;
                
                left: 85%; */
                top: 75%;
            }

            .title_button {
                display: inline;
                padding: 1.3rem 0.5rem;
            }
        }
    </style>
</head>

<body class="mini-sidebar">
    <div class="main-wrapper">
        <?php
        require 'templates/header.php';
        require 'templates/sidebar.php';
        ?>
        <div class="page-wrapper">
            <div class="content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Inicio </a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item active">Marketing</li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item active">Seguimiento de px:</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->


                <div class="row">
                    <h4>Mostrando px del dia: <span id="date_title"></span> - <span id="clinic_show"></span> </h4>
                    <div class="datepiccker_input">

                        <p>Seleccione para cambiar: <input class="input-group" type="text" id="datepicker"></p>

                        <div id="titles">
                            <div id="period" class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    Clinica:
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li> <a class="dropdown-item clinic_selected" href="#" data-clinic="Santa Fe" active current>Santa Fe</a></li>
                                    <li> <a class="dropdown-item clinic_selected" href="#" data-clinic="Pedregal">Pedregal</a></li>
                                    <li> <a class="dropdown-item clinic_selected" href="#" data-clinic="Queretaro">Queretaro</a></li>

                                </ul>
                            </div>


                        </div>
                    </div>


                </div>

                <div class="content_px"></div>

            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>


    <!-- jQuery -->
    <script src="assets/js/jquery.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- Feather Js -->
    <script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

    <!-- Slimscroll -->
    <script src="assets/js/jquery.slimscroll.js"></script>

    <!-- Select2 Js -->
    <script src="assets/js/select2.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/accent-neutralise.js"></script>
    <script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/diacritics-neutralise.js"></script>

    <!-- Sweet Alert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>

    <!-- Export To Excel -->
    <script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
    <!-- 	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> -->
    <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {

            var today = new Date();
            var formattedDate = today.getFullYear() + '-' +
                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                String(today.getDate()).padStart(2, '0');

            // Inicializa con la fecha de hoy
            const title = document.getElementById("date_title");
            title.innerHTML = formattedDate;
            //const dia_corte = document.getElementById("dia_corte");
            //dia_corte.innerHTML = formattedDate;

            var clinic = "Santa Fe";
            const clinicSelected = document.getElementById("clinic_show");
            clinicSelected.innerText = clinic;

            console.log("Valores iniciales:")
            console.log("Clinica", clinic)
            console.log("Fecha", formattedDate)

            getpx(formattedDate, clinic)

            $('.clinic_selected').on('click', function(event) {
                event.preventDefault(); // Evita que el enlace recargue la página
                clinic = $(this).data('clinic'); // Obtén el valor del atributo 'data-clinic'
                console.log("click en", clinic);
                getpx(formattedDate, clinic)

                // Actualiza el texto del elemento clinic_show
                clinicSelected.innerText = clinic;
                console.log(formattedDate);

                const dateParts = formattedDate.split('-'); // Divide la fecha en partes [YYYY, MM, DD]
                const dateForFilename = `${dateParts[2]}${dateParts[1]}${dateParts[0].slice(-2)}`; // 'DDMMYY'

                console.log("Fecha formateada para el click", dateForFilename);


            });


            // Configuración global predeterminada para todos los datepickers
            $.datepicker.setDefaults({
                showOn: "both",
                buttonImageOnly: true,
                buttonText: "Calendar",
                dateFormat: 'yy-mm-dd' // Establece el formato de fecha en YYYY-MM-DD
            });

            $(".selector").datepicker({
                autoSize: true,
                dateFormat: 'yy-mm-dd' // Asegúrate de que el formato sea consistente
            });

            // Inicialización específica para el datepicker con posición personalizada
            $("#datepicker").datepicker({
                dateFormat: 'yy-mm-dd', // Formato de la fecha
                beforeShow: function(input, inst) {
                    // Calcula la posición del input
                    var offset = $(input).offset();
                    var height = $(input).outerHeight();

                    // Mueve el calendario a la izquierda y más arriba del input
                    setTimeout(function() {
                        inst.dpDiv.css({
                            top: offset.top - height - 40, // Ajusta para mover más arriba (-height y -10px de margen)
                            left: offset.left - inst.dpDiv.outerWidth() - 10 // Mueve a la izquierda
                        });
                    }, 1);
                },
                onSelect: function(dateText) {
                    // Actualiza la variable formattedDate con la fecha seleccionada
                    formattedDate = dateText;
                    $("#date_title").text(dateText);
                    //	$("#dia_corte").text(dateText);
                    console.log("Fecha selected:", formattedDate);
                    getpx(formattedDate, clinic)

                }
            });









        });
    </script>

    <script>
        function getpx(date, clinic) {
            if (clinic === "Santa Fe") {
                console.log("SF");
                clinic = "Santafe";
            }

            let folders = ["pre", "diseno", "post", "24horas", "10dias", "1mes", "3meses", "6meses", "9meses", "12meses", "15meses", "18meses", "21meses", "post_alta"];

            // Construir la URL con los parámetros enviados por la función
            const url = `scripts/photos/tracking.php?date=${encodeURIComponent(date)}&clinic=${encodeURIComponent(clinic)}`;

            // Realizar la petición AJAX
            $.ajax({
                    url: url,
                    method: "GET",
                    dataType: "json", // Cambiar según el tipo de respuesta esperada
                })
                .done(function(response) {
                    // Limpiar el contenido previo
                    $('.content_px').empty();

                    // Verificar si la respuesta fue exitosa
                    if (response.success === "true") {
                        // Si el array 'data' está vacío, mostrar mensaje de "Sin resultados"
                        if (response.data.length === 0) {
                            $('.content_px').html('<p class="text-center">Sin resultados</p>');
                        } else {
                            console.log("Datos recibidos:", response.data);

                            // Crear el acordeón
                            let accordionHTML = `<div class="accordion" id="eventsAccordion">`;

                            response.data.forEach((event, index) => {
                                // Crear un ID único para cada panel del acordeón
                                const eventId = `event${index}`;

                                // Determinar la clase del badge según el estado de event.qualy
                                let badgeClass = "badge-primary"; // Clase por defecto
                                if (event.qualy === "Asistió") {
                                    badgeClass = "badge-success";
                                } else if (event.qualy === "Pendiente") {
                                    badgeClass = "badge-warning";
                                } else if (event.qualy === "No asistió") {
                                    badgeClass = "badge-danger";
                                }

                                // Obtener los nombres de los archivos del evento
                                let existingFiles = event.files.map(file => {
                                    return file.split('/')[0]; // Extraer el nombre de la carpeta del archivo
                                });

                                // Ordenar los archivos existentes en función del array 'folders'
                                existingFiles.sort((a, b) => folders.indexOf(a) - folders.indexOf(b));

                                // Inicializar la variable missingFolders
                                let missingFolders;

                                // Verificar si es un tratamiento y el número de expediente es 0
                                if (event.event_type === "tratamiento" && event.expedienteNumber === 0) {
                                    missingFolders = ["No aplica"];
                                } else {
                                    // Comparar con el array folders para encontrar elementos faltantes
                                    missingFolders = folders.filter(folder => !existingFiles.includes(folder));
                                }

                                // Agregar al acordeón la información del evento, incluyendo los archivos ordenados y los faltantes
                                accordionHTML += `
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading${eventId}">
                <button class="accordion-button title_button " type="button" data-bs-toggle="collapse" data-bs-target="#collapse${eventId}" aria-expanded="true" aria-controls="collapse${eventId}">
                   ${event.title} <strong class=""> / ${event.event_type} ${event.review_time}</strong> <a href="#" class="badge ${badgeClass} event_status">${event.qualy}</a>
                </button>
            </h2>
            <div id="collapse${eventId}" class="accordion-collapse collapse" aria-labelledby="heading${eventId}" data-bs-parent="#eventsAccordion">
                <div class="accordion-body">
                    <strong>Notas:</strong> ${event.description}<br>
                    <strong>Calificación:</strong> ${event.qualy}<br>
                   <strong>Tipo:</strong> ${event.attendance_type == 1 ? 'Virtual' : 'Presencial'}<br>
                    <strong>Status:</strong> ${event.status}<br>
                    <strong>Archivos (ordenados):</strong> ${existingFiles.length > 0 ? existingFiles.join(', ') : 'No hay archivos'}<br>
                    <strong>Carpetas faltantes:</strong> ${missingFolders.length > 0 ? missingFolders.join(', ') : 'Ninguna'}
                </div>
            </div>
        </div>`;
                            });


                            accordionHTML += `</div>`;

                            // Insertar el acordeón en el div de contenido
                            $('.content_px').html(accordionHTML);
                        }
                    } else {
                        console.log("Error en la respuesta:", response.message);
                        $('.content_px').html('<p class="text-center">Sin resultados</p>');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error en la petición AJAX:", textStatus, errorThrown);
                    $('.content_px').html('<p class="text-center">Error en la solicitud. Intente nuevamente.</p>');
                });
        }
    </script>



</body>

</html>