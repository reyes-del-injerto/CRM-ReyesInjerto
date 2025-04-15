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
if ($_SESSION['user_id'] != 1 && $_SESSION['user_id'] != 20 && $_SESSION['user_id'] != 41  && $_SESSION['user_id'] != 18 && $_SESSION['user_id'] != 11) {
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
	<title>Admin RDI CDMX</title>
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
		@media (max-width: 540px) {
			.contedor_cards_principales {


				flex-direction: column;
				gap: 1rem;
				;

			}

			.container_table_gastos {
				overflow-x: scroll;
			}

		}
	</style>





	<style>
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
			padding: 1.4rem 2rem;

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
						<div class="col-sm-12">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html">Inicio </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Panel de administracion</li>

							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->




			</div>
			<div id="general_info" class="row">
				<div class="col-sm-12">
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="#" data="gral">Vista general</a>
						</li>
						<!-- <li class="nav-item">
							<a class="nav-link" href="#" data="gastos">Inf. de gastos</a>
						</li> -->
						<li class="nav-item">
							<a class="nav-link" href="#" data="esp">Inf. de especialistas</a>
						</li>

						<!-- <li class="nav-item">
							<a class="nav-link" href="#" data="vacaciones">Inf. de vacaciones</a>
						</li> -->


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
						<h5 class="modal-title" id="exampleModalLabel">Agregar nueva categoría</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="formAddCategory" method="post">
							<!-- Campo de texto para el nombre -->
							<label for="nombre">Nombre de la categoría:</label>
							<input type="text" class="form-control" id="cat_name" name="cat_name" required>
							<br>

							<!-- Campo de email -->
							<label for="email">Monto:</label>
							<input type="number" step="0.01" min=0 class="form-control" id="cat_amount" name="cat_amount" required>
							<br>

					</div>
					<div class=" modal-footer">
						<button type="submit" class="btn btn-primary">Guardar</button>
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




			showTab("gral");




			$('.nav-link').on('click', function(event) {
				event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace

				// Quitar la clase 'active' de todas las pestañas
				$('.nav-link').removeClass('active');

				// Añadir la clase 'active' a la pestaña clickeada
				$(this).addClass('active');

				// Obtener el texto de la pestaña activa
				var activeTabText = $(this).text().trim();

				// Imprimir en la consola el nombre de la pestaña activa
				//console.log('Pestaña activa:', activeTabText);

				var activeTabData = $(this).attr('data'); // También puedes usar $(this).attr('data') si el atributo es diferente

				showTab(activeTabData)




			});

			function showTab(tab) {
				console.log('Pestaña llamada:', tab);

				$.ajax({
					url: `templates/admin_tabs/info_${tab}.php?period=first`, // URL del endpoint interpolada

					method: 'GET',
					success: function(response) {
						$('#view').html(response); // Actualiza el contenido del div con la respuesta



					},
					error: function(xhr, status, error) {
						console.error('Error en la petición:', status, error);
					}
				});
			}




		});
	</script>
</body>

</html>