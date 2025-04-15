<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

session_start();
require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";
require_once __DIR__ . "/scripts/common/utilities.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Inicio | ERP | Los Reyes del Injerto</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

	<!-- Toastr -->
	<link rel="stylesheet" href="assets/plugins//toastr/toastr.css">

	<!-- SWIPER CSS -->
	<link rel="stylesheet" href="assets/plugins/swiper-slider/swiper.min.css" />
	<link rel="stylesheet" href="assets/plugins/swiper-slider/style.css" />

	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>

<body class="mini-sidebar">
	<div class="main-wrapper">
		<?php
		require __DIR__ . '/templates/header.php';
		require __DIR__ . '/templates/sidebar.php';
		?>
		<div class="page-wrapper">
			<div class="content">

				<!-- Page Header -->
				<div class="page-header">
					<div class="row align-items-center"> <!-- Alineación vertical -->
						<div class="col-sm-6"> <!-- Ajusta el ancho según lo necesites -->
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.php">Inicio </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Dashboard</li>
							</ul>
						</div>
						<div class="col-sm-6 text-end"> <!-- Alineación del botón a la derecha -->
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
					</div>
				</div>

				<!-- /Page Header -->
				<h1 id="ds">Dashboard</h1>
				<div class="row">
					<div class="blog-slider">
						<div class="blog-slider__wrp swiper-wrapper" id="cards">
						</div>
						<div class="blog-slider__pagination"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="sidebar-overlay"></div>
		<!-- jQuery -->
		<script src="assets/js/jquery.min.js"></script>
		<!-- Bootstrap Core JS -->
		<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>
		<!-- Feather Js -->
		<script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>
		<!-- Slimscroll -->
		<script src="assets/js/jquery.slimscroll.js"></script>
		<!-- Sweet Alert-->
		<script src="assets/plugins/sweetalert/sweetalert.11.10.min.js"></script>
		<!-- Toastr -->
		<script src="assets/plugins/toastr/toastr.min.js"></script>
		<script src="assets/plugins/toastr/toastr.js"></script>
		<!-- Swiper Slider -->
		<script src="assets/plugins/swiper-slider/swiper.4.3.5.min.js"></script>
		<!-- Custom JS -->
		<script src="assets/js/app.js?1.0"></script>
		<!-- <script src="assets/js/toast_appointment.js"></script> -->
		<script>
			$(document).ready(function() {
				// Imprimir en la consola los valores almacenados
				console.log('user_id almacenado en localStorage:', localStorage.getItem('user_id'));
				console.log('user_name almacenado en localStorage:', localStorage.getItem('user_name'));
				console.log('clinica almacenada en localStorage:', localStorage.getItem('clinica'));

				var clinica = localStorage.getItem('clinica');
				if (clinica) {
					// Actualizar el texto del botón con la clínica guardada
					$('#clinicButton').text(clinica);

					// Marcar como activo el item correspondiente en el dropdown
					$('.item_clinic').each(function() {
						if ($(this).data('clinic') === clinica) {
							$(this).addClass('active'); // Agregar clase active al elemento correspondiente
						} else {
							$(this).removeClass('active'); // Quitar clase active de otros elementos
						}
					});
				}

				loadPatientsByClinic(clinica)

				// Función para cargar pacientes por clínica
				function loadPatientsByClinic(clinica) {
					$.ajax({
							url: "scripts/dashboard/load_patients.php",
							method: "POST",
							dataType: "json",
							data: {
								clinic: clinica // Inyecta la clínica en el cuerpo de la solicitud
							}
						})
						.done(function(response) {
							console.log("datos de dashboard: ", response);
							if (response.success) {
								$("#cards").html(response.cards);
								loadSwiper('.blog-slider');
							} else {
								$("#cards").html(`
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <h4>No hay procedimientos programados para hoy</h4>
                    </div>
                `);
							}
						})
						.fail(function(response) {
							console.log(response);
							showSweetAlert();
						});
				}

				$(document).on('click', '.item_clinic', function(e) {
					e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
					const clinica = $(this).data('clinic'); // Obtener el valor de la clínica

					// Actualizar el texto del botón con la clínica seleccionada
					$('#clinicButton').text(clinica);

					// Llama a la función para cargar pacientes con el valor de la clínica seleccionada
					loadPatientsByClinic(clinica);

					// Remover la clase 'active' de todos los elementos y añadirla al seleccionado
					$('.item_clinic').removeClass('active'); // Quitar la clase active de todos
					$(this).addClass('active'); // Agregar clase active al elemento seleccionado
				});

			});

			function showSweetAlert(title, text, icon, timer, timerProgressBar, showConfirmButton) {
				return Swal.fire({
					title: title || "Error",
					text: text || "Contacta a administración",
					icon: icon || "error",
					timer: timer || 2500,
					timerProgressBar: timerProgressBar || true,
					showConfirmButton: showConfirmButton || false,
				});
			}

			function loadSwiper(container) {
				new Swiper(container, {
					spaceBetween: 30,
					effect: 'fade',
					loop: true,
					mousewheel: {
						invert: false,
					},
					// autoHeight: true,
					pagination: {
						el: '.blog-slider__pagination',
						clickable: true,
					}
				});
			}
		</script>
</body>

</html>