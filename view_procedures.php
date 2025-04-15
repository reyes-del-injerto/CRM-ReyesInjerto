<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
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
	<title>Procedimientos | ERP |Los Reyes del Injerto</title>
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

	<!-- DateRangePicker -->
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

	<style>
		.d-block:hover {

			background-color: #e6bc69 !important;
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
								<li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Lista de Procedimientos</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<!-- /Page Header -->
				<div class="row">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-header">
								<div class="card report-card">
									<div class="card-body pb-0">
										<div class="row">
											<div class="col-12">
												<!-- Contenedor para los badges -->
												<div id="activeFilters" class="mt-3">

												</div>
												<script>
													document.addEventListener('DOMContentLoaded', () => {
														// Función para crear un badge
														const createBadge = (text, filterType) => {
															const badge = document.createElement('span');
															badge.classList.add('badge');
															badge.setAttribute('data-filter-type', filterType);
															badge.innerHTML = `${text} <span class="remove-badge">&times;</span>`;
															document.getElementById('activeFilters').appendChild(badge);
														};

														// Función para actualizar los badges según los filtros aplicados
														const updateBadges = () => {
															const activeFilters = document.getElementById('activeFilters');
															activeFilters.innerHTML = '';

															// Ejemplo: Añadir badge de fechas
															const selectedDate = document.querySelector('#dates').value;
															console.log("Selected Date:", selectedDate); // Añadido para depuración
															if (selectedDate) {
																createBadge(`Fechas: ${selectedDate}`, 'dates');
															}

															// Ejemplo: Añadir badges de tipos de procedimientos
															const checkedTypes = document.querySelectorAll('input[name="type[]"]:checked');
															const allTypes = document.querySelectorAll('input[name="type[]"]');

															if (checkedTypes.length === allTypes.length) {
																createBadge('Tipos de Procedimiento: Todos', 'type');
															} else {
																checkedTypes.forEach((checkbox) => {
																	createBadge(`Tipo: ${checkbox.value}`, 'type');
																});
															}

															// Ejemplo: Añadir badges de especialistas
															const checkedSpecialists = document.querySelectorAll('input[name="specialist[]"]:checked');
															const allSpecialists = document.querySelectorAll('input[name="specialist[]"]');

															if (checkedSpecialists.length === allSpecialists.length) {
																createBadge('Especialistas: Todos', 'specialist');
															} else {
																checkedSpecialists.forEach((checkbox) => {
																	createBadge(`Especialista: ${checkbox.value}`, 'specialist');
																});
															}
														};

														// Manejar el evento de los formularios de filtros
														document.querySelectorAll('.filter_forms').forEach((form) => {
															form.addEventListener('submit', (event) => {
																event.preventDefault();
																updateBadges();
															});
														});

														// Manejar el evento de limpiar filtros
														document.querySelector('.report-btn a').addEventListener('click', () => {
															document.getElementById('activeFilters').innerHTML = '';
															document.querySelector('#dates').value = '';
															document.querySelectorAll('input[name="type[]"], input[name="specialist[]"]').forEach(checkbox => checkbox.checked = false);
														});

														// Manejar el evento de eliminar un badge
														document.getElementById('activeFilters').addEventListener('click', (event) => {
															if (event.target.classList.contains('remove-badge')) {
																const badge = event.target.parentElement;
																const filterType = badge.getAttribute('data-filter-type');

																// Limpiar el filtro correspondiente
																if (filterType === 'dates') {
																	document.querySelector('#dates').value = '';
																} else if (filterType === 'type') {
																	const typeName = badge.innerText.replace('Tipo: ', '').replace(' ×', '').trim();
																	if (typeName !== 'Tipos de Procedimiento: Todos') {
																		document.querySelector(`input[name="type[]"][value="${typeName}"]`).checked = false;
																	} else {
																		document.querySelectorAll('input[name="type[]"]').forEach(checkbox => checkbox.checked = false);
																	}
																} else if (filterType === 'specialist') {
																	const specialistName = badge.innerText.replace('Especialista: ', '').replace(' ×', '').trim();
																	if (specialistName !== 'Especialistas: Todos') {
																		document.querySelector(`input[name="specialist[]"][value="${specialistName}"]`).checked = false;
																	} else {
																		document.querySelectorAll('input[name="specialist[]"]').forEach(checkbox => checkbox.checked = false);
																	}
																}

																badge.remove();
																updateBadges(); // Actualizar los badges después de eliminar uno
															}
														});
													});
												</script>
												<ul class="app-listing">
													<li>
														<div class="multipleSelection">
															<div class="selectBox">
																<p class="mb-0"><i data-feather="edit" class="me-1 select-icon"></i>Clinica</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#" id="clinic_filter" class="filter_forms">
																	<p class=" checkbox-title">Ver procedimientos de:</p>
																	<div class="selectBox-cont">
																		<label class="custom_check w-100">
																			<input type="checkbox" name="clinic[]" value="Santa Fe" checked>
																			<span class="checkmark"></span> Santa Fe
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="clinic[]" value="Queretaro" checked>
																			<span class="checkmark"></span> Queretaro
																		</label>

																	</div>
																	<button type="submit" class="btn w-100 btn-primary filters">Aplicar</button>
																</form>
															</div>
														</div>
													</li>
													<li>
														<div class="multipleSelection">
															<div class="selectBox">
																<p class="mb-0"><i data-feather="calendar" class="me-1 select-icon"></i> Fechas</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#">
																	<p class="checkbox-title">Ver Leads Creados En </p>
																	<div class="selectBox-cont selectBox-cont-one h-auto">
																		<div class="form-custom">
																			<input class="form-control" name="dates" id="dates" placeholder="Selecciona fechas" value="">
																		</div>
																		<div class="date-list">
																			<ul>
																				<!-- <li><a href="#" class="btn date-btn" data-value="tomorrow">Mañana</a></li> -->
																				<li><a href="#" class="btn date-btn" data-value="today">Hoy</a></li>
																				<li><a href="#" class="btn date-btn" data-value="yesterday">Ayer</a></li>
																				<li><a href="#" class="btn date-btn" data-value="thisweek">Esta semana</a></li>
																				<li><a href="#" class="btn date-btn" data-value="thismonth">Este mes</a></li>
																				<li><a href="#" class="btn date-btn" data-value="all">Todo</a></li>
																			</ul>
																		</div>
																	</div>
																</form>
															</div>
														</div>
													</li>
													<li>
														<div class="multipleSelection">
															<div class="selectBox">
																<p class="mb-0"><i data-feather="edit" class="me-1 select-icon"></i>Tipo de Proced.</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#" id="type_filter" class="filter_forms">
																	<p class=" checkbox-title">Ver procedimientos de:</p>
																	<div class="selectBox-cont">
																		<label class="custom_check w-100">
																			<input type="checkbox" name="type[]" value="Capilar" checked>
																			<span class="checkmark"></span> Capilar
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="type[]" value="Barba" checked>
																			<span class="checkmark"></span> Barba
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="type[]" value="Ambos" checked>
																			<span class="checkmark"></span> Ambos
																		</label>
																	</div>
																	<button type="submit" class="btn w-100 btn-primary filters">Aplicar</button>
																</form>
															</div>
														</div>
													</li>

													<li>
														<div class="multipleSelection">
															<div class="selectBox">
																<p class="mb-0"><i data-feather="edit" class="me-1 select-icon"></i>Especialista</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#" id="specialist_filter" class="filter_forms">
																	<p class=" checkbox-title">Ver procedimientos de:</p>
																	<div class="selectBox-cont">
																		<label class="custom_check w-100">
																			<input type="checkbox" name="specialist[]" value="Héctor Carmona" checked>
																			<span class="checkmark"></span> Héctor Carmona
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="specialist[]" value="Luis Moreno" checked>
																			<span class="checkmark"></span> Luis Moreno
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="specialist[]" value="Xóchitl Lagunas" checked>
																			<span class="checkmark"></span> Xóchitl Lagunas
																		</label>
																	</div>
																	<button type="submit" class="btn w-100 btn-primary filters">Aplicar</button>
																</form>
															</div>
														</div>
													</li>
													<li>
														<div class="report-btn">
															<a href="view_procedures.php" class="btn">
																<i data-feather="rotate-cw" class="me-1 select-icon"></i>Limpiar Filtros
															</a>
														</div>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<!-- Table Header -->
							</div>
							<div class="card-body">
								<!-- /Table Header -->
								<div class="table-responsive">
									<table class="table table-striped" id="proceduresTable">
										<thead>
											<tr>
												<th>Fecha Proc.</th>
												<th>Exp. No.</th>
												<th>Paciente</th>
												<th>Injerto</th>
												<th>Clinica</th>
												<th>Sala</th>
												<th>Especialista</th>
												<th>Observaciones</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
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
						<p style="font-size:10px;color:red;">Para editar campos deshabilitados, contacta a Administración.</p>
						<form action="scripts/procedures/update.php" method="POST" id="formUpdateProcedure">
							<input id="procedure_id" name="procedure_id" type="hidden" class="form-control" />
							<div class="row">
								<div class="col-6">
									<label for="cantidad_sala">Fecha de procedimiento</label>
									<input id="procedure_date" name="procedure_date" type="date" class="form-control" disabled />
								</div>
								<div class="col-6">
									<label for="num_med_record">Núm. de Expediente</label>
									<input id="num_med_record" name="num_med_record" type="text" class="form-control" disabled />
								</div>
							</div>
							<label for="nombre">Nombre del paciente</label>
							<div class="form-group">
								<input id="name" name="name" class="form-control" disabled />
							</div>
							<div class="row">
								<div class="col-6">
									<label for="basicInput">Tipo de Injerto</label>
									<select class="form-control" id="procedure_type" name="procedure_type" disabled>
										<option value=0 disabled readonly>Selecciona</option>
										<option value="Capilar">Capilar</option>
										<option value="Barba">Barba</option>
										<option value="Ambos">Ambos</option>
									</select>
								</div>
								<div class="col-6">
									<label for="gramaje">Sala</label>
									<select class="form-control" id="room" name="room" required>
										<option value="" selected readonly>Selecciona</option>
										<option value=1>1</option>
										<option value=2>2</option>
										<option value=3>3</option>
										<option value=4>Queretaro</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="gramaje">Especialista</label>
								<select class="form-control" id="specialist" name="specialist" required>
									<option value="" selected readonly>Selecciona</option>
									<option value="Héctor Carmona">Héctor Carmona</option>
									<option value="Luis Moreno">Luis Moreno</option>
									<option value="Xóchitl Lagunas">Xóchitl Lagunas</option>
									<option value="Dr Alejandro Santana">Dr Alejandro Santana</option>
									<option value="Dra Oriana Aguilar">Dra Oriana Aguilar</option>
								</select>
							</div>
							<div class="form-group">
								<label>Observaciones</label>
								<textarea id="notes" name="notes" placeholder="" class="form-control" rows=4></textarea>
							</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-dark" data-bs-dismiss="modal">
							<i class="bx bx-x d-block d-sm-none"></i>
							<span class="d-sm-block">Cerrar</span>
						</button>
						<button type="submit" class="btn btn-success ms-1">
							<i class="bx bx-check d-block d-sm-none"></i>
							<span class="d-sm-block">Actualizar</span>
						</button>
					</div>
					</form>
				</div>
			</div>
		</div>
		<div class="modal fade" id="optionsModal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Opciones</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>

					<!-- Incluye Font Awesome -->
					<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

					<div class="modal-body text-center menu_options">
						<div class="row mb-3">
							<div class="col-6 mb-3">
								<a id="btnInfo" class="d-block text-decoration-none badge bg-secondary text-white w-100 text-center p-3 fs-6 fw-lighter hover-effect" href="#" id="">
									<i class="fas fa-info-circle me-2"></i> Información
								</a>
							</div>
							<div class="col-6 mb-3">

								<a class="d-block text-decoration-none badge bg-secondary text-white w-100 text-center p-3 fs-6 fw-lighter hover-effect" id="btnPhoto" href="#">
									<i class="fas fa-camera me-2"></i> Procedimiento
								</a>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-6 mb-3">
								<a class="d-block text-decoration-none badge bg-secondary text-white w-100 text-center p-3 fs-6 fw-lighter hover-effect" href="view_notifications.php" id="btnNotif">
									<i class="fas fa-bell me-2"></i> Notificaciones
								</a>
							</div>
							<div class="col-6 mb-3">
								<a class="d-block text-decoration-none badge bg-secondary text-white w-100 text-center p-3 fs-6 fw-lighter hover-effect" id="photosTreatment" href="#">
									<i class="fas fa-capsules me-2"></i> Tratamiento
								</a>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-6 mb-3">
								<a class="d-block text-decoration-none badge bg-secondary text-white w-100 text-center p-3 fs-6 fw-lighter hover-effect" id="btnPhoto_second" href="#">
									<i class="fas fa-calendar-day me-2"></i> 2do Proced
								</a>
							</div>
							<div class="col-6 mb-3">
								<a class="d-block text-decoration-none badge bg-secondary text-white w-100 text-center p-3 fs-6 fw-lighter hover-effect" id="btnPhoto_micro" href="#">
									<i class="fas fa-microchip me-2"></i> Micro
								</a>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
						</div>
					</div>





				</div>
			</div>
			<div class="modal fade" id="photosModal" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Opciones</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="text-center">
								<a class="btn-options btn-3 btn-sep icon-photo" id="photosProcedure" href="#">Procedimiento</a>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

		<!-- DatetimePicker -->
		<script src="assets/plugins/moment/moment.min.js"></script>
		<script src="assets/plugins/datetimepicker/datetimepicker.min.js"></script>

		<!-- Sweet alert -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.5/dist/sweetalert2.min.js"></script>

		<!-- DataTables -->
		<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
		<script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/accent-neutralise.js"></script>
		<script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/diacritics-neutralise.js"></script>

		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>

		<!-- DateRangePicker -->
		<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


		<!-- SweetAlert -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<script>
			let table;
			let filters_applied = false;
			let date_range;
			let chosen_time = null;

			$(document).ready(function() {

				$('#dates').daterangepicker({
					autoUpdateInput: false,
					locale: {
						cancelLabel: 'Salir',
						applyLabel: 'Aplicar'
					}
				});
				/* =============== DATATABLE =============== */

				DataTable.datetime('DD/MM/YYYY');

				table = $("#proceduresTable").DataTable({
					"ajax": {
						"url": "scripts/procedures/load_all.php",
						"type": "POST",
						"data": function(d) {
							console.log("filtros enviados", d)
							if (filters_applied) {
								const type = $("#type_filter").serialize();
								const specialist = $("#specialist_filter").serialize();
								const clinic = $("#clinic_filter").serialize();
								const date_range = $("#dates").val();

								if (type.length > 0) d.type = type;
								if (specialist.length > 0) d.specialist = specialist;
								if (clinic.length > 0) d.clinic = clinic;
								if (date_range.length > 0 && chosen_time == null) d.date_range = date_range;
								if (date_range.length == 0 && chosen_time != null) d.chosen_time = chosen_time;



							}
						},
						"dataSrc": function(json) {
							console.log("respuesta de load_all.php", json);
							return json.data;

						},
						"error": function(response) {
							console.log(response)
						}
					},
					autoWidth: false,
					language: {
						//url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
					},
					scrollX: true,
					order: [], // Asegúrate de no especificar ningún orden inicial
				});

				/* =============== DATATABLE End =============== */



				$("#formUpdateProcedure").submit(function(e) {
					e.preventDefault();
					let formData = $(this).serialize();
					let method = $(this).attr('method');
					let url = $(this).attr('action');
					console.log(formData);
					$.ajax({
						method: method,
						url: url,
						data: formData,
						dataType: 'json'
					}).done(function(response) {
						console.log(response);
						if (response.success) {
							showSweetAlert("Listo!", response.message, "success");
							table.ajax.reload();
							$("#editModal").modal("hide");
						} else showSweetAlert("Error", response.message, "error");
					}).fail(function(response) {
						console.log(response.responseText);
						showSweetAlert();
					});
				});
			});

			$(document).on('submit', '.filter_forms', function(e) {
				e.preventDefault();
				$(this).closest(".filterDropDown").fadeOut("slow");

				filters_applied = true;
				table.ajax.reload();
			});


			$(document).on('click', '.date-btn', function(e) {
				$(".date-btn").css('background-color', '#fff');
				$(this).css('background-color', '#e0ac44');
				$("#dates").val("");

				$(this).closest(".filterDropDown").fadeOut("slow");

				filters_applied = true;
				chosen_time = $(this).data('value');
				table.ajax.reload();
			});

			$('#dates').on('apply.daterangepicker', function(ev, picker) {
				const dates_value = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
				$(this).val(dates_value);

				chosen_time = null; //Disable date-btn buttons
				$(".date-btn").css('background-color', '#fff'); //Remove bg-color if any date-btn was been clicked

				filters_applied = true;
				table.ajax.reload();
			});

			$(document).on("click", "#btnInfo", function(e) {
				e.preventDefault();
				$("#optionsModal").modal("hide");
				let procedure_id = $(this).data('procedureid');
				console.log(procedure_id);
				$.ajax({
					type: "POST",
					url: 'scripts/procedures/load_single.php',
					data: {
						procedure_id: procedure_id
					},
					dataType: 'json',
				}).done(function(response) {
					console.log("respues ", response);
					if (response.success) {
						const info = response.procedure_info;
						$("#procedure_id").val(procedure_id);
						const fields = [
							'procedure_date',
							'num_med_record',
							'name',
							'procedure_type',
							'room',
							'specialist',
							'notes'
						];

						// Recorrer cada campo y actualizar su valor
						fields.forEach(function(field) {
							$('#' + field).val(info[field]);
						});
						$("#editModal").modal("show");
					} else showSweetAlert("Error", response.message, "error");
				}).fail(function(response) {
					console.log(response);
					showSweetAlert();
				});

			});

			$(document).on("click", ".single_procedure", function(e) {
				e.preventDefault();
				const procedure_id = $(this).data('id');
				const procedure_num_med = $(this).data('exp');
				const clinic = $(this).data('clinic');

				console.log("🚀 ~ $ ~ procedure_id:", procedure_id);
				console.log("🚀 ~ $ ~ procedure_num_med:", procedure_num_med);
				console.log("🚀 ~ $ ~ clinic:", clinic);

				$("#btnInfo,#btnNotif,#btnPhoto,#photosTreatment,#btnPhoto_second,#btnPhoto_micro")
					.data('procedureid', procedure_id)
					.data('clinic', clinic); // Agregar el valor de 'clinic'

				$("#photosTreatment").data('procedure_exp', procedure_num_med);
				$("#optionsModal").modal("show");
			});



			$(document).on("click", "#btnNotif", function(e) {
				e.preventDefault();
				const procedure_id = $(this).data('procedureid');
				const action = $(this).attr('href');
				window.location.href = `${action}?id=${procedure_id}`;
			});

			$(document).on("click", "#btnPhoto", function(e) {
				e.preventDefault();
				const procedure_id = $(this).data('procedureid');
				window.location.href = `view_photos.php?px=1&type=procedure&id=${procedure_id}`;
			});

			$(document).on("click", "#btnPhoto_second", function(e) {
				e.preventDefault();
				const procedure_id = $(this).data('procedureid');
				window.location.href = `view_photos_second_procedure.php?px=1&type=touchup&id=${procedure_id}`;
				console.log(direccion)
			});

			$(document).on("click", "#btnPhoto_micro", function(e) {
				e.preventDefault();
				const procedure_id = $(this).data('procedureid');
				window.location.href = `view_photos_second_procedure.php?px=1&type=micro&id=${procedure_id}`;
				console.log(direccion)
			});

			$(document).on("click", "#photosTreatment", function(e) {
				e.preventDefault();
				const procedure_exp = $(this).data('procedure_exp');
				const clinic = $(this).data('clinic');
				console.log("clinic", clinic)
				window.location.href = `view_a_treatment.php?num_med=${procedure_exp}&clinic=${clinic}`;
				
			});
		</script>
</body>

</html>