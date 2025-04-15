<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

require_once "scripts/common/connection_db.php";




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Inicio | Los Reyes del Injerto</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="assets/plugins/datatables/css/datatables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

</head>

<body>
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
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item active"><a href="view_log.php">Diseño</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <div class="row">
                    <h2 class="text-center mb-4">Hoja de diseño</h2>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">

                                <h4>Selecciona el px:</h4>
                                <select id="patientsSelect" class="form-select" aria-label="Default select example">
                                    <option selected>Selecciona un paciente</option>
                                </select>
                                <input type="hidden" name="px" id="px">


                                <!-- Zona de carga 1 -->
                                <form class="dropzone" id="dropzoneArea1"></form>

                                <!-- Zona de carga 2 -->
                                <form class="dropzone" id="dropzoneArea2"></form>



                                <!-- Botón para generar el archivo Word -->
                                <button id="generateWord" class="btn btn-primary">Generar Word</button>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>

    <!-- jQuery -->
    <script src="assets/js/jquery.min.js"></script>


    <!-- Feather Js -->
    <script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

    <!-- Slimscroll -->
    <script src="assets/js/jquery.slimscroll.js"></script>

    <!-- Datatables JS -->
    <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/js/datatables.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            // Realiza la solicitud AJAX al cargar la página
            let clinic = localStorage.getItem("clinica")
            $.ajax({
                url: "./scripts/reception/get_patients_today.php",
                method: "POST", // Cambia a "GET" si tu endpoint lo requiere
                data: {
                    clinic: clinic
                }, // Envía la clínica al servidor
                dataType: "json",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Limpia el select antes de agregar opciones
                        const $select = $("#patientsSelect");
                        $select.empty();

                        // Agrega la opción inicial
                        $select.append('<option selected disabled>Selecciona un paciente</option>');

                        // Itera sobre los datos y agrega opciones
                        response.data.forEach(patient => {
                            const option = `<option value="${patient.name}">${patient.name}</option>`;
                            $select.append(option);
                        });
                    } else {
                        alert("No se pudieron obtener los pacientes: " + response.message);
                    }
                },
                error: function() {
                    alert("Hubo un error al cargar los pacientes.");
                }
            });
        });
    </script>
    <script>
        const filesArea1 = [];
        const filesArea2 = [];

        Dropzone.autoDiscover = false;

        // Configuración de Dropzone para el Área 1
        const dropzoneArea1 = new Dropzone("#dropzoneArea1", {
            url: "/fake-url", // No se envían automáticamente
            autoProcessQueue: false,
            paramName: "file",
            acceptedFiles: ".jpg,.png,.pdf,.jpeg,.heic",
            dictDefaultMessage: "Arrastra tus archivos aquí para el Área 1",
            init: function() {
                this.on("addedfile", function(file) {
                    filesArea1.push(file);
                });
            }
        });

        // Configuración de Dropzone para el Área 2
        const dropzoneArea2 = new Dropzone("#dropzoneArea2", {
            url: "/fake-url", // No se envían automáticamente
            autoProcessQueue: false,
            paramName: "file",
            acceptedFiles: ".jpg,.png,.pdf,.jpeg,.heic",
            dictDefaultMessage: "Arrastra tus archivos aquí para el Área 2",
            init: function() {
                this.on("addedfile", function(file) {
                    filesArea2.push(file);
                });
            }
        });

        // Configuración de Dropzone para el Área 3
        let selectedValue = ""
        // Configuración del evento onchange para el select
        const selectElement = document.getElementById("patientsSelect");

        selectElement.addEventListener("change", function() {
            selectedValue = selectElement.value;

            console.log("Paciente seleccionado:", selectedValue); // Depuración
            document.getElementById("px").value = selectedValue;
        });

        // Botón para generar el archivo Word
        document.getElementById("generateWord").addEventListener("click", function() {
            // Obtener el nombre del paciente seleccionado

            // Validar que se haya seleccionado un paciente
            if (!selectedValue || selectedValue.trim() === "") {
                alert("Por favor, selecciona un paciente antes de generar el archivo Word.");
                return; // Detener el envío si no hay paciente seleccionado
            }

            // Validar que cada área tenga al menos 3 archivos
            if (filesArea1.length < 3) {
                alert("Debes seleccionar al menos 3 archivos en el Área 1.");
                return;
            }
            if (filesArea2.length < 3) {
                alert("Debes seleccionar al menos 3 archivos en el Área 2.");
                return;
            }

            const formData = new FormData();
            formData.append("selectedPatient", selectedValue);

            // Añadir archivos del Área 1
            filesArea1.forEach((file, index) => formData.append(`area1File${index}`, file));

            // Añadir archivos del Área 2
            filesArea2.forEach((file, index) => formData.append(`area2File${index}`, file));

            fetch('./scripts/reception/generateWord.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error("No se pudo generar el archivo");

                    // Obtener el nombre del paciente del servidor si lo envía
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let fileName = 'Fotos_Areas_deecto.docx'; // Nombre por defecto

                    if (contentDisposition) {
                        const matches = contentDisposition.match(/filename="(.+?)"/);
                        if (matches && matches[1]) {
                            fileName = matches[1];
                        }
                    } else {
                        // Si no hay encabezado, usar el nombre del paciente
                        fileName = `${selectedValue}_Fotos_Areas.docx`;
                    }

                    return response.blob().then(blob => ({
                        blob,
                        fileName
                    }));
                })
                .then(({
                    blob,
                    fileName
                }) => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = fileName; // Usar el nombre del archivo
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => alert("Error al generar el archivo Word: " + error));
        });
    </script>



</body>

</html>