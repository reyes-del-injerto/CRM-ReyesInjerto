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
	<title>Reyes del Injerto | Ver Clientes</title>
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
		.dataTables_scrollBody {
			min-height: 170px !important;
		}

		.dropdown-item {
			cursor: pointer;
		}

		.asignar:hover {
			background-color: #8ADA75;
		}

		.cancelado:hover {
			background-color: #F97878;
		}

		/* estilos del badge */
		#filter_badges_container {
			margin-top: 20px;
		}

		.badge {
			display: inline-block;
			margin: 5px;
			padding: 10px;
			background-color: #007bff;
			color: #fff;
			border-radius: 15px;
		}

		.badge-remove {
			color: #fff;
			margin-left: 5px;
			text-decoration: none;
		}

		.badge-remove:hover {
			color: #ccc;
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
								<li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Ventas</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Lista de Cierres</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="card report-card">
									<div class="card-body pb-0">
										<div class="row">
											<div class="col-12">
												<h4>Filtros activos:</h4>
												<div id="filter_badges_container">Ninguno</div>
												<script>
													document.addEventListener('DOMContentLoaded', () => {
														// Función para crear un badge
														const createBadge = (text, filterType) => {
															const badge = document.createElement('span');
															badge.classList.add('badge');
															badge.setAttribute('data-filter-type', filterType);
															badge.innerHTML = `${text} <span class="remove-badge">&times;</span>`;
															document.getElementById('filter_badges_container').appendChild(badge);
														};

														// Función para actualizar los badges según los filtros aplicados
														const updateBadges = () => {
															const activeFilters = document.getElementById('filter_badges_container');
															activeFilters.innerHTML = '';

															// Añadir badge de fechas
															const selectedDate = document.querySelector('#dates').value;
															if (selectedDate) {
																createBadge(`Fechas: ${selectedDate}`, 'dates');
															}

															// Añadir badges de tipos de procedimientos
															const checkedTypes = document.querySelectorAll('input[name="type[]"]:checked');
															const allTypes = document.querySelectorAll('input[name="type[]"]');
															if (checkedTypes.length === allTypes.length) {
																createBadge('Tipos de Procedimiento: Todos', 'type');
															} else {
																checkedTypes.forEach((checkbox) => {
																	createBadge(`Tipo: ${checkbox.value}`, 'type');
																});
															}

															// Añadir badges de propietarios
															const checkedSellers = document.querySelectorAll('input[name="seller[]"]:checked');
															const allSellers = document.querySelectorAll('input[name="seller[]"]');
															if (checkedSellers.length === allSellers.length) {
																createBadge('Propietarios: Todos', 'seller');
															} else {
																checkedSellers.forEach((checkbox) => {
																	createBadge(`Propietario: ${checkbox.value}`, 'seller');
																});
															}

															// Añadir badges de status
															const checkedStatuses = document.querySelectorAll('input[name="status[]"]:checked');
															const allStatuses = document.querySelectorAll('input[name="status[]"]');
															const statusLabels = {
																'1': 'Próximo',
																'2': 'Expediente Asignado',
																'0': 'Cancelado'
															};

															if (checkedStatuses.length === allStatuses.length) {
																createBadge('Status: Todos', 'status');
															} else {
																checkedStatuses.forEach((checkbox) => {
																	createBadge(`Status: ${statusLabels[checkbox.value]}`, 'status');
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
															document.getElementById('filter_badges_container').innerHTML = '';
															document.querySelector('#dates').value = '';
															document.querySelectorAll('input[name="type[]"], input[name="seller[]"], input[name="status[]"]').forEach(checkbox => checkbox.checked = false);
														});

														// Manejar el evento de eliminar un badge
														document.getElementById('filter_badges_container').addEventListener('click', (event) => {
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
																} else if (filterType === 'seller') {
																	const sellerName = badge.innerText.replace('Propietario: ', '').replace(' ×', '').trim();
																	if (sellerName !== 'Propietarios: Todos') {
																		document.querySelector(`input[name="seller[]"][value="${sellerName}"]`).checked = false;
																	} else {
																		document.querySelectorAll('input[name="seller[]"]').forEach(checkbox => checkbox.checked = false);
																	}
																} else if (filterType === 'status') {
																	const statusName = badge.innerText.replace('Status: ', '').replace(' ×', '').trim();
																	const statusValues = {
																		'Próximo': '1',
																		'Expediente Asignado': '2',
																		'Cancelado': '0'
																	};
																	if (statusName !== 'Status: Todos') {
																		document.querySelector(`input[name="status[]"][value="${statusValues[statusName]}"]`).checked = false;
																	} else {
																		document.querySelectorAll('input[name="status[]"]').forEach(checkbox => checkbox.checked = false);
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
																<p class="mb-0"><i data-feather="user" class="me-1 select-icon"></i>Propietaria(o)</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#" id="seller_filter" class="filter_forms">
																	<p class=" checkbox-title">Ver clientes de:</p>
																	<div class="selectBox-cont">
																		<label class="custom_check w-100">
																			<input type="checkbox" name="seller[]" value="Janeth Ruíz" checked>
																			<span class="checkmark"></span> Janeth Ruíz
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="seller[]" value="Marisol Olmos" checked>
																			<span class="checkmark"></span> Marisol Olmos
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="seller[]" value="Adriana Silva" checked>
																			<span class="checkmark"></span>Adriana Silva
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
																	<p class="checkbox-title">Ver procedimientos con fecha </p>
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
																<p class="mb-0"><i data-feather="user" class="me-1 select-icon"></i>Clinica:</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#" id="clinic_filter" class="filter_forms">
																	<p class=" checkbox-title">Ver clientes de:</p>
																	<div class="selectBox-cont">
																		<label class="custom_check w-100">
																			<input type="checkbox" name="clinic[]" value="Santa Fe" checked>
																			<span class="checkmark"></span> Santa Fe
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="clinic[]" value="Pedregal" checked>
																			<span class="checkmark"></span> Pedregal
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
																<p class="mb-0"><i data-feather="user" class="me-1 select-icon"></i>Status</p>
																<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
															</div>
															<div id="checkBoxes" class="filterDropDown">
																<form action="#" id="status_filter" class="filter_forms">
																	<p class=" checkbox-title">Ver con Status:</p>
																	<div class="selectBox-cont">
																		<label class="custom_check w-100">
																			<input type="checkbox" name="status[]" value=1 checked>
																			<span class="checkmark"></span> Próximo
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="status[]" value=2 checked>
																			<span class="checkmark"></span> Expediente Asignado
																		</label>
																		<label class="custom_check w-100">
																			<input type="checkbox" name="status[]" value=0 checked>
																			<span class="checkmark"></span> Cancelado
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
																<p class="mb-0"><i data-feather="user" class="me-1 select-icon"></i>Tipo Proced.</p>
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
														<div class="report-btn">
															<a href="view_clients.php" class="btn">
																Limpiar Filtros
															</a>
														</div>

													</li>
												</ul>


												<button id="btn_num_med" class="btn btn-secondary show_modal_exp d-none">Cambiar número de exp</button>
											</div>
										</div>
									</div>
								</div>
								<!-- Table Header -->
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table border-0 custom-table comman-table datatable mb-0 table-striped" id="clientsTable" min-height="300px !important;">
										<thead>
											<tr>
												<th>Nombre del Px</th>
												<th>Tipo de proced.</th>
												<th>Fecha del proced.</th>
												<th>Clinica</th>
												<th>Vendedor(a)</th>
												<th>Opciones</th>
												<th>Num. med</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="status_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="">Status del Cliente</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-6 d-flex flex-column align-items-center">
							<span>Actual: </span>
							<div id="divCurrentStatus">
							</div>
						</div>
						<div class="col-6 d-flex flex-column align-items-center">
							<span>Cambiar a:</span>
							<div id="divSelectedStatus">
							</div>
						</div>
					</div>
					<div class="col-12 mt-4">
						<form id="update_client_status" method="POST" action="scripts/clients/update_status.php">
							<input type="hidden" name="lead_id" id="lead_id">
							<input type="hidden" name="current_status_val" id="current_status_val">
							<input type="hidden" name="chosen_status_lbl" id="chosen_status_lbl">

							<div id="divInput">

							</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary submit-form">Actualizar</button>
					</form>
				</div>
			</div>
		</div>
	</div>


	<!-- Modal -->
	<div class="modal fade" id="num_exp_modal" tabindex="-1" aria-labelledby="num_exp_modal_label" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="num_exp_modal_label">Cambiar número de expediente</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">

					<div class="col-12 mt-4">
						<form id="update_num_med" method="POST">
							<input type="hidden" name="lead_id" id="lead_id">
							<input type="hidden" name="current_status_val" id="current_status_val">
							<input type="hidden" name="chosen_status_lbl" id="chosen_status_lbl">

							<div id="divInput">
								<div class="mb-3">
									<label for="current_exp_num" class="form-label">Número de expediente actual</label>
									<input type="number" class="form-control current_exp_num" name="current_exp_num" id="current_exp_num" min=0 step=1 max=40000>
								</div>

								<div class="mb-3">
									<label for="name" class="form-label">Nombre</label>
									<input type="text" class="form-control" id="name_px" name="name_px">
								</div>

								<div class="mb-3">
									<label for="new_exp_num" class="form-label">Nuevo número de expediente</label>
									<input type="number" class="form-control" id="new_exp_num" name="new_exp_num" min=0 step=1 max=40000>
								</div>
							</div>

							<div class="modal-footer">
								<button type="submit" class="btn btn-primary submit-form">Actualizar</button>
							</div>
						</form>

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
		<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
		<script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/accent-neutralise.js"></script>
		<script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/diacritics-neutralise.js"></script>

		<!-- SweetAlert -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>

		<!-- DateRangePicker -->
		<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


		<script>
			let admin = localStorage.getItem("user_id")
			if (admin == 1) {
				let show_btn = document.getElementById("btn_num_med")

				show_btn.classList.remove("d-none");
			}
		</script>

		<script>
			Swal.fire({
				title: "Cargando...",
				allowOutsideClick: false,
				showConfirmButton: false,
			});

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

				DataTable.datetime('DD/MM/YYYY');

				table = $("#clientsTable").DataTable({
					"ajax": {
						"url": "scripts/clients/load_all.php",
						"type": "POST",
						"data": function(d) {
							console.log(d)
							if (filters_applied) {
								const seller = $("#seller_filter").serialize();
								const status = $("#status_filter").serialize();
								const clinic = $("#clinic_filter").serialize();
								const type = $("#type_filter").serialize();
								const date_range = $("#dates").val();

								if (seller.length > 0) d.seller = seller;
								if (status.length > 0) d.status = status;
								if (clinic.length > 0) d.clinic = clinic;
								if (type.length > 0) d.type = type;
								if (date_range.length > 0 && chosen_time == null) d.date_range = date_range;
								if (date_range.length == 0 && chosen_time != null) d.chosen_time = chosen_time;

								console.log("Chosen Time: " + chosen_time);
							}
						},
						"dataSrc": function(json) {
							console.log("respuesta de load all", json);
							return json.data;

						},
						"error": function(response) {
							console.log("fallo en scripts/clients/load_all.php", response.responseText)
						}
					},
					autoWidth: false,
					language: {
						//url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
					},
					scrollX: true,
					order: [], // Asegúrate de no especificar ningún orden inicial
				});
				$("#update_client_status").submit(function(e) {
					e.preventDefault();

					if (this.checkValidity()) {
						$(".submit-form").attr('disabled', 'true');

						const method = $(this).attr('method');
						const url = $(this).attr('action');
						const formData = $(this).serialize();

						$.ajax({
								data: formData,
								cache: false,
								dataType: "json",
								method: method,
								url: url,
							})
							.done(function(response) {
								console.log(response);
								(response.success) ? showSweetAlert("Listo!", response.message, "success", 1500, true, false): showSweetAlert();
								$("#status_modal").modal("hide");
								table.ajax.reload();
							})
							.fail(function(response) {
								console.log(response);
								showSweetAlert();
							}).always(function() {
								$(".submit-form").removeAttr('disabled');
							});
					}
				});
				Swal.close();
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
				$(".date-btn").css('background-color', '#fff'); //Remove bg-color if any date-button was been clicked

				filters_applied = true;
				table.ajax.reload();
			});

			$(document).on('click', '.show_modal btn', function(e) {
				$("#status_modal").modal("show");
			});
			$(document).on('click', '.show_modal_exp', function(e) {
				$("#num_exp_modal").modal("show");

			});


			// Search Number Medical Record
			$(document).on("change", "#current_exp_num", function() {
				const num_med_record = $(this).val();

				if (num_med_record.length > 0) {
					$.ajax({
							data: {
								clinic: "Santafe",
								num_med_record: num_med_record,
							},
							dataType: "json",
							method: "POST",
							url: "scripts/calendar/get_patient_name.php",

						})
						.done(function(response) {
							Swal.close();
							console.log(response);
							if (response.success) {
								// $("#revision_patient_name").attr("readonly", "true");
								$("#name_px").val(response.fullname);
							} else {
								Swal.fire({
									title: "🙁",
									text: response.message,
									icon: "warning",
									showConfirmButton: false,
									timer: 2300,
									timerProgressBar: true,
								});
								//$("#revision_patient_name").removeAttr("readonly");
								//  $("#revision_patient_name").val("");
							}
						})
						.fail(function(response) {
							console.log(response.responseText);
						});
				} else {
					$("#revision_patient_name").removeAttr("readonly");
					$("#revision_patient_name").val("");
				}
			});


			$(document).on('submit', '#update_num_med', function(e) {
				e.preventDefault();

				$.ajax({
						dataType: "json",
						method: "POST",
						url: "scripts/procedures/update_num_med.php",
						data: $(this).serialize(), // Envía los datos del formulario
					
					})
						
					.done(function(response) {
						console.log(response);
						if (response.success) {
							Swal.fire({
								title: "Listo",
								text: response.message,
								icon: "success",
								showConfirmButton: false,
								timer: 2300,
								timerProgressBar: true,
							});
							$('#update_num_med')[0].reset();
							$("#num_exp_modal").modal("hide");
							
						} else {
							Swal.fire({
								title: "🙁",
								text: response.message,
								icon: "warning",
								showConfirmButton: false,
								timer: 2300,
								timerProgressBar: true,
							});
						}
					})
					.fail(function(response) {
						console.log(response.responseText);
					});
			});









			$(document).on('click', '.client-status a', function(e) {
				e.preventDefault();
				const lead_id = $(this).closest('.dropdown-menu').data('lead-id');

				const selected_status = {
					"label": $(this).html().trim(),
					"color": $(this).data('color'),
				};

				const current_status = {
					"label": $(this).closest('.dropdown').find('.custom-badge').html().trim(),
					"color": $(this).closest('.dropdown').find('.custom-badge').data('color'),
					"value": $(this).closest('.dropdown').find('.custom-badge').data('status'),
				};

				const available_inputs = {
					"num_med_record": `
														<label>Número de Expediente Asignado<span class="login-danger">*</span></label>
														<input class="form-control" type="number" name="num_med_record" id="num_med_record" required>
													`,
					"cancel_reason": `
														<label>Motivo de la Cancelación del Procedimiento<span class="login-danger">*</span></label>
														<textarea class="form-control" name="cancel_reason" id="cancel_reason" rows="4" required></textarea>
													`
				};

				let input;

				switch (selected_status.label) {
					case 'Cancelado':
						input = available_inputs.cancel_reason;
						break;
					case 'Asignar Exped.':
						input = available_inputs.num_med_record;
						break;
					case 'Próximo':
						input = "";
						break;
				}

				$("#lead_id").val(lead_id);
				$("#current_status_val").val(current_status.value);
				$("#chosen_status_lbl").val(selected_status.label);

				$("#divCurrentStatus").html(`<button class="custom-badge rounded-pill bg-${current_status.color}" readonly>${current_status.label}</button>`);
				$("#divSelectedStatus").html(`<button class="custom-badge rounded-pill bg-${selected_status.color}" readonly>${selected_status.label}</button>`);
				$("#divInput").html(input);
				$("#status_modal").modal("show");
			});


			function showSweetAlert(title, text, icon, timer, timerProgressBar, showConfirmButton) {
				Swal.fire({
					title: title || "Error",
					text: text || "Error desconocido. Contacta a administración.",
					icon: icon || "error",
					timer: 2500,
					timerProgressBar: timerProgressBar || false,
					showConfirmButton: showConfirmButton || false,
				});
			}

			function showSweetConfirm(title, message) {
				Swal.fire({
					title: title,
					text: message,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Sí, es un retoque!',
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					if (result.isConfirmed) {
						$.ajax({
							url: 'tu_archivo.php',
							method: 'POST',
							data: {
								parametro: 'valor'
							}, // Datos que deseas enviar
							success: function(response) {
								// Manejar la respuesta si es necesaria
								Swal.fire(
									'¡Hecho!',
									'La solicitud AJAX fue realizada con éxito.',
									'success'
								);
							},
							error: function(xhr, status, error) {
								// Manejar errores si es necesario
								console.error(xhr.responseText);
								Swal.fire(
									'Error',
									'Hubo un error al realizar la solicitud AJAX.',
									'error'
								);
							}
						});
					}
				});
			}
		</script>

</body>

</html>