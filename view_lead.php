<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Viendo Lead | Reyes del Injerto CRM</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">
	<!-- Fontawesome CSS -->
	<!-- <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css"> -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
	<!-- Select2 CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
	<!-- Datatables CSS -->
	<link rel="stylesheet" href="assets/plugins/datatables/css/datatables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.4.0/css/scroller.bootstrap5.min.css">
	<link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">
	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/plugins/filemanager/style.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.5.0/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" type="text/css" href="assets/css/lead_styles.css">
	<!-- <link href="assets/plugins/toastify/toastify.css" rel="stylesheet"> -->
	<link href="assets/plugins/timeline/style.css" media="all" rel="stylesheet" type="text/css" />
	<!-- Profile Card-->
	<link href="assets/plugins/profile-card/style.css" rel="stylesheet" type="text/css" media="all" />

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">


</head>

<body class="mini-sidebar">
	<div class="main-wrapper">
		<?php
		require 'templates/header.php';
		require 'templates/sidebar.php';
		?>
	</div>
	<div class="page-wrapper">

		<div class="content">
			<!-- Page Header -->
			<div class="page-header">
				<div class="row">
					<div class="col-sm-12">
						<?php if (!isset($_GET['client'])) { ?> <!-- Breadcumbs si viene de Leads-->
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.php">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item "><a href="#">Ventas</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item"><a href="view_leads.php">Leads</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Ver Lead</li>
							</ul>
						<?php } else { ?>
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.php">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item "><a href="view_clients.php">Clientes</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Ver Cliente</li>
							</ul>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-12 col-md-6 d-flex align-items-center">

									<?php if (!isset($_GET['client'])) { ?> <!-- Info si viene de Leads-->
										<div class="names-profiles">
											<h2 id="lead_fullname"></h2>
											<a style="color:#000;font-size:15px;" class="custom-badge rounded-pill bg-warning" href="#" id="lead_stage"></a>
											<a style="color:#000;font-size:15px;" class="custom-badge rounded-pill bg-primary" href="#" id="lead_qualif"></a>
										</div>
									<?php } else { ?> <!-- Info si viene de Clientes -->
										<div class="names-profiles">
											<h2 id="client_fullname"></h2>
											<a style="color:#000;font-size:15px;" class="custom-badge rounded-pill bg-warning" href="#" id="client_
											procedure_type"></a>
											<a style="color:#fff;font-size:15px;" class="custom-badge rounded-pill bg-info" href="#">Numero de exp: #<span id="client_num_med_record">Sin asignar</span></a>
											<a style="color:#fff;font-size:15px;" class="custom-badge rounded-pill bg-dark" href="#" id="client_procedure_date"></a>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-lg-12">
						<div class="invoices-main-tabs">
							<div class="row align-items-center">
								<div class="col-12">
									<div class="invoices-tabs">
										<ul>
											<!-- Si viene de Leads. -->
											<?php if (!isset($_GET['client'])) { ?>
												<li data-tab="info"><a href="#" class="active" data-tab="info"><i class="fa fa-info-circle"></i>Seguimiento del Lead</a></li>
											<?php } ?>
											<li data-tab="assessment"><a id="test1" href="#" data-tab="assessment" style="display:none;"><i class="fa-brands fa-weixin"></i> Valoración</a></li>
											<li data-tab="profile"><a href="#" data-tab="profile" style="display:none;"><i class="fa-solid fa-person"></i> Perfil del Px</a></li>
											<li data-tab="invoices"><a href="#" data-tab="invoices" style="display:none;"><i class="fa-solid fa-file-invoice-dollar"></i> Generar Recibo</a></li>
											<li data-tab="payments"><a href="#" data-tab="payments" style="display:none;"><i class="fa-solid fa-cart-shopping"></i> Historial de Transacciones</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card mt-4">
					<div class="row" id="other-tabs">
						<div class="col-lg-12">
							<div class="card">
								<div class="card-body">
									<?php
									$tabs = ["info", "assessment", "profile", "invoices", "payments"];
									foreach ($tabs as $tab) {
										require_once __DIR__ . "/templates/view_lead/tab_{$tab}.php";
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				

			</div>
			<!-- Modal -->
			<!-- Task Modal -->

			<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalLabel">Registrar nueva tarea.</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
						</div>
						<div class="modal-body">
							<form id="new_task" action="scripts/sales/add_lead_task.php" method="POST">
								<input type="hidden" id="lead_id" name="lead_id" value="<?= $_GET['id']; ?>">
								<input type="hidden" name="seller_id" id="seller_id" value="">
								<script>
									document.addEventListener('DOMContentLoaded', (event) => {
										// Obtener el valor de user_id desde localStorage
										const userIdSeller = localStorage.getItem('user_id');

										// Obtener el input hidden por su ID
										const sellerIdInput = document.getElementById('seller_id');
										

									
										// Asignar el valor de user_id al atributo value del input hidden
										sellerIdInput.value = userIdSeller;
									});
								</script>

								<div class="row">
									<div class="col-12">
										<div class="input-block local-forms">
											<label>Asunto <span class="login-danger">*</span></label>
											<select class="form-control tagging" name="subject" id="subject" required>
												<option value="Enviar costo">Enviar Costo</option>
												<option value="Enviar Mensaje">Enviar Mensaje</option>
												<option value="Enviar casos de éxito">Enviar casos de éxito</option>
												<option value="Llamada">Llamada</option>
												<option value="Agendar">Agendar</option>
												<option value="Recordar Valoracin">Recordar Valoración</option>
												<option value="Valoracion"> Valoración</option>
											</select>
										</div>
									</div>
									<div class="col-12">
										<div class="input-block local-forms">
											<label>Comentarios</label>
											<textarea name="comments" id="comments" class="form-control" rows=4></textarea>
										</div>
									</div>
									<div class="col-12">
										<div class="input-block local-forms">
											<label>Asignado a <span class="login-danger">*</span></label>
											<input type="text" name="seller" id="seller" class="form-control" value="<?= $_SESSION['user_name']; ?>" readonly>
										</div>
									</div>
									<div class="col-12">
										<div class="input-block local-forms">
											<label>Fecha y hora de vencimiento:</label>
											<input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" cols=5 min="<?php echo date('Y-m-d\TH:i'); ?>" required>
										</div>
									</div>

									<div class="col-12">
										<strong>Recordarme:</strong><br>
										<label class="no-style">
											<input type="checkbox" name="reminder[]" value="at_the_moment" checked disabled>En el momento</input>
										</label><br>
										<label class="no-style">
											<input type="checkbox" name="reminder[]" id="in_the_morning" value="in_the_morning">El mismo día a las...</input>
											<input type="time" id="time" name="time" class="form-control" value="09:00">
										</label><br>
										<label class="no-style">
											<input type="checkbox" name="reminder[]" value="one_day_before">Un día antes</input>
										</label><br>
										<label class="no-style">
											<input type="checkbox" name="reminder[]" value="one_week_before">Una semana antes</input>
										</label><br>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
									<button type="submit" class="btn btn-primary">Agregar Tarea</button>
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
		<!-- Slimscroll -->
		<script src="assets/js/jquery.slimscroll.js"></script>
		<!-- Select2 Js -->
		<script src="assets/js/select2.min.js"></script>
		<!-- Moment -->
		<script src="assets/plugins/moment/moment.min.js"></script>
		<!-- Kartik FileInput-->
		<script src="assets/plugins/fileinput/fileinput.js"></script>
		<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/locales/es.js" type="text/javascript"></script>
		<!-- SweetAlert -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<!-- DataTables -->
		<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>

		<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
		<script src="assets/plugins/toastify/toastify.js" type="text/javascript"></script>
		<script src="assets/js/view_lead.js" type="text/javascript"></script>
		<!-- <script src="assets/plugins/profile-card/script.js"></script> -->
		<?php if (isset($_GET['client']) && $_GET['client'] === "yes") { ?>
			<script>
				$('a[data-tab="profile"]').first().click();
			</script>
		<?php } ?>

		<script>
			// Obtener el valor de localStorage
			var userId = localStorage.getItem('user_id');

			// Asignar el valor al campo de texto
			document.getElementById('user_id').value = userId;
			console.log("user_id", userId)


			$(document).on('click', '.kv-file-zoom', function() {
				console.log("iamgen ")
				// Obtener el elemento padre más cercano con la clase .file-preview-frame
				var parentElement = $(this).closest('.file-preview-frame');

				// Buscar el elemento de la imagen dentro del elemento padre
				var imageUrl = parentElement.find('img').attr('src');

				console.log(imageUrl);

				$('.file-zoom-detail').attr('src', imageUrl);


				


			});
		</script>

</body>

</html>