<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
setlocale(LC_TIME, 'es_ES');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";
require_once __DIR__ . "/scripts/common/utilities.php";

$clinic = $_GET['clinic'];
if ($_GET['clinic'] == "Santafe") {
	$parsed_clinic = "Santa Fe";
} elseif ($_GET['clinic'] == "Queretaro") {
	$parsed_clinic = "Queretaro";
} else {
	$parsed_clinic = "Pedregal";
} ?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Calendario <?= $parsed_clinic; ?> | ERP | Los Reyes del Injerto</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">
	<!-- Fontawesome CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<!-- Select2 CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
	<!-- Toastr -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/toastr/toastr.css">
	<!-- TimePicker -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/timepicker/timepicker.min.css">
	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css?v=1.2">
</head>

<body class="mini-sidebar">
	<input type="hidden" id="chosen_clinic" value="<?= $clinic; ?>">
	<div class="main-wrapper">
		<?php
		require __DIR__ . '/templates/header.php';
		require __DIR__ . '/templates/sidebar.php';
		?>
		<div class="page-wrapper">
			<div class="content">
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.php">Inicio </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active"><?= $parsed_clinic; ?></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Calendario</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- Title -->
					<div class="col-12">
						<h3><strong><?= $parsed_clinic; ?></strong></h3>
					</div>
					<div class="col-3 col-md-3 d-none d-md-block">
						<!-- Mini Calendar -->
						<div class="card">
							<div class="card-block table-dash">
								<div class="calendar-container">
									<div class="calendar-month-arrow-container">
										<div class="calendar-month-year-container">
											<div class="row">
												<div class="col-6 col-md-4">
													<select class="calendar-years" id="year-selected"></select>
												</div>
												<div class="col-6 col-md-4">
													<select class="calendar-months" id="month-selected"></select>
												</div>
											</div>
										</div>
										<div class="calendar-month-year">
										</div>
										<div class="col-6">
											<div class="calendar-arrow-container">
												<button class="calendar-today-button"></button>
												<button class="calendar-left-arrow">←</button>
												<button class="calendar-right-arrow">→</button>
											</div>
										</div>
									</div>
									<ul class="calendar-week">
									</ul>
									<ul class="calendar-days">
									</ul>
								</div>
							</div>
						</div>
						<!-- ./ End Mini Calendar -->
						<!-- Events Search -->
						<div class="card">
							<div class="card-block table-dash">
								<h4 class="text-center">Buscar citas en <?= $parsed_clinic; ?>:</h4>

								<form method="POST" id="search_appointments" action="scripts/calendar/search.php">
									<!-- <div class="row">
									<div class="col-4">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="search_type" id="radioPrev" value="prev">
											<label class="form-check-label" for="radioPrev">
											Pasadas
											</label>
										</div>
									</div>
									<div class="col-4">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="search_type" id="radioNext" value="next">
											<label class="form-check-label" for="radioNext">
											Próximas
											</label>
										</div>
									</div>
									<div class="col-4">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="search_type" id="radioBoth" value="both" checked>
											<label class="form-check-label" for="radioBoth">
											Ambas
											</label>
										</div>
									</div>
								</div> -->
									<div class="input-group">
										<input type="text" placeholder="Nombre o Expediente ..." class="form-control" name="search" id="search" minlength="3" required>
										<button id="btn_search" type="submit" class="btn btn-outline-dark">Buscar</button>
									</div>
								</form>
							</div>
						</div>
						<!-- ./ End Events Search -->
						<!-- Copy Agenda -->
						<div class="card">
							<div class="card-block table-dash">
								<button id="copy_agenda" type="button" class="btn btn-block btn-rounded btn-outline-primary">Copiar Agenda de hoy <?= spanishToday(); ?></button>
								<button id="copy_agenda_message"  style="text-align:center;" type="button" class="btn">
									Copiar agenda <?= $dayLabel ?> <?= $nextDay ?>
								</button>
							</div>
						</div>
						<!-- ./ End Copy Agenda -->
						<!-- Filters -->
						<div class="card">
							<div class="card-block table-dash">
								<h4>Viendo:</h4><br>
								<div class="form-check">
									<input class="form-check-input cb_event_type" name="cb_event_type" type="checkbox" id="checkRevisiones" value="revision" checked>
									<label class="form-check-label" for="checkRevisiones">
										<h4>Revisiones</h4>
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input cb_event_type" name="cb_event_type" type="checkbox" id="checkValoraciones" value="valoracion" checked>
									<label class="form-check-label" for="checkValoraciones">
										<h4>Valoraciones</h4>
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input cb_event_type" name="cb_event_type" type="checkbox" id="checkTratamientos" value="tratamiento" checked>
									<label class="form-check-label" for="checkTratamientos">
										<h4>Tratamientos</h4>
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input cb_event_type" name="cb_event_type" type="checkbox" id="checkEvento" value="evento" checked>
									<label class="form-check-label" for="checkEvento">
										<h4>Eventos</h4>
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input cb_event_type" name="cb_event_type" type="checkbox" id="checkHolidays" value="holidays">
									<label class=" form-check-label" for="checkHolidays">
										<h4>Vacaciones</h4>
									</label>
								</div>
								<?php if ($clinic == "Santafe" || $clinic == "Queretaro") { ?>
									<div class="form-check mb-4">
										<input class="form-check-input cb_event_type" name="cb_event_type" type="checkbox" id="checkProcedimientos" value="procedimiento" checked disabled>
										<label class="form-check-label" for="checkProcedimientos">
											<h4>Procedimientos</h4>
										</label>
									</div>
								<?php } ?>
								<a type="button" href="calendar.php?clinic=<?= ($clinic == "Santafe") ? "Pedregal" : "Santafe"; ?>" class="btn btn-block btn-primary">Cambiar a Calendario <?= ($clinic == "Santafe") ? "Pedregal" : "Santafe"; ?></a>
							</div>
						</div>
						<!-- End Filters -->
						<!-- Calendar -->
						<!-- ./ End Calendar -->
					</div>
					<div class="col-12 col-md-9">
						<div class="card">
							<div class="card-block table-dash">
								<div id='calendar'></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--  Event Modal -->
	<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="uploaded_by">

					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="event_type" name="event_type" value="revision">
					<div class="row">
						<div class="w-100">
							<div class="wrapper-nav">
								<nav class="nav nav-tabs list mt-2" id="myTab" role="tablist">
									<a class="nav-item nav-link pointer" id="tab-revision" data-bs-toggle="tab" data-bs-target="#content-revision" role="tab" aria-controls="public" aria-selected="true">Revisión</a>
									<a class="nav-item nav-link pointer" id="tab-valoracion" data-bs-target="#content-valoracion" role="tab" data-bs-toggle="tab">Valoración</a>
									<a class="nav-item nav-link pointer" id="tab-tratamiento" data-bs-target="#content-tratamiento" role="tab" data-bs-toggle="tab">Tratamiento</a>
									<a class="nav-item nav-link pointer" id="tab-evento" data-bs-target="#content-evento" role="tab" data-bs-toggle="tab">Evento</a>
								</nav>
							</div>
							<div class="tab-content p-3" id="myTabContent">
								<div class="tab-pane fade mt-2" id="content-revision" role="tabpanel" aria-labelledby="public-tab">
									<form action="#" method="POST" id="formRevision">
										<input type="hidden" name="clinic" id="clinic" value="<?= $_GET['clinic']; ?>">
										<div class="row">
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Tipo:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="attendance_type" id="revision_attendance_type" required>
														<option value="" selected disabled>Selecciona</option>
														<option value=0>Presencial</option>
														<option value=1>Virtual</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Corresponde a:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="revision_time" id="revision_time" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="24 H">24 horas</option>
														<option value="10 D">10 días</option>
														<option value="1 M">1 Mes</option>
														<option value="3 M">3 Meses</option>
														<option value="6 M">6 Meses</option>
														<option value="9 M">9 Meses</option>
														<option value="12 M">12 Meses</option>
														<option value="15 M">15 Meses</option>
														<option value="18 M">18 Meses</option>
														<option value="Post Alta">Post Alta</option>
													</select>
												</div>
											</div>
										</div>
										<div class="col-12 mb-3">
											<div class="input-block local-forms">
												<label><strong>Exped #:</strong><span class="login-danger">*</span></label>
												<input type="number" class="form-control num_med_record" name="num_med_record" id="revision_num_med_record" min=0 step=1 max=2000>
											</div>
										</div>
										<div class="col-12 mb-3">
											<div class="input-block local-forms">
												<label><strong>Nombre del px:</strong><span class="login-danger">*</span></label>
												<input type="text" class="form-control" name="patient_name" id="revision_patient_name" required>
											</div>
										</div>
										<div class="col-12">
											<div class="input-block local-forms">
												<label><strong>Fecha:</strong><span class="login-danger">*</span></label>
												<input type="date" class="form-control event_dates" name="event_date" id="revision_event_date" required>
											</div>
										</div>
										<div class="row">
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Inicio:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control start_times input-times" name="start_date" id="revision_start_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Termino:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control end_times input-times" name="end_date" id="revision_end_date" required>
												</div>
											</div>
										</div>
										<div class="col-12">
											<div class="input-block local-forms">
												<label><strong>Notas adicionales</strong></label>
												<textarea class="form-control" name="notes" id="revision_notes"></textarea>
											</div>
										</div>
										<div class="row">
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Status:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="status" id="revision_status" required>
														<option value="" disabled>Selecciona</option>
														<option value="Agendada" selected>Agendada</option>
														<option value="Confirmada">Confirmada</option>
														<option value="No Confirmada">No Confirmada</option>
														<option value="No contestó">No contestó</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Calificación:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="qualy" id="revision_qualy" required>
														<option value="" disabled>Selecciona</option>
														<option value="Pendiente" selected>Pendiente</option>
														<option value="Asistió">Asistió</option>
														<option value="No asistió">No asistió</option>
														<option value="Reagendó">Reagendó</option>
													</select>
												</div>
											</div>

											<div class="">
												<div class="input-block local-forms">
													<label><strong>Clinica:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="clinic" id="revision_clinic" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Santafe">Santa Fe</option>
														<option value="Pedregal">Pedregal</option>
														<option value="Queretaro">Queretaro</option>
													</select>
												</div>
											</div>


										</div>
									</form>
								</div>
								<div class="tab-pane fade mt-2" id="content-valoracion" role="tabpanel" aria-labelledby="group-dropdown2-tab">
									<form action="#" method="POST" id="formValoracion">
										<input type="hidden" name="clinic" id="clinic" value="<?= $_GET['clinic']; ?>">
										<div class="row">
											<div class="col-6">
												<div class="input-block local-forms">
													<label><strong>Tipo:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="attendance_type" id="valoracion_attendance_type" required>
														<option value="" selected disabled>Selecciona</option>
														<option value=0>Presencial</option>
														<option value=1>Virtual</option>
													</select>
												</div>
											</div>
											<div class="col-6">
												<div class="input-block local-forms">
													<label><strong>Vendedor(a):</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="seller" id="valoracion_seller" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Janeth Ruíz">Janeth Ruíz</option>
														<option value="Marisol Olmos">Marisol Olmos</option>
														<option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
														<option value="Dra. Lizbeth Carmona">Dra. Lizbeth Carmona</option>
														<option value="Sin vendedor">Sin vendedor(a) asignado(a)</option>
														<option value="Adriana Silva">Adriana Silva</option>
													</select>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Nombre del cliente:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control" name="patient_name" id="valoracion_patient_name" required>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Fecha:</strong><span class="login-danger">*</span></label>
													<input type="date" class="form-control event_dates" name="event_date" id="valoracion_event_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Inicio:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control start_times input-times" name="start_date" id="valoracion_start_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Término:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control end_times input-times" name="end_date" id="valoracion_end_date" required>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Notas adicionales:</strong><span class="login-danger">*</span></label>
													<textarea class="form-control" name="notes" id="valoracion_notes"></textarea>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Status:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="status" id="valoracion_status" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Agendada">Agendada</option>
														<option value="Confirmada">Confirmada</option>
														<option value="No Confirmada">No Confirmada</option>
														<option value="No contestó">No contestó</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Calificación:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="qualy" id="valoracion_qualy" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Pendiente">Pendiente</option>
														<option value="Asistió">Asistió</option>
														<option value="No asistió">No asistió</option>
														<option value="Reagendó">Reagendó</option>
													</select>
												</div>
											</div>

											<div class="">
												<div class="input-block local-forms">
													<label><strong>Clinica:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="clinic" id="valoracion_clinic" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Santafe">Santa Fe</option>
														<option value="Pedregal">Pedregal</option>
														<option value="Queretaro">Queretaro</option>
													</select>
												</div>
											</div>
										</div>
									</form>
								</div>
								<div class="tab-pane fade mt-2" id="content-tratamiento" role="tabpanel" aria-labelledby="group-dropdown2-tab">
									<form action="#" method="POST" id="formTratamiento">
										<input type="hidden" name="clinic" id="clinic" value="<?= $_GET['clinic']; ?>">
										<input type="hidden" name="attendance_type" id="tratamiento_attendance_type" value=0>
										<div class="row">
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Exped. #:</strong><span class="login-danger">*</span></label>
													<input required type="number" class="form-control num_med_record" name="num_med_record" id="tratamiento_num_med_record" min=0 step=1 max=2000 required>

													<div class="form-check d-inline-block ms-2">
														<span style="font-size:.8rem;">Sin numero de exp.</span>
														<input class="form-check-input" type="checkbox" name="dif" id="dif">
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Nombre del Paciente:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control" name="patient_name" id="tratamiento_patient_name" required>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Fecha:</strong><span class="login-danger">*</span> </label>
													<input type="date" class="form-control event_dates" name="event_date" id="tratamiento_event_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Inicio:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control start_times input-times" name="start_date" id="tratamiento_start_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Término:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control end_times input-times" name="end_date" id="tratamiento_end_date" required>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Notas:</strong></label>
													<textarea class="form-control" name="notes" id="tratamiento_notes"></textarea>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Status:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="status" id="tratamiento_status" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Agendada">Agendada</option>
														<option value="Confirmada">Confirmada</option>
														<option value="No Confirmada">No Confirmada</option>
														<option value="Reagendó">Reagendó</option>
														<option value="No contestó">No contestó</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Calificación:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="qualy" id="tratamiento_qualy" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Pendiente">Pendiente</option>
														<option value="Asistió">Asistió</option>
														<option value="No asistió">No asistió</option>
														<option value="Reagendó">Reagendó</option>
													</select>
												</div>
											</div>
											<div class="">
												<div class="input-block local-forms">
													<label><strong>Clinica:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="clinic" id="tratamiento_clinic" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Santafe">Santa Fe</option>
														<option value="Pedregal">Pedregal</option>
														<option value="Queretaro">Queretaro</option>
													</select>
												</div>
											</div>
										</div>
									</form>
								</div>

								<div class="tab-pane fade mt-2" id="content-evento" role="tabpanel" aria-labelledby="group-dropdown2-tab">
									<form action="#" method="POST" id="formEvento">
										<input type="hidden" name="clinic" id="clinic" value="<?= $_GET['clinic']; ?>">
										<input type="hidden" name="attendance_type" id="evento_attendance_type" value=0>
										<div class="row">
											<div class="col-12">

											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Nombre del evento:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control" name="event_name" id="event_name" required>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Fecha:</strong><span class="login-danger">*</span> </label>
													<input type="date" class="form-control event_dates" name="event_date" id="evento_event_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Inicio:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control start_times input-times" name="start_date" id="evento_start_date" required>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Término:</strong><span class="login-danger">*</span></label>
													<input type="text" class="form-control end_times input-times" name="end_date" id="evento_end_date" required>
												</div>
											</div>
											<div class="col-12">
												<div class="input-block local-forms">
													<label><strong>Notas:</strong></label>
													<textarea class="form-control" name="notes" id="evento_notes"></textarea>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Status:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="status" id="evento_status" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Agendada">Agendada</option>
														<option value="Confirmada">Confirmada</option>
														<option value="No Confirmada">No Confirmada</option>
														<option value="Reagendó">Reagendó</option>
														<option value="No contestó">No contestó</option>
													</select>
												</div>
											</div>
											<div class="col-12 col-md-6">
												<div class="input-block local-forms">
													<label><strong>Calificación:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="qualy" id="evento_qualy" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Pendiente">Pendiente</option>
														<option value="Asistió">Asistió</option>
														<option value="No asistió">No asistió</option>
														<option value="Reagendó">Reagendó</option>
													</select>
												</div>
											</div>
											<div class="">
												<div class="input-block local-forms">
													<label><strong>Clinica:</strong><span class="login-danger">*</span></label>
													<select class="form-control" name="clinic" id="evento_clinic" required>
														<option value="" selected disabled>Selecciona</option>
														<option value="Santafe">Santa Fe</option>
														<option value="Pedregal">Pedregal</option>
														<option value="Queretaro">Queretaro</option>
													</select>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger btn-delete-event">
						Eliminar
					</button>
					<button type="button" class="btn btn-success btn-update-event">
						Guardar cambios
					</button>
					<button type="button" class="btn btn-primary btn-add-event">
						Añadir evento
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Obtiene el parámetro 'clinic' de la URL
		const clinicParam = new URLSearchParams(window.location.search).get('clinic');

		// Verifica si el parámetro existe
		if (clinicParam) {
			// Selecciona el valor en el <select> si existe
			document.querySelector(`#revision_clinic option[value="${clinicParam}"]`)?.setAttribute('selected', 'selected');
		} else {
			console.error("Error: El parámetro 'clinic' no está en la URL.");
		}


		document.getElementById('dif').addEventListener('change', function() {
			const numMedRecordInput = document.getElementById('tratamiento_num_med_record');

			if (this.checked) {
				numMedRecordInput.removeAttribute('required');
				numMedRecordInput.value = ''; // Opcional: Limpiar el valor del input
				numMedRecordInput.setAttribute('disabled', 'disabled'); // Deshabilitar el input
			} else {
				numMedRecordInput.setAttribute('required', 'required');
				numMedRecordInput.removeAttribute('disabled'); // Habilitar el input
			}
		});
	</script>
	<!-- ./ End Event Modal -->
	<!-- Search Event Modal -->
	<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body" id="searchModalBody"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	<!-- ./ End Search Event Modal -->
	<div class="sidebar-overlay"></div>

	<!-- jQuery -->
	<script src="assets/js/jquery.min.js"></script>
	<!-- Bootstrap Core JS -->
	<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>
	<!-- Slimscroll -->
	<script src="assets/js/jquery.slimscroll.js"></script>
	<!-- Toast -->
	<script src="assets/plugins/toastr/toastr.min.js"></script>
	<script src="assets/plugins/toastr/toastr.js"></script>
	<!-- Select2 Js -->
	<script src="assets/js/select2.min.js"></script>
	<!-- Sweet Alert-->
	<script src="assets/plugins/sweetalert/sweetalert.11.10.min.js"></script>
	<!-- FullCalendar -->
	<script src='assets/plugins/fullcalendar/script.js'></script>
	<!-- TimePicker -->
	<script src="assets/plugins/timepicker/timepicker.min.js"></script>
	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>
	<script src="assets/js/minicalendar-script.js"></script>
	<script src="assets/js/calendar-init.js"></script>
	<script src="assets/js/calendar-functions.js"></script>
	<!-- <script src="assets/js/toast_appointment.js"> </script> -->

</body>

</html>