<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

$lead_id = $_GET['id'];

if (isset($_GET['px']) && $_GET['px'] == 1)
    $sql_row  = "SELECT ep.num_med_record, CONCAT(sla.first_name, ' ',sla.last_name) AS name FROM enf_procedures ep INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id WHERE ep.lead_id = ?;";
else
    $sql_row = "SELECT t.id, t.name, t.num_med_record FROM enf_treatments t WHERE t.id = ?;";

$sql = $conn->prepare($sql_row);
$sql->bind_param("i", $lead_id);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_object();
}
$num_med_record = $row->num_med_record;
$sql->close();

$sql_row = "SELECT type, DATE_FORMAT(date, '%d.%m.%Y') AS date FROM enf_treatments_appointments WHERE num_med_record = ? AND date = (SELECT MAX(date) FROM enf_treatments_appointments WHERE num_med_record = ?);";

$sql = $conn->prepare($sql_row);
$sql->bind_param("ii", $num_med_record, $num_med_record);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $last_treatment = $result->fetch_object();
    $type = "Último tratamiento: <br>{$last_treatment->type}";
    $date = "Aplicado el:<br>{$last_treatment->date}";
} else {
    $type = 'Sin aplicaciones registradas';
    $date = ':(';
}
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
                                <li class="breadcrumb-item "><a href="view_procedures.php">Tratamientos</a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right" style="color:#000;"></i></li>
                                <li class="breadcrumb-item"><a href="procedure_photos.php?id=<?= $_GET['id']; ?>">Revisiones</a></li>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mx-auto col-12 col-md-4">
                        <div class="card text-white bg-secondary ">
                            <div class="card-body text-center">
                                <h2 style='color:#e0ac44;'><?= $row->name; ?></h2>
                                <p><span style='font-size:20px;' class='badge bg-secondary'>#<?= $row->num_med_record; ?></span></p>
                                <p><span style='font-size:20px;' class='badge bg-primary'><?= $type; ?></span></p>
                                <p style='font-size:20px;'><strong><?= $date; ?></strong></p>
                            </div>
                            <input type="hidden" id="clinic" name="clinic" value="1">
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="text-center col-md-12 col-xs-12 order-md-1 order-last">
                                        <input type="hidden" value="<?= $_GET['id']; ?>" id="px_sales_id">
                                        <input type="hidden" id="px_num_med_record">
                                        <input type="hidden" id="clinic">
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
                                <h4 style='color:#e0ac44;' id="fileinput-title"></h4>

                                <div class="inputfile-container">
                                    <input type="file" id="file" name="file[]" multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>>
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
                    <form id="formNewTreatment" action="scripts/procedures/treatments/add.php" method="POST">
                        <input type="hidden" id="num_med_record" name="num_med_record" value="<?= $row->num_med_record; ?>">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-block local-forms">
                                    <label>Fecha<span class="login-danger">*</span></label>
                                    <input class="form-control" type="date" name="date" required>
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
                                    <select class="form-control" id="clinic" name="clinic">
                                        <option value="" selected disabled>Selecciona</option>
                                        <option value="Santa Fe">Santa Fe</option>
                                        <option value="Pedregal">Pedregal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-block local-forms">
                                    <label>Médico responsable<span class="login-danger">*</span></label>
                                    <select class="form-control" id="doctor" name="doctor">
                                        <option value="" selected disabled>Selecciona</option>
                                        <option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
                                        <option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
                                        <option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
                                        <option value="Dra. Ana Karen">Dra. Ana Karen</option>
                                        <option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input-block local-forms">
                                    <label>Notas<span class="login-danger">*</span></label>
                                    <textarea class="form-control" id="notes" name="notes" rows=3></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Añadir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
    <script>
        const num_med_record = $("#num_med_record").val();

        $(document).ready(function() {
            var folders, folders_name, swiper;
            let swiperSlide = "";
            Swal.fire({
                title: "Cargando...",
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            $.ajax({
                    data: {
                        num_med_record: num_med_record
                    },
                    dataType: "json",
                    method: "POST",
                    url: "scripts/procedures/treatments/load_single.php",
                })
                .done(function(response) {
                    console.log(response);
                    $(".inputfile-container").css("display", "none");
                    let container = $(".swiper-wrapper");
                    response.treatments.forEach(function(treatment) {
                        swiperSlide += `
							<div class="swiper-slide">
									<div class="people__card">
										<div class="people__image">
											<img src="https://www.losreyesdelinjerto.com/assets/img/leon-footer.webp" style="width:50%;height:auto;">
										</div>
										<div class="people__info">
												<h3 class="text-white">${treatment.type}</h3>
										</div>
										<div class="people__btn">
											<a class="view_imgs" data-id="${treatment.id}" data-date="${treatment.date}" data-clinic="${treatment.clinic}" data-doctor="${treatment.doctor}" data-type="${treatment.type}" data-notes="${treatment.notes}" href="#">Ver info</a>
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
                        //$(".inputfile-container").fadeOut('slow');
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

            $("#formNewTreatment").submit(function(e) {
                e.preventDefault();

                if (this.checkValidity()) {
                    $(".submit-form").attr('disabled', 'true');
                    const method = $(this).attr('method');
                    const url = $(this).attr('action');
                    const formData = $(this).serialize();
                    $.ajax({
                            data: formData,
                            cache: false,
                            method: method,
                            url: url,
                            dataType: 'json'
                        })
                        .done(function(response) {
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
                            console.log(response);
                            showSweetAlert();
                        })

                    $(".submit-form").attr('disabled', 'true');
                }
            })
        });
        $(document).on("click", ".view_imgs", function(e) {
            e.preventDefault();

            let treatment_id = $(this).data("id");
            let date = $(this).data('date');
            let clinic = <?= (isset($_GET['px']) && $_GET['px'] == "yes") ? 1 : 5; ?>;
            let doctor = $(this).data('doctor');
            let type = $(this).data('type');
            let notes = $(this).data('notes');

            Swal.fire({
                title: "Cargando...",
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            $.ajax({
                    data: {
                        num_med_record: num_med_record,
                        step: treatment_id,
                        type: "px_treatment"
                    },
                    dataType: "json",
                    method: "POST",
                    url: "scripts/photos/load_dir.php",
                })
                .done(function(response) {
                    $(".inputfile-container").fadeIn("slow");
                    $("#fileinput-title").html("Viendo fotos de: " + type);
                    $("#file").fileinput("destroy");

                    $("#file").fileinput({
                        allowedFileExtensions: ["jpg", "png", "heic", "jpeg"],
                        language: "es",
                        uploadUrl: `scripts/photos/upload.php?num_med_record=${num_med_record}&step=${treatment_id}&type=px_treatment`,
                        showRemove: false,
                        showCancel: false,
                        overwriteInitial: false,
                        initialPreview: response.initialPreview,
                        initialPreviewConfig: response.initialPreviewConfig,
                        initialPreviewAsData: false,
                    });
                    $(".kv-file-rotate,.file-drag-handle").css("display", "none");
                })
                .fail(function(response) {
                    console.log(response.responseText);
                    showSweetAlert();
                })
                .always(function() {
                    Swal.close();
                });
        });
    </script>
</body>

</html>