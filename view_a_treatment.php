<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

$lead_id = isset($_GET['id']) ? $_GET['id'] : null;
$clinic = isset($_GET['clinic']) && !empty($_GET['clinic']) ? $_GET['clinic'] : '';  // Asigna 'Santa Fe' si no se recibe 'clinic'
$px_num_med = isset($_GET['num_med']) ? $_GET['num_med'] : null;
echo "clinicaa recibida: ".$clinic;
$patient_name = '';
$px_identifier = '';
$type = '';
$date = '';

if ($px_num_med !== null) {
    // Consulta para num_med
    $sql_row = "
        SELECT ep.num_med_record as px_ide, CONCAT(sla.first_name, ' ', sla.last_name) AS name 
        FROM enf_procedures ep 
        INNER JOIN sa_leads_assessment sla 
        ON ep.lead_id = sla.lead_id 
        WHERE ep.num_med_record = ? AND ep.clinic = ? ;
    ";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("is", $px_num_med,$clinic); // Bind de $px_num_med
} else if ($lead_id !== null) {
    // Consulta para id
    $sql_row = "
        SELECT t.id as px_ide, t.name, t.num_med_record 
        FROM enf_treatments t 
        WHERE t.id = ?;
    ";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $lead_id); // Bind de $lead_id
} else {
    echo "No se proporcionó ni 'id' ni 'num_med'.";
    exit();
}

// Ejecutar la consulta
$sql->execute();
$result = $sql->get_result();

// Verificar si hay resultados y guardar los datos para el HTML
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patient_name = $row['name'];
        $px_identifier = $row['px_ide'];
        $px_identifier_type = "";

        // Usar la tabla adecuada dependiendo si tiene num_med_record o no
        if ($px_num_med !== null) {
            // Usar enf_treatments_appointments si hay num_med_record
            $px_identifier_type = "Exp";
            $sql_row_appointments = "
                SELECT clinic,type, DATE_FORMAT(date, '%d/%m/%Y') AS date 
                FROM enf_treatments_appointments 
                WHERE num_med_record = ? 
                AND date = (SELECT MAX(date) FROM enf_treatments_appointments WHERE num_med_record = ?);
                
            ";
        } else {
            // Usar enf_treatments_appointments_ext si no hay num_med_record
            $px_identifier_type = "ID";
            $sql_row_appointments = "
                SELECT clinic, type, DATE_FORMAT(date, '%d/%m/%Y') AS date 
                FROM enf_treatments_appointments_ext 
                WHERE px_id = ? 
                AND date = (SELECT MAX(date) FROM enf_treatments_appointments_ext WHERE px_id = ?);
            ";
        }

        // Ejecutar la consulta de la tabla de tratamientos
        $sql_appointments = $conn->prepare($sql_row_appointments);
        $sql_appointments->bind_param("ii", $row['px_ide'], $row['px_ide']);
        $sql_appointments->execute();
        $result_appointments = $sql_appointments->get_result();

        if ($result_appointments->num_rows > 0) {
            $last_treatment = $result_appointments->fetch_object();
            $type = "Último tratamiento tipo: {$last_treatment->type} - en $last_treatment->clinic";
            $date = "Aplicado el: {$last_treatment->date}";
        } else {
            $type = 'Sin aplicaciones registradas';
            $date = '';
        }
    }
} else {
    // Si no hay resultados, muestra SweetAlert y redirige
    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
           
            setTimeout(function() {
                // Muestra la alerta por 2 segundos
                Swal.fire({
                    title: 'Perfil no encontrado',
                    text: 'No se encontro el perfil del px, registralo o solicita su registro',
                    icon: 'warning',
                    showConfirmButton: false,
                    timer: 5000 // Muestra la alerta por 2 segundos
                });

                // Espera 2 segundos después de que se cierre el SweetAlert para redirigir
                setTimeout(function() {
                    window.location.href = 'view_treatments.php';
                }, 5000); // Redirige 2 segundos después de cerrar el SweetAlert
            }, 1000); // Espera inicial de 2 segundos
        });
    </script>";
}

// Cerrar la consulta principal
$sql->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Fotos de Tratamientos | ERP | Los Reyes del Injerto</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

    <!-- Main CSS -->
    <link href="assets/plugins/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css" />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.css'>
    <link rel="stylesheet" href="assets/plugins/fancycards/style.css">

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <link rel="stylesheet" href="./assets/css/uiverse.css">

</head>

