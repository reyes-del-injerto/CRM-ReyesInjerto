<?php

// Configuración y conexión a la base de datos
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

session_start();
require_once "scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}

// Verifica si el user_id es diferente de 1 y 20
if ($_SESSION['user_id'] != 1 && $_SESSION['user_id'] != 20  && $_SESSION['user_id'] != 7 && $_SESSION['user_id'] != 41 && $_SESSION['user_id'] != 18) {
    header('Location: login.php');
    exit();
}


setlocale(LC_TIME, 'es_ES');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <!-- <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png"> -->
    <title>Inventarios | RDI CDMX</title>
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


    <!-- Info css -->
    <link rel="stylesheet" href="./assets/css/tab_info.css">


    <style>
        .quickAccions {
            display: flex;
            justify-content: end;
            gap: 1rem;
        }

        #add {
            background-color: green;
            border: none;
            border-radius: 5px;

            cursor: pointer;
            display: inline-flex;
            justify-content: center;
            align-items: center;

            transition: background-color 0.3s ease, transform 0.3s ease;
            color: white;
            /* Cambiar el color del texto */
            font-weight: bold;
            /* Hacer el texto más destacado */
        }

        #add:hover {
            background-color: lightgreen;
            transform: scale(1.1);
            /* Aumenta el tamaño al hacer hover */
        }


        .card {
            padding: 1rem;
            background-color: #fff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            /* max-width: 320px; */
            border-radius: 20px;
            height: 330px;
        }

        .card_titles {
            display: flex;
            align-items: start;
            flex-direction: column;
            padding: 5px 3px;

        }

        .card_titles p {
            margin: 0rem;
            color: #000;
            text-align: center;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;

        }

        .card_titles h6 {
            margin: 0rem;
            color: #000;

            font-size: 13px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;

        }

        .title {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .title span {
            position: relative;
            padding: 0.5rem;
            background-color: #10B981;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
        }

        .title span svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ffffff;
            height: 1rem;
        }

        .title-text {
            margin-left: 0.5rem;
            color: #374151;
            font-size: 18px;
        }

        .percent {
            color: #02972f;
            font-weight: 600;

        }

        .percent_budget {
            color: lightcoral;
            font-weight: 600;

        }

        .data {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .data p {


            color: #1F2937;

            line-height: 2.5rem;
            font-weight: 700;
            text-align: left;
        }

        .data .range {
            position: relative;
            background-color: #E5E7EB;
            width: 100%;
            height: 0.5rem;
            border-radius: 0.25rem;
        }

        .data .range {
            background-color: #e0e0e0;
            /* Color de fondo de la barra de progreso */
            width: 100%;
            /* Ancho completo */
            height: 5px;
            /* Altura de la barra de progreso */
            border-radius: 0.25rem;
            overflow: hidden;
            /* Oculta cualquier contenido que se desborde */
            margin-top: 40px;
        }

        .data .range .fill {
            background-color: #10B981;
            /* Color de la barra de progreso */
            height: 100%;
            border-radius: 0.25rem;
            transition: width 0.3s ease;
            /* Transición suave cuando cambia el ancho */
        }

        .p-title {
            margin-top: 1rem;
            margin-bottom: 1rem;
            color: #1F2937;
            font-size: 1.25rem;
            line-height: 2.5rem;
            font-weight: 700;
            text-align: left;
        }

        .subcategories {
            display: flex;
            flex-direction: column;
        }

        #view {
            padding: 1rem;

        }
    </style>

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
                        <div class="col-sm-6">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Inicio </a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item active">Inventarios</li>

                            </ul>
                        </div>
                        <div class="col-sm-6 text-end"> <!-- Alineación del botón a la derecha -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" id="clinicButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Clinica
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item item_clinic" href="#" data-clinic="Santa fe">Santa Fe</a></li>
                                    <li><a class="dropdown-item item_clinic" href="#" data-clinic="Queretaro">Queretaro</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
            </div>
            <div class="quickAccions">
                <button id="add">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        style="display: block;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>

                <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#quickExitModal">
                    Procedimiento médicos
                </button>

                <!--  <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#changeLocation">
                    Cambio de ubicación
                </button> -->

            </div>
            <div id="general_info" class="row">
                <div class="col-sm-12">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#" data-tab="overview">Vista general</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-tab="checkouts">Movimientos detallados</a>
                        </li>
                    </ul>

                </div>
            </div>

            <div id="view" class="row">

            </div>


        </div>

        <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Registrar Movimiento de Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddMovement" method="post">
                            <input type="hidden" id="isNewProduct" name="is_new_product" value="0"> <!-- 1 = nuevo, 0 = existente -->

                            <!-- Selección de tipo de movimiento -->
                            <label for="movementType">Tipo de Movimiento:</label>
                            <select class="form-control" id="movementType" name="movement_type" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="entrada">Entrada</option>
                                <option value="salida">Salida</option>
                            </select>
                            <br>

                            <!-- Campos para entrada -->
                            <div id="entradaFields" style="display: none;">
                                <!-- Campo dinámico para el nombre del producto -->
                                <div id="productNameField">
                                    <label for="itemName">Nombre del Producto:</label>
                                    <select class="form-control" id="itemNameSelect" name="item_name" required>
                                        <option value="" disabled selected>Seleccione un producto...</option>
                                    </select>
                                </div>
                                <br>

                                <!-- Checkbox para producto nuevo debajo del nombre -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newProductCheckbox">
                                    <label class="form-check-label" for="newProductCheckbox">¿Es un producto nuevo?</label>
                                </div>
                                <br>

                                <label for="itemDescription">Descripción:</label>
                                <input type="text" class="form-control" id="itemDescription" name="item_description" required>
                                <br>

                                <label for="itemsCategory">Categoría:</label>
                                <select class="form-control" id="itemsCategory" name="category" required>
                                    <option value="" disabled selected>Seleccione una categoría...</option>
                                    <option value="Farmacia">Farmacia</option>
                                    <option value="Lanceta">Lanceta</option>
                                    <option value="La paz">La paz</option>
                                    <option value="TIM">TIM</option>
                                    <option value="Imprenta">Imprenta</option>
                                    <option value="Sams">Sams</option>
                                    <option value="Amazon">Amazon</option>
                                    <option value="Office">Office</option>
                                    <option value="Instituto de tricologia">Instituto de tricologia</option>
                                    <option value="Turquia">Turquia</option>
                                </select>
                                <br>

                                <label for="itemQuantity">Cantidad:</label>
                                <input type="number" class="form-control" id="itemQuantity" name="stock" min="1" required>
                                <br>

                                <!-- Campo de ubicación oculto con valor por defecto "Bodega" -->
                                <input type="hidden" id="itemLocation" name="item_location" value="Bodega">

                                <label for="quantityPackage">Cantidad por Paquete:</label>
                                <input type="number" class="form-control" id="quantityPackage" name="quantity_package">
                                <br>



                                <label for=" itemUnit">Unidad de Medida:</label>
                                <select class="form-control" id="unit" name="unit" required>
                                    <option value="" disabled selected>Seleccione la unidad...</option>
                                    <option value="Pieza">Pieza</option>
                                    <option value="Litro">Litro</option>
                                    <option value="cajas">Cajas</option>
                                </select>
                                <br>



                                <!--  <div class=" form-check">
                                <input class="form-check-input" type="checkbox" id="isMedicine" onchange="toggleExpiryDate()">
                                <label class="form-check-label" for="isMedicine">¿Es un medicamento?</label>
                            </div> -->
                                <!--  <br>

                                <div id="expiryDateField" style="display: none;">
                                    <label for="expiryDate">Fecha de Caducidad:</label>
                                    <input type="date" class="form-control" id="expiryDate" name="expiry_date">
                                    <br>
                                </div> -->

                                <!-- Campo de valor mínimo, inicialmente oculto -->
                                <div id="minimumValueField" style="display: none;">
                                    <label for="minimumValue">Valor Mínimo Requerido:</label>
                                    <input type="number" class="form-control" id="minimumValue" name="minimum_value" min="0">
                                    <br>
                                </div>
                            </div>

                            <!-- Campos para salida -->
                            <div id="salidaFields" style="display: none;">
                                <label for="productSelect">Seleccionar Producto:</label>
                                <select class="form-control" id="productSelect" name="product_id" required>
                                    <option value="" disabled selected>Seleccione un producto...</option>
                                </select>
                                <br>

                                <label for="outputQuantity">Cantidad a Salir:</label>
                                <input type="number" class="form-control" id="outputQuantity" name="output_quantity" min="1" required>
                                <br>

                                <select id="receivedBy" name="received_by" required>
                                    <option value="">Selecciona a quién se le entrega</option>
                                    <option value="Idania Bastida">Idania Bastida</option>
                                    <option value="Alison Ruiz">Alison Ruiz</option>
                                    <option value="Dra Oriana">Dra Oriana</option>
                                    <option value="Karen">Karen</option>
                                    <option value="Eliot">Eliot</option>
                                    <option value="Gaby">Gaby</option>
                                    <option value="Yanahi">Yanahi</option>
                                    <option value="Daniela">Daniela</option>
                                    <option value="Alan">Alan</option>
                                    <option value="Sofia">Sofia</option>
                                    <option value="Gloria">Gloria</option>
                                    <option value="Julieta">Julieta</option>
                                    <option value="Sofia">Sofia</option>

                                   
                                    <option value="Hector">Hector</option>
                                    <option value="Luis">Luis</option>
                                    <option value="Xochitl">Xochitl</option>
                                    <option value="Janeth">Janeth</option>
                                    <option value="Dra Samanta">Dra Samanta</option>
                                </select>
                                <br>

                                <label for="outputDate">Fecha:</label>
                                <input type="date" class="form-control" id="outputDate" name="output_date" required>
                                <br>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveButton">Guardar</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="quickExitModal" tabindex="-1" aria-labelledby="quickExitModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickExitModal">Salida rapida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formquickExit" method="post">
                            <label for="receivedBy">Seleccionar a quién se le entrega:</label>
                            <select id="receivedBy" name="received_by" class="form-control" required>
                                <option value="" disabled selected>Seleccione a quién se le entrega</option>
                                <option value="Idania Bastida">Idania Bastida</option>
                                <option value="Dra Oriana">Dra Oriana</option>
                                <option value="Karen">Karen</option>
                                <option value="Eliot">Eliot</option>
                                <option value="Gaby">Gaby</option>
                                <option value="Yanahi">Yanahi</option>
                                <option value="Janeth">Janeth</option>
                                <option value="Dra Samanta">Dra Samanta</option>
                                <option value="Sofia">Sofia</option>

                                
                            </select>
                            <br>

                            <input type="hidden" name="clinic" id="clinic_exit">

                            <label for="type">Tipo:</label>
                            <select id="type" name="type" class="form-control" required>
                                <option value="" disabled selected>Seleccione qué tipo de kits</option>
                                <option value="capilar">Capilar</option>
                                <option value="barba">Barba</option>
                            </select>
                            <br>

                            <label for="outputDate">Fecha:</label>
                            <input type="date" class="form-control" id="outputDate" name="output_date" required>
                            <br>

                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="changeLocation" tabindex="-1" aria-labelledby="changeLocation" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeLocation">Cambio de Ubicación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formChangeLocation" method="post">
                            <label for="product_SelectModal">Seleccionar Producto:</label>
                            <select id="product_SelectModal" name="product" class="form-control" required>
                                <option value="" disabled selected>Seleccione un producto</option>
                            </select>
                            <br>

                            <!-- Ubicación actual -->
                            <label for="currentLocation">Ubicación Actual:</label>
                            <input type="text" id="currentLocation" name="current_location" class="form-control" readonly value="">
                            <br>

                            <!-- Cantidad actual -->
                            <label for="currentQuantity">Cantidad Actual:</label>
                            <input type="text" id="currentQuantity" name="current_quantity" class="form-control" readonly value="">
                            <br>

                            <!-- Selección de destino -->
                            <label for="destinationSelect">Seleccionar Destino:</label>
                            <select id="destinationSelect" name="destination" class="form-control" required>
                                <option value="" disabled selected>Seleccione un destino</option>
                                <option value="Bodega">Bodega</option>
                                <option value="Casa Dra.">Casa Dra</option>
                            </select>
                            <br>

                            <!-- Cantidad a mover -->
                            <label for="quantityToMove">Cantidad a Mover:</label>
                            <input type="number" id="quantityToMove" name="quantity_to_move" class="form-control" required>
                            <br>

                            <!-- Botón para enviar el formulario -->
                            <button type="submit" class="btn btn-primary">Cambiar Ubicación</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="createProduct" tabindex="-1" aria-labelledby="createProduct" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProduct">Cambio de Ubicación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formChangeLocation" method="post">
                            <label for="product_SelectModal">Seleccionar Producto:</label>
                            <select id="product_SelectModal" name="product" class="form-control" required>
                                <option value="" disabled selected>Seleccione un producto</option>
                            </select>
                            <br>

                            <!-- Ubicación actual -->
                            <label for="currentLocation">Ubicación Actual:</label>
                            <input type="text" id="currentLocation" name="current_location" class="form-control" readonly value="">
                            <br>

                            <!-- Cantidad actual -->
                            <label for="currentQuantity">Cantidad Actual:</label>
                            <input type="text" id="currentQuantity" name="current_quantity" class="form-control" readonly value="">
                            <br>

                            <!-- Selección de destino -->
                            <label for="destinationSelect">Seleccionar Destino:</label>
                            <select id="destinationSelect" name="destination" class="form-control" required>
                                <option value="" disabled selected>Seleccione un destino</option>
                                <option value="Bodega">Bodega</option>
                                <option value="Casa Dra.">Casa Dra</option>
                            </select>
                            <br>

                            <!-- Cantidad a mover -->
                            <label for="quantityToMove">Cantidad a Mover:</label>
                            <input type="number" id="quantityToMove" name="quantity_to_move" class="form-control" required>
                            <br>

                            <!-- Botón para enviar el formulario -->
                            <button type="submit" class="btn btn-primary">Cambiar Ubicación</button>
                        </form>
                    </div>
                </div>
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

    <!-- Datatables JS -->
    <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/js/datatables.min.js"></script>

    <!-- counterup JS -->
    <script src="assets/js/jquery.waypoints.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>

    <!-- Apexchart JS -->
    <script src="assets/plugins/apexchart/apexcharts.min.js"></script>
    <script src="assets/plugins/apexchart/chart-data.js"></script>

    <!-- Numeral JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

    <!-- Sweet Alert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            let clinica_prefer = localStorage.getItem("clinica");
            console.log(clinica_prefer)

            // Asignar dinámicamente el evento onchange al select
            document.getElementById('movementType').addEventListener('change', toggleFields);
            document.getElementById('newProductCheckbox').addEventListener('change', toggleNewProductField);
            // onchange="toggleNewProductField()"

            if (clinica_prefer) {
                // Actualizar el texto del botón con la clínica guardada
                $('#clinicButton').text(clinica_prefer);
                console.log("Clínica preferida guardada:", clinica_prefer);

                // Marcar como activo el item correspondiente en el dropdown
                $('.item_clinic').each(function() {
                    // Verificar si el valor de data-clinic coincide con clinica_prefer
                    if ($(this).data('clinic') === clinica_prefer) {
                        $(this).addClass('active'); // Agregar clase 'active' al elemento correspondiente
                        console.log("Se asignó 'active' a:", $(this).data('clinic')); // Log para ver qué elemento fue activado
                    } else {
                        $(this).removeClass('active'); // Quitar clase 'active' de otros elementos
                        console.log("Se removió 'active' de:", $(this).data('clinic')); // Log para ver qué elementos fueron desactivados
                    }
                });

                console.log("Clínica asignada y 'active' aplicado correctamente");
            } else {
                console.log("No se encontró ninguna clínica preferida guardada.");
            }


            showTab("overview", clinica_prefer)



            $(document).on('click', '.item_clinic', function(e) {
                e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
                const clinica = $(this).data('clinic'); // Obtener el valor de la clínica
                console.log("click en clinica", clinica)
                loadProducts(clinica);
                document.getElementById("clinic_exit").value = clinica

                // Actualizar el texto del botón con la clínica seleccionada
                $('#clinicButton').text(clinica);

                // Remover la clase 'active' de todos los elementos y añadirla al seleccionado
                $('.item_clinic').removeClass('active'); // Quitar la clase active de todos
                $(this).addClass('active'); // Agregar clase active al elemento seleccionado

                // Obtener el data del enlace activo en las pestañas
                const activeTab = $('.nav-tabs .nav-link.active').data('tab'); // Cambiado de 'data-tab' a 'data' para que coincida con tu HTML
                console.log(activeTab)
                showTab(activeTab, clinica); // Envía el tab activo y la clínica seleccionada


                // Llama a la función para cargar pacientes con el valor de la clínica seleccionada
                // loadPatientsByClinic(clinica);
            });




            $('.nav-link').on('click', function(event) {
                event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace

                // Quitar la clase 'active' de todas las pestañas
                $('.nav-link').removeClass('active');

                // Añadir la clase 'active' a la pestaña clickeada
                $(this).addClass('active');

                // Obtener el texto de la pestaña activa (opcional, si lo necesitas para algo más)
                var activeTabText = $(this).text().trim();

                // Obtener el valor del atributo data-tab de la pestaña activa
                var activeTabData = $(this).data('tab'); // .data() es preferible a .attr('data-tab')
                console.log("Presionó pestaña: ", activeTabData);

                // Inicializar la variable para almacenar la clínica activa
                var activeClinicData = null;

                // Recorrer los elementos de .item_clinic para encontrar el que tiene la clase 'active'
                $('.item_clinic').each(function() {
                    if ($(this).hasClass('active')) {
                        activeClinicData = $(this).data('clinic'); // Obtener el valor de data-clinic del elemento activo
                    }
                });

                // Si no se encontró ninguna clínica activa, se maneja como null
                if (activeClinicData) {
                    console.log("Clínica activa: ", activeClinicData);
                } else {
                    console.log("No se encontró ninguna clínica activa.");
                }

                // Llamar a la función para mostrar la pestaña activa y enviar la clínica activa
                showTab(activeTabData, activeClinicData);
            });



            function showTab(tab, clinic) {
                console.log('Pestaña llamada:', tab);

                $.ajax({
                    url: `templates/inventary/${tab}.php?clinic=${clinic}`, // URL del endpoint interpolada

                    method: 'GET',
                    success: function(response) {
                        $('#view').html(response); // Actualiza el contenido del div con la respuesta
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la petición:', status, error);
                    }
                });
            }

            $('#add').click(function() {
                $('#miModal').modal('show');
            });






            function loadProducts(activeClinicData) {


                // Hacer la solicitud con el parámetro de clínica
                fetch(`./scripts/inventary/get_actualProducts.php?clinic=${encodeURIComponent(activeClinicData)}`)
                    .then(response => response.json())
                    .then(data => {
                        const itemNameSelect = document.getElementById('itemNameSelect');
                        const productSelect = document.getElementById('productSelect');
                        const product_Select_location = document.getElementById('product_SelectModal');

                        // Limpiar los selects
                        itemNameSelect.innerHTML = '';
                        productSelect.innerHTML = '';
                        product_Select_location.innerHTML = '';

                        // Agregar la opción por defecto
                        itemNameSelect.appendChild(new Option('Seleccione un producto...', '', true, true));
                        productSelect.appendChild(new Option('Seleccione un producto...', '', true, true));
                        product_Select_location.appendChild(new Option('Seleccione un producto...', '', true, true));

                        if (data.status === 'success') {
                            data.data.forEach(product => {
                                // Crear opción para itemNameSelect
                                const itemOption = new Option(product.name, product.name);
                                itemOption.dataset.minimum = product.minimum_required;
                                itemOption.dataset.description = product.description;
                                itemOption.dataset.category = product.category;
                                itemOption.dataset.location = product.location;
                                itemOption.dataset.stock = product.stock;
                                itemOption.dataset.unit = product.unit; // Nuevo campo
                                itemOption.dataset.quantity_package = product.quantity_package; // Nuevo campo
                                itemNameSelect.appendChild(itemOption);

                                // Crear opción para productSelect
                                const exitOption = new Option(product.name, product.id);
                                productSelect.appendChild(exitOption);

                                // Crear opción para product_Select_location
                                const locationOption = new Option(product.name, product.id);
                                locationOption.dataset.location = product.location;
                                locationOption.dataset.stock = product.stock;
                                product_Select_location.appendChild(locationOption);
                            });
                        } else {
                            console.error('Error al obtener productos:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud:', error);
                    });
            }


            function updateProductDetails() {
                console.log("updateProductDetails");
                const productSelect = document.getElementById('product_SelectModal');
                const selectedOption = productSelect.options[productSelect.selectedIndex];

                if (selectedOption.value) {
                    console.log(selectedOption.value);
                    // Obtiene los valores almacenados en los atributos data
                    const location = selectedOption.dataset.location || 'Ubicación no disponible';
                    const stock = selectedOption.dataset.stock || '0';
                    console.log(location);
                    console.log(stock);

                    // Actualiza los campos de ubicación y cantidad
                    document.getElementById('currentLocation').value = location;
                    document.getElementById('currentQuantity').value = stock;
                }
            }

            $('#formChangeLocation').on('submit', function(event) {
                event.preventDefault(); // Evita el envío normal del formulario

                // Obtiene los valores de las cantidades
                var currentQuantity = parseInt($('#currentQuantity').val());
                var quantityToMove = parseInt($('#quantityToMove').val());

                // Valida que la cantidad a mover no sea mayor que la cantidad actual
                if (quantityToMove > currentQuantity) {
                    alert('La cantidad a mover no puede ser mayor que la cantidad actual.');
                    return; // Detiene el envío del formulario
                }

                // Obtiene los datos del formulario
                var formData = $(this).serialize();
                console.log(formData)

                $.ajax({
                    url: './scripts/inventary/changeLocation.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === 'success') {
                            // Maneja el caso de éxito
                            alert('Ubicación cambiada con éxito!');
                            // Limpia el formulario y cierra el modal
                            $('#formChangeLocation')[0].reset(); // Limpia el formulario
                            $('#changeLocation').modal('hide'); // Cierra el modal
                            window.location.reload();
                        } else {
                            // Maneja el caso de error
                            alert('Error al cambiar la ubicación: ' + data.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error en la solicitud:', textStatus, errorThrown);
                        alert('Error al enviar la solicitud. Por favor, inténtalo de nuevo.');
                    }
                });
            });



            loadProducts(localStorage.getItem("clinica"));
            document.getElementById("clinic_exit").value = localStorage.getItem("clinica")

            // Añade un event listener para actualizar los detalles del producto seleccionado
            document.getElementById('product_SelectModal').addEventListener('change', updateProductDetails);



            function toggleFields() {
                const movementType = document.getElementById('movementType').value;
                const entradaFields = document.getElementById('entradaFields');
                const salidaFields = document.getElementById('salidaFields');

                if (movementType === 'entrada') {
                    entradaFields.style.display = 'block';
                    salidaFields.style.display = 'none';
                } else if (movementType === 'salida') {
                    entradaFields.style.display = 'none';
                    salidaFields.style.display = 'block';
                } else {
                    entradaFields.style.display = 'none';
                    salidaFields.style.display = 'none';
                }
            }

            document.getElementById('itemNameSelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const itemDescription = document.getElementById('itemDescription');
                const itemsCategory = document.getElementById('itemsCategory');
                const itemLocation = document.getElementById('itemLocation'); // Asegúrate de tener este campo en tu HTML
                const itemUnit = document.getElementById('unit');
                const itemQuantity_package = document.getElementById('quantityPackage');

                if (selectedOption.value) {
                    itemDescription.value = selectedOption.dataset.description;
                    itemsCategory.value = selectedOption.dataset.category;
                    itemLocation.value = selectedOption.dataset.location;
                    itemUnit.value = selectedOption.dataset.unit; // Actualiza el campo de unidad
                    itemQuantity_package.value = selectedOption.dataset.quantity_package; // Actualiza el campo de cantidad por paquete
                    document.getElementById('isNewProduct').value = "0"; // Producto existente
                    document.getElementById('itemQuantity').value = selectedOption.dataset.minimum; // Asignar minimum_required
                }
            });


            function toggleNewProductField() {
                const isChecked = document.getElementById('newProductCheckbox').checked;
                const productNameField = document.getElementById('productNameField');
                const minimumValueField = document.getElementById('minimumValueField');
                const itemDescription = document.getElementById('itemDescription');

                if (isChecked) {
                    // Mostrar el campo para ingresar un nuevo producto
                    productNameField.innerHTML = `
            <label for="itemName">Nombre del Producto:</label>
            <input type="text" class="form-control" id="itemName" name="item_name" required>
        `;
                    minimumValueField.style.display = 'block';
                    itemDescription.value = ''; // Limpiar la descripción
                } else {
                    // Mostrar el select para elegir un producto existente
                    productNameField.innerHTML = `
            <label for="itemName">Nombre del Producto:</label>
            <select class="form-control" id="itemNameSelect" name="item_name" required>
                <option value="" disabled selected>Seleccione un producto...</option>
            </select>
        `;
                    minimumValueField.style.display = 'none';

                    // Obtener el elemento activo con la clase 'active' y su data-clinic
                    const activeClinicElement = document.querySelector('.item_clinic.active');
                    const activeClinicData = activeClinicElement ? activeClinicElement.getAttribute('data-clinic') : null;

                    if (activeClinicData) {
                        console.log("Cargando productos para la clínica:", activeClinicData);
                        loadProducts(activeClinicData); // Enviar el valor de la clínica como parámetro
                    } else {
                        console.log("No se encontró ninguna clínica activa.");
                    }

                    itemDescription.value = ''; // Limpiar la descripción
                }
            }

            function toggleExpiryDate() {
                const isMedicine = document.getElementById('isMedicine').checked;
                const expiryDateField = document.getElementById('expiryDateField');

                expiryDateField.style.display = isMedicine ? 'block' : 'none';
            }

            $('#formAddMovement').on('submit', submitForm);

            $('#saveButton').on('click', function(event) {
                submitForm(event); // Llama a la función submitForm
            });

            function submitForm(event) {
                event.preventDefault();

                const activeClinic = document.querySelector('.item_clinic.active');
                const clinicData = activeClinic.getAttribute('data-clinic');

                console.log("a enviar", clinicData);

                // Obtener tipo de movimiento
                const movementType = document.getElementById('movementType').value;
                const form = document.getElementById('formAddMovement');

                const formData = new FormData(form);
                // Agregar 'clinicData' a formData
                formData.append('clinic', clinicData);

                const url = movementType === 'entrada' ? './scripts/inventary/add_stock.php' : './scripts/inventary/remove_stock.php';

                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const message = movementType === 'entrada' ? 'Movimiento guardado correctamente.' : 'Salida de stock registrada correctamente.';
                            alert(message);
                            $('#miModal').modal('hide');
                            form.reset();
                            window.location.reload();
                        } else {
                            alert('Error al guardar el movimiento: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud:', error);
                        alert('Ocurrió un error al guardar el movimiento.');
                    });
            }




            // Nueva función para procesar salida de stock
            function submitOutputForm(event) {
                event.preventDefault();

                const form = document.getElementById('formAddMovement');
                const formData = new FormData(form);

                fetch('./scripts/inventary/remove_stock.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Salida de stock registrada correctamente.');
                            $('#miModal').modal('hide');
                            form.reset();
                            loadProducts(activeClinicData);
                            window.location.reload();
                        } else {
                            alert('Error al registrar la salida de stock: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud:', error);
                        alert('Ocurrió un error al registrar la salida de stock.');
                    });
            }
        });
    </script>


    <script>
        document.getElementById('formquickExit').onsubmit = function(event) {
            event.preventDefault(); // Prevenir el envío del formulario por defecto

            // Obtener el formulario
            const form = document.getElementById('formquickExit');

            // Crear un objeto FormData con los datos del formulario
            const formData = new FormData(form);

            // Enviar la solicitud AJAX
            fetch('scripts/inventary/quick_exit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    // Manejar la respuesta del servidor
                    if (data.status === 'success') {
                        alert('Salida de kits registrada correctamente');
                        form.reset(); // Limpiar el formulario
                        window.location.reload();
                    } else {
                        alert('Error al registrar la salida de kits: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud AJAX:', error);
                    alert('Ocurrió un error al enviar los datos. Por favor, inténtalo de nuevo.');
                });
        };
    </script>




</body>

</html>