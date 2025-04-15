<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

//! Falta añadir permisos

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Nuevo Lead | ERP | Los Reyes del Injerto</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>

<body class="mini-sidebar">
	<div class="main-wrapper">
		<?php
		require __DIR__ . '/templates/header.php';
		require __DIR__ . '/templates/sidebar.php';
		?>
	</div>
	<div class="page-wrapper">
		<div class="content">
			<!-- Page Header -->
			<div class="page-header">
				<div class="row">
					<div class="col-sm-12">
						<ul class="breadcrumb">
							<li class="breadcrumb-item"><a href="index.php">Dashboard </a></li>
							<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
							<li class="breadcrumb-item "><a href="#">Ventas</a></li>
							<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
							<li class="breadcrumb-item"><a href="view_leads.php">Nuevo Lead</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-12">
									<form method="POST" action="scripts/sales/add_lead.php" id="new_lead">
										<div class="row">
											<div class="col-12">
												<div class="form-heading">
													<h4><b>Recolecta toda la información disponible del nuevo lead</b></h4>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Nombre (s) <span class="login-danger">*</span></label>
													<input class="form-control" type="text" name="first_name" id="first_name" required>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Apellido (s)</label>
													<input class="form-control" type="text" name="last_name" id="last_name">
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Clínica *</label>
													<select class="form-control select" name="clinic" id="clinic" required>
														<option disabled>Selecciona</option>
														<option selected value="CDMX">CDMX</option>
														<option selected value="Queretaro">Queretaro</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Origen:<span class="login-danger">*</span></label>
													<select class="form-control select" name="origin" id="origin" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Facebook">Facebook</option>
														<option value="Instagram">Instagram</option>
														<option value="Tiktok">Tiktok</option>
														<option value="Google">Google</option>
														<option value="Whatsapp">Whatsapp</option>
														<option value="Referido">Referido</option>
														<option value="Organico">Orgánico</option>
														<option value="Recomendado">Recomendado</option>
														<option value="Pagina">Pagina</option>
														<option value="Px">Ya es px</option>
														<option value="Campaña">Campaña publicitaria</option>
														<option value="Otro">Otro</option>
														<option value="Px">Desconocido</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Teléfono Celular<span class="login-danger">*</span></label>
													<input class="form-control" type="text" name="phone" id="phone" required>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Interesado en:<span class="login-danger">*</span></label>
													<select class="form-control select" name="interested_in" id="interested_in" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Capilar">Injerto Capilar</option>
														<option value="Barba">Injerto Barba</option>
														<option value="Ambos">Ambos Injertos</option>
														<option value="Tratamientos">Tratamientos</option>
														<option value="Micro">Micro</option>
														<option value="Retoque">Retoque</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Etapa del Prospecto<span class="login-danger">*</span></label>
													<select class="form-control select" name="stage" id="stage" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Nuevo Lead">Nuevo Lead</option>
														<option value="En prospección">En prospección</option>
														<option value="Interesado">Interesado</option>
														<option value="Agendó valoración">Agendó valoración</option>
														<option value="Valorado">Valorado</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Calificación<span class="login-danger">*</span></label>
													<select class="form-control select" id="qualifff" name="qualif" required>
														<option selected disabled>Selecciona la Etapa</option>
														<option value="En conversación">En conversación</option>
														<option value="Descartado">Descartado</option>
														<option value="Inactivo">Inactivo</option>
														<option value="Interesado">Interesado</option>
														<option value="Seguimiento">Seguimiento</option>
														<option value="En negociación">En negociación</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6 col-xl-4">
												<div class="input-block local-forms">
													<label>Propietaria (o):<span class="login-danger">*</span></label>
													<select class="form-control select" name="seller" id="seller" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Janeth Ruíz">Janeth Ruíz</option>
														<option value="Marisol Olmos">Marisol Olmos</option>
														<option value="Adriana Silva">Adriana Silva</option>
														<option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
														<option value="Dra. Lizbeth Carmona">Dra. Lizbeth Carmona</option>
														<option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
														<option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
														<option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
													</select>
												</div>
											</div>

											<div class="col-12">
												<div class="input-block local-forms">
													<label>Link de Respond (si aplica)</label>
													<input class="form-control" type="text" name="link" id="link">
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label>Notas:</label>
													<textarea rows=5 class="form-control" type="text" name="notes" id="notes"></textarea>
												</div>
											</div>
											<div class="col-12">
												<div class="d-flex justify-content-end">
													<button type="submit" class="btn btn-primary" id="btn_add_lead">Agregar nuevo lead</button>
												</div>
											</div>
										</div>
									</form>
								</div>
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
	<!-- Bootstrap Core JS -->
	<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>
	<!-- Slimscroll -->
	<script src="assets/js/jquery.slimscroll.js"></script>
	<!-- SweetAlert -->
	<script src="assets/plugins/sweetalert/sweetalert.11.10.min.js"></script>
	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>

	<script>
		$(document).ready(function() {
			$("#new_lead").submit(function(e) {
				e.preventDefault();

				if (this.checkValidity()) {
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
								showSweetAlert("Listo!", response.message, "success", 2300, true, false).then(function() {
									window.location.href = "view_lead.php?id=" + response.lead_id;
								});
							} else {
								console.log(response);
								Swal.fire({
									title: "Ocurrió un error",
									text: "Por favor, contacta a administración",
									icon: "error",
									timer: 1700, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
									timerProgressBar: true, // Muestra una barra de progreso
									showConfirmButton: false, // No muestra el botón de confirmación
								});
							}
						})
						.fail(function(response) {
							console.log(response);
							Swal.fire({
								title: "Ocurrió un error",
								text: "Por favor, contacta a administración",
								icon: "error",
								timer: 1700, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
								timerProgressBar: true, // Muestra una barra de progreso
								showConfirmButton: false, // No muestra el botón de confirmación
							});
						})

					$(".submit-form").attr('disabled', 'true');
				} else {
					Swal.fire({
						title: "Error",
						text: "Faltan algunos datos para registrar el cierre",
						icon: "error",
						timer: 1900, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
						timerProgressBar: true, // Muestra una barra de progreso
						showConfirmButton: false, // No muestra el botón de confirmación
					});
				}
			});
			$("#stage").change(function() {
				//Available Options
				let opt = ["En conversación", "Negociación", "Fuera de su presupuesto", "Se encuentra lejos", "No es candidato", "Está comparando opciones", "No acudió a valoración", "Pendiente de anticipo", "Dejó de contestar", "No volver a contactar", "Seguimiento pre-proced.", "Seguimiento post-proced."];

				//Assign options to each Stage
				let change = {
					"Nuevo Lead": [opt[0]],
					"En prospección": [opt[1], opt[2], opt[3], opt[5], opt[8], opt[9]],
					"Interesado": [opt[1], opt[2], opt[3], opt[5], opt[8], opt[9]],
					"Agendó valoración": [opt[1], opt[2], opt[3], opt[4], opt[5], opt[6], opt[8], opt[9]],
					"Valorado": [opt[1], opt[2], opt[3], opt[4], opt[5], opt[6], opt[8], opt[9], opt[7]],
					"Dio anticipo": [opt[10], opt[8], opt[9]],
					"Cerrado": [opt[10], opt[8], opt[9]],
					"Canceló": [opt[10], opt[8], opt[9]],
					"Reagendó": [opt[10], opt[8], opt[9]],
					"Cliente": [opt[11], opt[8]]
				};

				//Load matching options
				let selectedValue = $(this).val();
				let selectedOptions = change[selectedValue];
				$("#qualif").empty();

				//Put the options in qualif select
				if (selectedOptions) {
					$.each(selectedOptions, function(index, optionText) {
						$("#qualif").append($("<option></option>").attr("value", optionText).text(optionText));
					});
				}
			});
		});
	</script>
</body>

</html>