<body class="mini-sidebar">
    <div class="main-wrapper">
        <?php
        require 'templates/header.php';
        require 'templates/sidebar.php';
        ?>
        <div class="page-wrapper">
            <div class="content">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard </a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right" style="color:#000;"></i></li>
                                <li class="breadcrumb-item "><a href="view_treatments.php">Tratamientos</a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right" style="color:#000;"></i></li>
                                <li class="breadcrumb-item"><a href="#">Revisiones</a></li>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mx-auto col-12 col-md-4">
                        <div class="card text-white bg-secondary ">
                            <div class="card-body text-center">
                                <h2 style='color:#e0ac44;'> <?php echo  $patient_name; ?> </h2>
                                <p><span style='font-size:20px;' class='badge bg-secondary'> <?php echo $px_identifier_type . " - ".$clinic." : " . $px_identifier; ?> </span></p>
                                <p><span class='badge bg-primary'> <?php echo  $type; ?> </span></p>

                                <p style='font-size:20px;'><strong><?= $date; ?></strong></p>

                                <div class="doctor-search-blk">
                                    <div class="add-group">
                                        <span>
                                            Generar nuevo recibo
                                        </span>
                                        <a type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#modalReceipt">
                                            <img src="assets/img/icons/plus.svg" alt>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="clinic" name="clinic" value="<?php echo $clinic; ?>">
                        </div>
                    </div>

                    <div class="col-12 col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="text-center col-md-12 col-xs-12 order-md-1 order-last">
                                        <input type="hidden" value="<?php echo $px_identifier; ?>" id="px_identifier">
                                        <input type="hidden" value="<?php echo $px_identifier_type; ?>" id="px_identifier_type">

                                    </div>
                                </div>
                                <div class=" slider">
                                    <div class="swiper people__slide">
                                        <div class="swiper-wrapper">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 style='color:#e0ac44;'>Recibos de Tratamientos</h4>
                                <div id="receiptsContainer" class="row">
                                    <!-- Aquí se agregarán dinámicamente los recibos -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!--  <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 style='color:#e0ac44;' id="fileinput-title"></h4>

                                <div class="inputfile-container">
                                    <input type="file" id="file" name="file[]" multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">

                                            <p id="note-title"></p>

                                                <h4 style='color:#e0ac44;' id="fileinput-title"></h4>

                                                <div class="inputfile-container">
                                                    <input type="file" id="file" name="file[]" multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="noteextra">

                                </div>


                                <div id="extraaa">

                                    <div class="extras_medical">
                                        <!-- <button type="button" class="button_ui">
											<span class="button_ui__text">Crear nota</span>
											<span class="button_ui__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg">
													<line y2="19" y1="5" x2="12" x1="12"></line>
													<line y2="12" y1="12" x2="19" x1="5"></line>
												</svg></span>
										</button> -->


                                    </div>


                                </div>

                                <div id="sign_step"></div>



                            </div>
                        </div>
                    </div>
                </div>


                <!-- <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <iframe id="pdfInvoiceViewer" src="" width="100%" height="1000px"></iframe>
                        </div>
                    </div>
                </div> -->

            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar nueva aplicación de tratamiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="formNewTreatment" method="POST">
                        <input type="hidden" id="px_identifier" name="px_identifier" value="<?= $px_identifier; ?>">
                        <input type="hidden" id="px_identifier_type" name="px_identifier_type" value="<?= $px_identifier_type; ?>">
                        <input type="hidden" id="" name="px_fullname" value="<?php echo $patient_name; ?>">
                        <input class="form-control" type="hidden" name="origin" id="origin" value="<?= $clinic; ?>">
                        <input type="hidden" id="user_id_tr" name="user_id_tr" value="">

                        <div class="row">
                            <div class="col-12">
                                <div class="input-block local-forms">
                                    <label>Fecha<span class="login-danger">*</span></label>
                                    <input class="form-control" type="date" name="date" id="date_new_tr" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-block local-forms">
                                    <label>Tipo<span class="login-danger">*</span></label>
                                    <input class="form-control" type="text" name="type" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <label>Clínica<span class="login-danger">*</span></label>
                                    <select class="form-control" id="clinic" name="clinic" required>
                                        <option value="" selected disabled>Selecciona</option>
                                        <option value="Santa Fe">Santa Fe</option>
                                        <option value="pedregal">Pedregal</option>
                                        <option value="Queretaro">Queretaro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <label>Médico responsable<span class="login-danger">*</span></label>
                                    <select class="form-control" id="doctor" name="doctor" required>
                                        <option value="" selected disabled>Selecciona</option>
                                        <option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
                                        <option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
                                        <option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
                                        <option value="Dra. Ana Karen">Dra. Ana Karen</option>
                                        <option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
                                        <option value="Dra. Samanta Duran">Dra. Samanta Duran</option>
                                        <option value="Dra. Priscila Tapia">Dra. Priscila Tapia</option>
                                        <option value="Dr. Luis Andres Peña Melendez">Dr. Luis Andres Peña Melendez</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-block local-forms">
                                    <label>Notas rapidas<span class="login-danger">*</span></label>
                                    <textarea class="form-control" id="notes" name="notes" rows=3 > </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Añadir</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalReceipt" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Generar recibo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="scripts/treatments/add_receipts_trats.php" id="invoice_tr">
                        <input type="hidden" name="invoice_type" id="invoice_type" value="tratamiento">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <input type="hidden" name="px_identifier" id="px_identifier_recibe" value="<?= $px_identifier; ?>">
                        <input type="hidden" name="px_identifier_type" id="px_identifier_type_recibe" value="<?= $px_identifier_type; ?>">

                        <div class="row">
                            <div class="col-12 col-md-12 show-after">
                                <div class="input-block local-forms">
                                    <label>Fecha <span class="login-danger">*</span></label>
                                    <input class="form-control" type="date" name="receipt_date" id="date" value="<?= date("Y-m-d"); ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-12 show-after">
                                <div class="input-block local-forms">
                                    <label>Nombre del Paciente <span class="login-danger">*</span></label>
                                    <input class="form-control" type="text" name="full_name" id="full_name" value="<?= $patient_name; ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="input-block local-forms">
                                    <label>Tipo de recibo<span class="login-danger">*</span></label>
                                    <select class="form-control select" name="invoice_typeee" id="invoice_typeee" required onchange="toggleFields()">
                                        <option selected disabled value="">Selecciona</option>
                                        <option value="Tratamiento">Tratamiento</option>
                                        <option value="Producto">Producto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-12" id="treatment_field" style="display:none;">
                                <div class="input-block local-forms">
                                    <label>Tratamiento que se realizó:<span class="login-danger">*</span></label>
                                    <select class="form-control select" name="treatment" id="treatment">
                                        <option selected disabled value="">Selecciona</option>
                                        <option value="1">Factores de Crecimiento</option>
                                        <option value="2">Dutasteride</option>
                                        <option value="3">Exosomas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-12" id="product_field" style="display:none;">
                                <div class="input-block local-forms">
                                    <label>Producto (s):<span class="login-danger">*</span></label>
                                    <input class="form-control" type="text" name="product" id="product">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 show-after">
                                <div class="input-block local-forms">
                                    <label>Monto total (MXN) <span class="login-danger">*</span></label>
                                    <div class="time-icon">
                                        <input type="number" class="form-control" name="amount" id="amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 show-after">
                                <div class="input-block local-forms">
                                    <label>Forma de Pago:<span class="login-danger">*</span></label>
                                    <select class="form-control select" name="payment_method" id="payment_method" required>
                                        <option selected disabled value="">Selecciona</option>
                                        <option value="1">Efectivo</option>
                                        <option value="2">Transferencia</option>
                                        <option value="3">Tarjeta de Débito</option>
                                        <option value="4">Tarjeta de Crédito</option>
                                        <option value="5">Dólares</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 show-after">
                                <div class="input-block local-forms">
                                    <label>Sucursal:<span class="login-danger">*</span></label>
                                    <select class="form-control select" name="clinic" id="clinic" required>
                                        <option selected disabled value="">Selecciona</option>
                                        <option value="pedregal">Pedregal</option>
                                        <option value="Queretaro">Queretaro</option>
                                        <option value="Santa Fe">Santa Fe</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 mx-auto show-after">
                                <div class="input-block local-forms">
                                    <label>Notas (aparecerán en el recibo)</label>
                                    <textarea class="form-control" rows="3" cols="30" name="notes" id="notes"></textarea>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3" style="display:none;" id="input_other_employee">
                                <div class="input-block local-forms">
                                    <label>Nombre de quien atendió: <span class="login-danger">*</span></label>
                                    <div class="time-icon">
                                        <input type="text" class="form-control" name="other_employee" id="other_employee">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary submit-form me-2">Siguiente</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script>
                        function toggleFields() {
                            console.log("toggle")
                            var invoiceType = document.getElementById('invoice_typeee').value;
                            var treatmentField = document.getElementById('treatment_field');
                            var productField = document.getElementById('product_field');

                            if (invoiceType == 'Tratamiento') {
                                console.log("tratamiento")
                                treatmentField.style.display = 'block';
                                productField.style.display = 'none';
                                document.getElementById('treatment').required = true;
                                document.getElementById('product').required = false;
                            } else if (invoiceType == 'Producto') {
                                console.log("producto")
                                treatmentField.style.display = 'none';
                                productField.style.display = 'block';
                                document.getElementById('treatment').required = false;
                                document.getElementById('product').required = true;
                            } else {
                                console.log("else")
                                treatmentField.style.display = 'none';
                                productField.style.display = 'none';
                                document.getElementById('treatment').required = false;
                                document.getElementById('product').required = false;
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">
                        Editar Procedimiento.
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm" method="post">
                        <div class="form-group">
                            <label for="num_med_record">Número de Expediente:</label>
                            <input type="hidden" class="form-control" id="identifier_notes" name="identifier" required readonly>
                            <input type="hidden" class="form-control" id="identifier_type_notes" name="identifier_type" required readonly>
                            <input type="hidden" class="form-control" id="phase" name="phase" required>
                            <input type="hidden" class="form-control" id="author" name="author" required>
                        </div>

                        <div class="form-group">
                            <label for="note">Nota:</label>
                            <textarea class="form-control" id="note" name="note" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="date">Fecha:</label>
                            <input type="date" class="form-control" id="date_note" name="date" required>
                        </div>


                        <button type="submit" class="btn btn-primary">Agregar Nota</button>
                    </form>


                </div>
            </div>
        </div>
    </div>

    <div class="librerias">

        <!-- jQuery -->
        <script src="assets/js/jquery.min.js"></script>


        <script src="assets/js/buffer.js" type="text/javascript"></script>
        <script src="assets/js/filetype.js" type="text/javascript"></script>

        <!-- Bootstrap Core JS -->
        <script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

        <script src="assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/locales/es.js" type="text/javascript"></script>

        <!-- Slimscroll -->
        <script src="assets/js/jquery.slimscroll.js"></script>

        <script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    </div>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>





    <script>
        $(document).ready(function() {


            const identifier = $("#px_identifier").val();
            const px_identifier_type = $("#px_identifier_type").val();
            console.log("identificador", identifier)
            console.log("tipo", px_identifier_type)

            document.getElementById("identifier_notes").value = identifier;
            document.getElementById("identifier_type_notes").value = px_identifier_type;

            const userId = localStorage.getItem("user_id")
            document.getElementById('user_id').value = userId;

            console.log("user_id", userId);

            document.getElementById('user_id_tr').value = userId;


            document.getElementById("date_new_tr").value = new Date().toISOString().split('T')[0];







            $('#firma').on('submit', function(event) {
                event.preventDefault(); // Prevenir el comportamiento por defecto del formulario
                console.log("enviando")
                // Obtener los datos del formulario
                var formData = $(this).serialize();
                console.log("form", formData)



            });


            $('#addNoteForm').on('submit', function(event) {
                event.preventDefault(); // Prevenir el comportamiento por defecto del formulario
                console.log("enviando")
                // Obtener los datos del formulario
                // Obtener los datos del formulario como un arreglo de objetos {name: value}
                var formDataArray = $(this).serializeArray();
                console.log("formdata", formDataArray);


                // Convertir el array en un objeto para acceder a los valores fácilmente
                var formDataObj = {};
                $.each(formDataArray, function(i, field) {
                    formDataObj[field.name] = field.value;
                });

                // Extraer los valores específicos para enviarlos a showNotes
                var identifier = formDataObj.identifier;
                var identifier_type = formDataObj.identifier_type;
                var phase = formDataObj.phase;




                // Enviar los datos a través de AJAX
                $.ajax({
                    type: 'POST',
                    url: './scripts/treatments/add_notes.php', // URL del script PHP que procesa la firma
                    data: formDataArray,
                    success: function(response) {
                        $("#editModal").modal("hide");
                        console.log("object")

                        showNotes(phase, identifier, identifier_type, );


                        showSweetAlert(
                            "Listo!",
                            response.message,
                            "success",
                            1500,
                            true,
                            false
                        )

                    },
                    error: function() {

                    }
                });
            });


            $(document).on('click', '.kv-file-zoom', function() {
                console.log("iamgen ")
                // Obtener el elemento padre más cercano con la clase .file-preview-frame
                var parentElement = $(this).closest('.file-preview-frame');

                // Buscar el elemento de la imagen dentro del elemento padre
                var imageUrl = parentElement.find('img').attr('src');

                console.log(imageUrl);

                //	$('.file-zoom-detail').attr('src', imageUrl);


                // nuevo codigo

                // Modificar la URL para eliminar "thumb"
                var modifiedUrl = imageUrl.replace('/thumb/', '/');

                console.log("nueva foto", modifiedUrl);

                // Actualizar el atributo src de .file-zoom-detail con la URL modificada
                $('.file-zoom-detail').attr('src', modifiedUrl);


            });




            var folders, folders_name, swiper;
            let swiperSlide = "";
            Swal.fire({
                title: "Cargando...",
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            console.log("clini to send",clinic.value)
            let origin = document.getElementById("origin").value;
            console.log("origin para details",origin)

            $.ajax({
                    data: {
                        px_identifier_type: px_identifier_type,
                        identifier: identifier,
                        clinic: origin,

                    },
                    dataType: "json",
                    method: "POST",
                    url: "scripts/treatments/load_details_treatment.php",
                })
                .done(function(response) {
                    console.log("heeey", response);
                    $(".inputfile-container").css("display", "none");
                    let container = $(".swiper-wrapper");

                    response.treatments.forEach(function(treatment) {
                        const treatmentDate = new Date(treatment.date);
                        const formattedDate = `${String(treatmentDate.getDate()).padStart(2, '0')}/${String(treatmentDate.getMonth() + 1).padStart(2, '0')}/${treatmentDate.getFullYear()}`;


                        swiperSlide += `
							<div class="swiper-slide">
									<div class="people__card">
										<div class="people__image">
											<img src="./assets/img/leon-footer.webp" style="width:50%;height:auto;">
										</div>
										<div class="people__info">
												<h3 class="text-white">${treatment.type}</h3>
                                                <p class="mr-4 text-white">${formattedDate} - ${treatment.clinic}</p>
                                                <p class="mr-4 text-white">${treatment.doctor}</p>
										</div>
										<div class="people__btn">
											<a class="view_imgs" data-id="${treatment.id}" data-note="${treatment.notes}" data-date="${treatment.date}" data-clinic="${treatment.clinic}" data-doctor="${treatment.doctor}" data-type="${treatment.type}" data-px_identifier_type="${treatment.px_identifier_type}" data-px_identifier="${treatment.px_identifier}"	 href="#">Ver info</a>
										</div>
									</div>
							</div>`;
                    })
                    swiperSlide += `
					<div class="swiper-slide">
							<div class="people__card">
								<div class="people__image">
										<img src="assets/img/svg/syringe.svg" style="width:70%;height:auto;">
								</div>
								<div class="people__info">
										<h3 class="text-white">Nueva aplicación</h3>
								</div>
								<div class="people__btn">
										<a type="button" class="btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Añadir</a>
								</div>
							</div>
					</div>`;
                    console.log("append")
                    $(".swiper-wrapper").append(swiperSlide);

                    swiper = new Swiper(".swiper", {
                        loop: false,
                        slidesPerView: "auto",
                        centeredSlides: true,
                        observeParents: true,
                        observer: true,
                    });

                    // Agregar un event listener a cada slide
                    swiper.slides.forEach(function(slide, index) {
                        slide.addEventListener("mouseover", function() {
                            $(this).css("cursor", "pointer");
                        });
                        slide.addEventListener("click", function() {
                            swiper.slideTo(index);
                        });
                    });

                    swiper.on("slideChangeTransitionEnd", function() {
                        $(".inputfile-container").fadeOut('slow');
                        $(".people__btn").css("display", "none");

                        $(swiper.el)
                            .find(".swiper-slide-active .people__btn")
                            .css("display", "block");
                    });
                })
                .fail(function(response) {
                    console.log(response);
                }).always(function() {
                    Swal.close();
                });

            $.ajax({
                type: "GET",
                url: `scripts/treatments/fetch_receipts.php?identifier=${identifier}&type_identifier=${px_identifier_type}`, // Ajusta la URL según tu estructura
                dataType: "json",
                success: function(response) {
                    if (response.success && response.receipts.length > 0) {
                        // Recorremos los recibos obtenidos
                        response.receipts.forEach(function(receipt) {
                            // Creamos un elemento HTML para mostrar cada recibo
                            var receiptHtml = `
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">${receipt.name}</h5>
                    <a href="${receipt.url}" target="_blank" class="btn btn-primary">
                        <i class="far fa-file-pdf"></i> Descargar
                    </a>
                </div>
            </div>
        </div>
        `;
                            // Agregamos el elemento al contenedor de recibos
                            $('#receiptsContainer').append(receiptHtml);
                        });
                    } else {
                        // Si no hay recibos disponibles, mostrar mensaje o manejar según necesites
                        $('#receiptsContainer').html('<p>No hay recibos disponibles.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición AJAX:', error);
                    $('#receiptsContainer').html('<p>Ocurrió un error al cargar los recibos.</p>');
                }
            });


            $(document).on("click", ".view_imgs", function(e) {
                e.preventDefault();
                let treatment_id = $(this).data("id");
                let date = $(this).data('date');
                let clinic = <?= (isset($_GET['px']) && $_GET['px'] == "yes") ? 1 : 5; ?>;
                let doctor = $(this).data('doctor');
                let type = $(this).data('type');
                let px_identifier_type = $(this).data('px_identifier_type');
                let px_identifier = $(this).data('px_identifier');
                let noteold = $(this).data('note');
                let step = type;

                console.log("Informacion del tratamiento selecccionado")

                console.log('date:', date);
                console.log('doctor:', doctor);
                // a enviar para fotos
                console.log('treatment_id:', treatment_id);
                console.log('type:', type);
                console.log("px_identifier_type: ", px_identifier_type)
                console.log("px_identifier: ", px_identifier)

                console.log("----")




                Swal.fire({
                    title: "Cargando...",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });


                //      showSign(step, num_med_record);



                $.ajax({

                        data: {
                            px_identifier_type: px_identifier_type,
                            px_identifier: px_identifier,
                            treatment_id: treatment_id,
                            type: type

                        },
                        dataType: "json",
                        method: "POST",
                        url: "scripts/photos/load_photos_treatments.php",
                    })
                    .done(function(response) {
                        console.log("done   ", response)
                        console.log("peticion a load dir hecha correctamente")
                        console.log("---")
                        console.log('treatment_id:', treatment_id);
                        console.log('type:', type);
                        console.log("px_identifier_type: ", px_identifier_type)
                        console.log("px_identifier: ", px_identifier)
                        console.log("notes: ", notes)
                        console.log("---")
                        $(".inputfile-container").fadeIn("slow");
                        $("#fileinput-title").html("Viendo fotos de: " + type);
                        $("#note-title").html("Notas rapidas: " + noteold);
                        $("#file").fileinput("destroy");

                        $("#file").fileinput({
                            allowedFileExtensions: ["jpg", "png", "heic", "jpeg"],
                            language: "es",
                            uploadUrl: `scripts/photos/upload_photos_treatments.php?px_identifier_type=${px_identifier_type}&px_identifier=${px_identifier}&treatment_id=${treatment_id}&type=${type}`, // URL con parámetros de consulta                            showRemove: false,
                            showCancel: false,
                            overwriteInitial: false,
                            initialPreview: response.initialPreview,
                            initialPreviewConfig: response.initialPreviewConfig,
                            initialPreviewAsData: true,
                        });
                        $(".kv-file-rotate,.file-drag-handle").css("display", "none");

                        var buttonHtml = `<button type="button" class="button_ui"  data-step="${step}">
					<span class="button_ui__text">Crear nota</span>
					<span class="button_ui__icon"></span>
				</button>`;

                        showNotes(step, px_identifier, px_identifier_type, );


                        $('#noteextra').empty();
                        $('#noteextra').append(buttonHtml);
                        //$('#extraaa').empty();



                    })
                    .fail(function(response) {
                        console.log(response.responseText);
                        showSweetAlert();
                    })
                    .always(function() {
                        Swal.close();
                    });




            });



            $("#invoice_tr").submit(function(e) {
                e.preventDefault();

                createInvoice();
            });


            function createInvoice() {
                const form = $("#invoice_tr")[0];
                let method = $(form).attr('method');
                let url = $(form).attr('action');
                let formData = $(form).serialize();

                $.ajax({
                        data: formData,
                        cache: false,
                        method: method,
                        url: url,
                        dataType: 'json'
                    })
                    .done(function(response) {
                        // console.log(response);
                        $('#modalReceipt').modal('hide');
                        if (response.success) {
                            console.log("PDF Path:", response.path);

                            console.log(response);
                            Swal.fire({
                                title: "Listo!",
                                text: response.message,
                                icon: "success",
                                showConfirmButton: true,
                                confirmButtonText: "Descargar recibo",
                            }).then(function() {
                                // Abrir el PDF en una nueva pestaña
                                const pdfUrl = response.path; // Asumiendo que response.path contiene la URL completa del PDF

                                window.open(pdfUrl, '_blank');

                                // Recargar la página después de un breve retraso para asegurar que la nueva pestaña se haya abierto
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            });

                            // Puedes agregar lógica aquí si necesitas manejar la respuesta de éxito.
                        } else {
                            console.log("Error:", response.error);
                            console.log(response);
                            Swal.fire({
                                title: "Ocurrió un error",
                                text: "Por favor, contacta a administración",
                                icon: "error",
                                timer: 1700,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            });
                            // Manejar el error aquí si es necesario.
                        }
                    })
                    .fail(function(response) {
                        console.log("Fail response:", response);
                        Swal.fire({
                            title: "Ocurrió un error",
                            text: "Por favor, contacta a administración",
                            icon: "error",
                            timer: 1700,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    });
            }

            function loadInvoicePDF(url) {
                document.getElementById('pdfInvoiceViewer').src = url;
            }


            $("#formNewTreatment").submit(function(e) {
                e.preventDefault();


                if (this.checkValidity()) {
                    $(".submit-form").attr('disabled', 'true');
                    const formData = $(this).serialize();
                    console.log(formData)
                    $.ajax({
                            data: formData,
                            cache: false,
                            method: "POST",
                            url: "./scripts/treatments/add_treatment.php",
                            dataType: 'json'
                        })
                        .done(function(response) {
                            console.log("si")
                            if (response.success) {
                                console.log(response);
                                Swal.fire({
                                    title: "Listo!",
                                    text: response.message,
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                console.log(response);
                                Swal.fire({
                                    title: "Ocurrió un error",
                                    text: "Por favor, contacta a administración",
                                    icon: "error",
                                    timer: 1700,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                });
                            }
                        })
                        .fail(function(response) {
                            console.log("no")
                            console.log(response);
                            showSweetAlert();
                        })

                    $(".submit-form").attr('disabled', 'true');
                }
            })


        });


        $(document).on('click', '.button_ui', function(e) {
            $("#editModal").modal("show");

            /*  $("#num_med_record_form").val(num_med_record);
            //$("#note").val("");
            const user_id = localStorage.getItem("user_id")
            
           
 */

            let step = $(this).data('step');
            console.log(step)
            console.log(user_id.value)
            $("#author").val(user_id.value);




            $("#phase").val(step);


            // Función para formatear la fecha en YYYY-MM-DD
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Meses empiezan en 0
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Establece la fecha actual como valor por defecto

            const today = new Date();
            document.getElementById('date_note').value = formatDate(today);

        });

        function showNotes(phase, identifier, type_identifier) {
            $.ajax({
                url: './scripts/treatments/showNotes.php',
                type: 'POST',
                data: {
                    type_identifier: type_identifier,
                    identifier: identifier,
                    phase: phase,
                },
                dataType: 'json',
                success: function(response) {
                    // Maneja la respuesta del servidor
                    if (response.status === 'success') {
                        console.log(response)
                        // Vaciamos el contenido de la div
                        $("#note").val("");
                        $('#extraaa').empty();

                        if (response.status === 'success') {
                            response.data.forEach(function(item) {

                                var fullNote = item.note;
                                var shortNote = fullNote.length > 30 ? fullNote.substring(0, 30) + '...' : fullNote;






                                // Construir el HTML con la información recibida
                                var cardHtml = `
			<div class="extras_medical">
					
				<div class="notes_medical">
				
					<div class="card_medical" note-id="${item.id}" data-step="${item.phase}">
					 <div class="card_medical__actions">
                            <img src="./assets/img/svg/close.svg" alt="close icon" class="less-button">
                            <img src="./assets/img/svg/delete.svg" alt="delete note" class="delete-note">
                        </div>
					
						<h3 class="card_medical__title"></h3>
						                      <p class="card_medical__content" data-fullnote="${fullNote}" data-shortnote="${shortNote}">
                        ${shortNote}
                    </p>
                    </p>
						<div class="card_medica_footer">
							<div class="card_medical__date">
								${item.date}
							</div>
							<div class="card_medical__author">
								${item.author_name}
							</div>
						</div>
						<div class="card_medical__arrow">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="15" width="15">
														<path fill="#fff" d="M13.4697 17.9697C13.1768 18.2626 13.1768 18.7374 13.4697 19.0303C13.7626 19.3232 14.2374 19.3232 14.5303 19.0303L20.3232 13.2374C21.0066 12.554 21.0066 11.446 20.3232 10.7626L14.5303 4.96967C14.2374 4.67678 13.7626 4.67678 13.4697 4.96967C13.1768 5.26256 13.1768 5.73744 13.4697 6.03033L18.6893 11.25H4C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75H18.6893L13.4697 17.9697Z"></path>
													</svg>
						</div>
					</div>
				</div>
				
			</div>
		`;

                                // Añadir el HTML a la div
                                $('#extraaa').append(cardHtml);
                            });
                        }


                    } else {
                        $('#extraaa').empty();
                        console.log("error", response)


                    }
                },
                error: function(xhr, status, error) {
                    // Maneja los errores de la solicitud AJAX
                    $('#result').html('<p>Error en la solicitud: ' + error + '</p>');
                }
            });
        }

        $(document).on('click', '.less-button', function() {
            console.log("esconder");
            var cardContent = $(this).closest('.card_medical').find('.card_medical__content');
            var shortNote = cardContent.data('shortnote'); // Obtener el texto corto guardado
            cardContent.text(shortNote); // Restaurar el texto corto
            //	$(this).hide(); // Ocultar el botón "less"
        });


        $(document).on('click', '.card_medical__content', function() {
            console.log("mostrar");
            var fullNote = $(this).data('fullnote'); // Obtener el texto completo guardado
            $(this).text(fullNote); // Mostrar el texto completo
            $(this).siblings('.less-button').show(); // Mostrar el botón "less"
        });

        $(document).on('click', '.delete-note', function() {
            var $card = $(this).closest('.card_medical');
            var noteId = $card.attr('note-id'); // Obtener el ID de la nota
            var step = $card.data('step'); // Obtener el valor del atributo data-step
            console.log("borrar", noteId, "step", step);

            $.ajax({
                type: 'POST',
                url: './scripts/treatments/delete_note.php',
                data: {
                    id: noteId,
                },
                success: function(response) {
                    // Parsear la respuesta JSON
                    var jsonResponse = JSON.parse(response);

                    if (jsonResponse.status === 'success') {
                        // Eliminar el elemento del DOM
                        $card.closest('.extras_medical').remove();

                        // Mostrar alerta y actualizar la vista
                        showSweetAlert(
                            "Listo!",
                            jsonResponse.message,
                            "success",
                            1500,
                            true,
                            false
                        );

                        //showNotes(step); // Llamada a la función para mostrar las notas, pasa el step si es necesario
                    } else {
                        alert('Hubo un error al eliminar la nota: ' + jsonResponse.message);
                    }
                },
                error: function() {
                    alert('Hubo un error al procesar la solicitud.');
                }
            });
        });

        function showSign(step, num_med_record) {
            $.ajax({
                url: './scripts/procedures/search_sign.php', // Cambia esto a la ruta del script PHP que manejará la petición
                type: 'POST',
                dataType: 'json',
                data: {
                    fase: step,
                    num_med: num_med_record
                },
                success: function(response) {
                    if (response.success) {
                        // Manejar el éxito
                        console.log("Firma encontrada: ");
                        // Limpiar el div antes de agregar la imagen
                        $('#sign_step').empty();

                        // Crear el párrafo "Firma:"
                        var pFirma = $('<h4>').text('Firma:').css('margin-top', '1rem');

                        // Crear una etiqueta de imagen y agregarla al div
                        var img = $('<img>').attr('src', 'data:image/png;base64,' + response.url);

                        // Añadir el párrafo y la imagen al div
                        $('#sign_step').append(pFirma).append(img);

                    } else {
                        var firma_canva = `<p>Firma:</p>
    <canvas id="canvas" width="400" height="200" style="border: 1px solid black;"></canvas>
    <br>
    <button class="btn btn-danger" id="btnLimpiar">Limpiar</button>

    <button class="btn btn-light" id="btnCrearPdf">Guardar firma</button>
	
        <input type="checkbox" name="" id="checkboxVideollamada">
				<span> Videollamada </span>
	`;

                        $('#sign_step').empty();
                        $('#sign_step').append(firma_canva);
                        //$('#extraaa').empty();

                        const $botonCrearPdf = document.querySelector("#btnCrearPdf");
                        const $canvas = document.querySelector("#canvas"),
                            $btnLimpiar = document.querySelector("#btnLimpiar"),
                            $id = document.querySelector("#id"),
                            $nombre = document.querySelector("#nombre"),
                            $apellido = document.querySelector("#apellido"),
                            $direccion = document.querySelector("#direccion");
                        const contexto = $canvas.getContext("2d");
                        const COLOR_PINCEL = "black";
                        const COLOR_FONDO = "white";
                        const GROSOR = 2;

                        let xAnterior = 0,
                            yAnterior = 0,
                            xActual = 0,
                            yActual = 0;
                        let haComenzadoDibujo = false;

                        const obtenerXReal = (clientX) => clientX - $canvas.getBoundingClientRect().left;
                        const obtenerYReal = (clientY) => clientY - $canvas.getBoundingClientRect().top;

                        const limpiarCanvas = () => {
                            contexto.fillStyle = COLOR_FONDO;
                            contexto.fillRect(0, 0, $canvas.width, $canvas.height);
                        };

                        limpiarCanvas();
                        $btnLimpiar.onclick = limpiarCanvas;

                        const onClicOToqueIniciado = evento => {
                            xAnterior = xActual;
                            yAnterior = yActual;
                            xActual = obtenerXReal(evento.clientX);
                            yActual = obtenerYReal(evento.clientY);
                            contexto.beginPath();
                            contexto.fillStyle = COLOR_PINCEL;
                            contexto.fillRect(xActual, yActual, GROSOR, GROSOR);
                            contexto.closePath();
                            haComenzadoDibujo = true;
                        };

                        const onMouseODedoMovido = evento => {
                            evento.preventDefault();
                            if (!haComenzadoDibujo) return;

                            let target = evento;
                            if (evento.type.includes("touch")) {
                                target = evento.touches[0];
                            }
                            xAnterior = xActual;
                            yAnterior = yActual;
                            xActual = obtenerXReal(target.clientX);
                            yActual = obtenerYReal(target.clientY);
                            contexto.beginPath();
                            contexto.moveTo(xAnterior, yAnterior);
                            contexto.lineTo(xActual, yActual);
                            contexto.strokeStyle = COLOR_PINCEL;
                            contexto.lineWidth = GROSOR;
                            contexto.stroke();
                            contexto.closePath();
                        };

                        const onMouseODedoLevantado = () => {
                            haComenzadoDibujo = false;
                        };

                        ["mousedown", "touchstart"].forEach(nombreDeEvento => {
                            $canvas.addEventListener(nombreDeEvento, onClicOToqueIniciado);
                        });

                        ["mousemove", "touchmove"].forEach(nombreDeEvento => {
                            $canvas.addEventListener(nombreDeEvento, onMouseODedoMovido);
                        });

                        ["mouseup", "touchend"].forEach(nombreDeEvento => {
                            $canvas.addEventListener(nombreDeEvento, onMouseODedoLevantado);
                        });

                        $botonCrearPdf.addEventListener("click", async () => {
                            // Verificar que todos los campos estén llenos


                            // Convertir el contenido del canvas a base64
                            const imagenBase64 = $canvas.toDataURL("image/png");

                            // Comprobar si el canvas no está vacío
                            const canvasVacio = imagenBase64 === document.createElement('canvas').toDataURL("image/png");
                            if (canvasVacio) {
                                showSweetAlert(
                                    "Advertencia!",
                                    response.message,
                                    "warning",
                                    1500,
                                    true,
                                    false
                                )
                                return;
                            }
                            console.log("num med", num_med_record)
                            console.log("step", step)


                            // Crear un objeto FormData y agregar los campos
                            const formData = new FormData();

                            formData.append("firma", imagenBase64);
                            formData.append("fase", step);
                            formData.append("num_med", num_med_record);

                            // Serializar los datos para imprimir en consola
                            const datosSerializados = {

                                //firma: imagenBase64,
                                fase: step,
                                num_med: num_med_record
                            };

                            // Mostrar en consola los datos serializados y la imagen en base64
                            console.log("Datos a enviar:", datosSerializados);

                            // Mostrar un mensaje de confirmación antes de enviar
                            const confirmarEnvio = confirm("¿Estás seguro de que deseas enviar la información?");
                            if (!confirmarEnvio) {
                                return;
                            }

                            // Enviar los datos al servidor
                            try {
                                $botonCrearPdf.disabled = true;
                                $botonCrearPdf.textContent = "Enviando...";

                                const respuestaHttp = await fetch("./scripts/procedures/add_signature.php", {
                                    body: formData,
                                    method: "POST",
                                });

                                if (!respuestaHttp.ok) {
                                    throw new Error(`HTTP error! status: ${respuestaHttp.status}`);
                                }

                                const respuestaDelServidor = await respuestaHttp.text();
                                console.log("Respuesta del servidor:", respuestaDelServidor);
                                console.log(step, num_med_record)
                                showSign(step, num_med_record);
                                showSweetAlert(
                                    "Listo!",
                                    "Firma agregada",
                                    "success",
                                    1500,
                                    true,
                                    false
                                )


                            } catch (error) {
                                console.error("Error al enviar los datos:", error);
                                showSweetAlert()
                            } finally {
                                $botonCrearPdf.disabled = false;
                                $botonCrearPdf.textContent = "Guardar firmar";
                            }
                        });





                        // Aquí seleccionas el checkbox después de que ha sido añadido al DOM
                        const checkboxVideollamada = document.querySelector('#checkboxVideollamada');

                        if (checkboxVideollamada) {
                            checkboxVideollamada.addEventListener('change', function() {
                                if (this.checked) {
                                    // Añade el texto "Videollamada" al canvas cuando se marca el checkbox
                                    contexto.font = "20px Arial";
                                    contexto.fillStyle = "black";
                                    contexto.fillText("Videollamada", 10, 30);
                                } else {
                                    limpiarCanvas(); // Limpiar el canvas si se desmarca el checkbox
                                }
                            });
                        } else {
                            console.error("Checkbox Videollamada no encontrado en el DOM.");
                        }









                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la petición AJAX: " + status + " - " + error);
                }
            });
        }
    </script>
</body>

</html>