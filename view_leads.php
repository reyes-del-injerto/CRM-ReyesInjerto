<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 0); // Desactivamos la visualización de errores en la salida
require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Lista de Leads | Los Reyes del Injerto</title>
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
	<link rel="stylesheet" href="assets/plugins/datatables/css/datatables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.4.0/css/scroller.bootstrap5.min.css">
	<link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link rel="stylesheet" href="assets/plugins//toastr/toastr.css">
	<link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/view_leads.css">
</head>

<body class="mini-sidebar">
	<div class="main-wrapper">
		<?php
		require 'templates/header.php';
		require 'templates/sidebar.php';
		?>
		<div class="page-wrapper">
			<div class="content container-fluid">
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Ventas</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Leads</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="">
					<span>Recordatorios de hoy:</span>
					<script>
						let user_id = localStorage.getItem("user_id");
						console.log("ID de usuario enviado:", user_id);
					</script>
					<div class="overflow-scroll" style="max-height: 280px;">
						<ul id="notifications" style="list-style: none;"></ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<div class="card report-card">
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-12 contenedor">
											<h4>Filtros activos</h4>
											<div id="filter_badges_container"> <strong>Ninguno</strong> </div>
											<ul class="app-listing">
												<!-- Filtro Propietaria(o) -->
												<li>
													<div class="multipleSelection">
														<div class="selectBox">
															<p class="mb-0"><i data-feather="user" class="me-1 select-icon"></i> Propietaria(o)</p>
															<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
														</div>
														<div id="checkBoxes" class="filterDropDown">
															<form action="#" id="seller_filter" class="filter_forms">
																<p class="checkbox-title">Ver leads de:</p>
																<div class="selectBox-cont">
																	<label class="custom_check w-100">
																		<input type="checkbox" name="seller[]" value="Janeth Ruíz">
																		<span class="checkmark"></span> Janeth Ruíz
																	</label>
																	<label class="custom_check w-100">
																		<input type="checkbox" name="seller[]" value="Marisol Olmos">
																		<span class="checkmark"></span> Marisol Olmos
																	</label>
																	<label class="custom_check w-100">
																		<input type="checkbox" name="seller[]" value="Adriana Silva">
																		<span class="checkmark"></span>Adriana Silva
																	</label>
																</div>
																<button type="submit" class="btn w-100 btn-primary filters">Aplicar</button>
															</form>
														</div>
													</div>
												</li>
												<!-- Filtro Clinica(o) -->
												<li>
													<div class="multipleSelection">
														<div class="selectBox">
															<p class="mb-0"><i data-feather="user" class="me-1 select-icon"></i> Clinica(o)</p>
															<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
														</div>
														<div id="checkBoxes" class="filterDropDown">
															<form action="#" id="clinic_filter" class="filter_forms">
																<p class="checkbox-title">Ver leads de:</p>
																<div class="selectBox-cont">
																	<label class="custom_check w-100">
																		<input type="checkbox" name="clinic[]" value="CDMX">
																		<span class="checkmark"></span> CDMX
																	</label>
																	<label class="custom_check w-100">
																		<input type="checkbox" name="clinic[]" value="Queretaro">
																		<span class="checkmark"></span>Queretaro
																	</label>
																</div>
																<button type="submit" class="btn w-100 btn-primary filters">Aplicar</button>
															</form>
														</div>
													</div>
												</li>
												<!-- Filtro Fechas -->
												<li>
													<div class="multipleSelection">
														<div class="selectBox">
															<p class="mb-0"><i data-feather="calendar" class="me-1 select-icon"></i> Fechas</p>
															<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
														</div>
														<div id="checkBoxes" class="filterDropDown">
															<form action="#">
																<p class="checkbox-title">Ver Leads Creados En</p>
																<div class="selectBox-cont selectBox-cont-one h-auto">
																	<div class="form-custom">
																		<input class="form-control" name="dates" id="dates" placeholder="Selecciona fechas" value="">
																	</div>
																	<div class="date-list">
																		<ul>
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
												<!-- Filtro Etapa -->
												<li>
													<div class="multipleSelection">
														<div class="selectBox">
															<p class="mb-0"><i data-feather="edit" class="me-1 select-icon"></i> Etapa</p>
															<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
														</div>
														<div id="checkBoxes" class="filterDropDown">
															<form action="#" id="stages_filter" class="filter_forms">
																<p class="checkbox-title">Filtrar por Etapa</p>
																<button type="button" class="btn btn-link btn-sm" onclick="toggleCheckboxes('stages_filter', true)">Marcar todas</button>
																<button type="button" class="btn btn-link btn-sm" onclick="toggleCheckboxes('stages_filter', false)">Desmarcar todas</button>
																<div class="selectBox-cont" id="selectStage"></div>
																<button type="submit" class="btn w-100 btn-primary">Aplicar</button>
															</form>
														</div>
													</div>
												</li>
												<!-- Filtro Semáforo -->
												<li>
													<div class="multipleSelection">
														<div class="selectBox">
															<p class="mb-0"><i data-feather="filter" class="me-1 select-icon"></i> Semáforo</p>
															<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
														</div>
														<div id="checkBoxes" class="filterDropDown">
															<form action="#" id="semaforo_filter" class="filter_forms">
																<p class="checkbox-title">Filtrar por Semáforo</p>
																<button type="button" class="btn btn-link btn-sm" onclick="toggleCheckboxes('semaforo_filter', true)">Marcar todas</button>
																<button type="button" class="btn btn-link btn-sm" onclick="toggleCheckboxes('semaforo_filter', false)">Desmarcar todas</button>
																<div class="selectBox-cont" id="selectSemaforo"></div>
																<button type="submit" class="btn w-100 btn-primary">Aplicar</button>
															</form>
														</div>
													</div>
												</li>
												<!-- Filtro Calif. -->
												<li>
													<div class="multipleSelection">
														<div class="selectBox">
															<p class="mb-0"><i data-feather="edit" class="me-1 select-icon"></i> Calif.</p>
															<span class="down-icon"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
														</div>
														<div id="checkBoxes" class="filterDropDown">
															<form action="#" id="qualis_filter" class="filter_forms">
																<p class="checkbox-title">Filtrar por Calificación</p>
																<button type="button" class="btn btn-link btn-sm" onclick="toggleCheckboxes('qualis_filter', true)">Marcar todas</button>
																<button type="button" class="btn btn-link btn-sm" onclick="toggleCheckboxes('qualis_filter', false)">Desmarcar todas</button>
																<div class="selectBox-cont" id="selectQuali"></div>
																<button type="submit" class="btn w-100 btn-primary">Aplicar</button>
															</form>
														</div>
													</div>
												</li>
												<li>
													<div class="report-btn">
														<a href="view_leads.php" class="btn">
															<i data-feather="rotate-cw" class="me-1 select-icon"></i> Limpiar Filtros
														</a>
													</div>
												</li>
												<li>
													<div class="report-btn">
														<button id="export_reporte_leads" class="btn btn-success">
															<i class="fa fa-file-excel"></i> Excel
														</button>
													</div>
												</li>
											</ul>
											<script>
												document.addEventListener("DOMContentLoaded", function () {
													const sellert = document.getElementsByName("seller[]");
													if (user_id == 2) {
														sellert[0].checked = true;
														filters_applied = true;
													}
													if (user_id == 3) {
														sellert[1].checked = true;
														filters_applied = true;
													}
													if (user_id == 33) {
														sellert[2].checked = true;
														filters_applied = true;
													}
													const filterForms = document.querySelectorAll(".filter_forms");
													const filterBadgesContainer = document.getElementById("filter_badges_container");
													filterForms.forEach(form => {
														form.addEventListener("submit", function (event) {
															event.preventDefault();
															updateFilterBadges();
														});
													});
													function updateFilterBadges() {
														let badges = [];
														const sellerCheckboxes = document.querySelectorAll("#seller_filter input[name='seller[]']:checked");
														if (sellerCheckboxes.length === 0) {
															badges.push("Propietaria(o): Ninguno");
														} else if (sellerCheckboxes.length === document.querySelectorAll("#seller_filter input[name='seller[]']").length) {
															badges.push("Propietaria(o): Todos");
														} else {
															sellerCheckboxes.forEach(checkbox => {
																badges.push(`Propietaria(o): ${checkbox.value}`);
															});
														}
														const stageCheckboxes = document.querySelectorAll("#stages_filter input[name='stage[]']:checked");
														if (stageCheckboxes.length === 0) {
															badges.push("Etapa: Ninguno");
														} else if (stageCheckboxes.length === document.querySelectorAll("#stages_filter input[name='stage[]']").length) {
															badges.push("Etapa: Todos");
														} else {
															stageCheckboxes.forEach(checkbox => {
																badges.push(`Etapa: ${checkbox.value}`);
															});
														}
														const semaforoCheckboxes = document.querySelectorAll("#semaforo_filter input[name='semaforo[]']:checked");
														if (semaforoCheckboxes.length === 0) {
															badges.push("Semáforo: Ninguno");
														} else if (semaforoCheckboxes.length === document.querySelectorAll("#semaforo_filter input[name='semaforo[]']").length) {
															badges.push("Semáforo: Todos");
														} else {
															semaforoCheckboxes.forEach(checkbox => {
																badges.push(`Semáforo: ${checkbox.value}`);
															});
														}
														const qualiCheckboxes = document.querySelectorAll("#qualis_filter input[name='quali[]']:checked");
														if (qualiCheckboxes.length === 0) {
															badges.push("Calif.: Ninguno");
														} else if (qualiCheckboxes.length === document.querySelectorAll("#qualis_filter input[name='quali[]']").length) {
															badges.push("Calif.: Todos");
														} else {
															qualiCheckboxes.forEach(checkbox => {
																badges.push(`Calif.: ${checkbox.value}`);
															});
														}
														const dateRange = document.querySelector("#dates").value;
														if (dateRange) {
															badges.push(`Fechas: ${dateRange}`);
														}
														if (badges.length > 0) {
															filterBadgesContainer.innerHTML = badges.map(badge => `<span class="badge">${badge}</span>`).join(" ");
														} else {
															filterBadgesContainer.innerHTML = "<strong>Ninguno</strong>";
														}
													}
													const semaforoColors = {
														'Ya no responde': '#4f0e00',
														'No es candidato': '#a900d6',
														'No respondió desde el primer mensaje': '#d80000',
														'Interesado': '#14db73',
														'Mando fotografias': '#ce9e03',
														'Agendo valoración': '#35a0ea',
														'Tratamineto': '#e174f5',
														'Basura': '#cb6310',
														'Cierre': '#009346',
														'Promoción': '#00ffe8'
													};
													const semaforos = Object.keys(semaforoColors);
													semaforos.forEach(function (value) {
														const option = `<label class="custom_check w-100">
															<input type="checkbox" name="semaforo[]" value="${value}">
															<span class="checkmark"></span> ${value}
														</label>`;
														$("#selectSemaforo").append(option);
													});
													const dateButtons = document.querySelectorAll(".date-btn");
													dateButtons.forEach(button => {
														button.addEventListener("click", function (event) {
															event.preventDefault();
															const value = button.getAttribute("data-value");
															const dateInput = document.querySelector("#dates");
															switch (value) {
																case "today":
																	dateInput.value = new Date().toLocaleDateString("es-MX");
																	break;
																case "yesterday":
																	const yesterday = new Date();
																	yesterday.setDate(yesterday.getDate() - 1);
																	dateInput.value = yesterday.toLocaleDateString("es-MX");
																	break;
																case "thisweek":
																	const today = new Date();
																	const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
																	dateInput.value = firstDayOfWeek.toLocaleDateString("es-MX") + " - " + new Date().toLocaleDateString("es-MX");
																	break;
																case "thismonth":
																	const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
																	dateInput.value = firstDayOfMonth.toLocaleDateString("es-MX") + " - " + new Date().toLocaleDateString("es-MX");
																	break;
																case "all":
																	dateInput.value = "";
																	break;
															}
															updateFilterBadges();
														});
													});
													updateFilterBadges();
												});
											</script>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped" id="table1">
									<thead>
										<tr>
											<th>ID</th>
											<th>Nombre Completo</th>
											<th>Clinica</th>
											<th>Teléfono</th>
											<th>Interés en</th>
											<th>Etapa</th>
											<th>Semáforo</th>
											<th>Propietaria(o)</th>
											<th>Actividad proxima</th>
											<th>Valoración</th>
											<th>Status</th>
											<th>Opt.</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<script>
				function toggleCheckboxes(formId, check) {
					var checkboxes = document.querySelectorAll('#' + formId + ' input[type="checkbox"]');
					checkboxes.forEach(function (checkbox) {
						checkbox.checked = check;
					});
				}
			</script>
		</div>
	</div>
	<div class="notification-box">
		<div class="msg-sidebar notifications msg-noti">
			<div class="topnav-dropdown-header">
				<span>Recordatorios de hoy</span>
			</div>
			<div class="drop-scroll msg-list-scroll" id="msg_list">
				<ul class="list-box"></ul>
			</div>
		</div>
	</div>
	<div class="sidebar-overlay"></div>
	<script src="assets/js/jquery.min.js" type="text/javascript"></script>
	<script src="assets/plugins/toastr/toastr.min.js"></script>
	<script src="assets/plugins/toastr/toastr.js"></script>
	<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js" type="text/javascript"></script>
	<script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js" type="text/javascript"></script>
	<script src="assets/js/jquery.slimscroll.js" type="text/javascript"></script>
	<script src="assets/js/select2.min.js" type="text/javascript"></script>
	<script src="assets/plugins/moment/moment.min.js"></script>
	<script src="assets/plugins/datetimepicker/datetimepicker.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.5/dist/sweetalert2.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
	<script src="assets/js/app.js" type="text/javascript"></script>
	<script src="assets/js/view_lead.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="assets/js/export_reporte_leads.js"></script>
	<script>
		moment.locale('es');
		let table;
		let filters_applied = false;
		let date_range;
		let chosen_time = null;
		let allStage = true;
		$(document).ready(function () {
			console.log(localStorage.getItem("user_id"));
			let user_id = localStorage.getItem("user_id");
			const o = "rtl" === $("html").attr("data-textdirection");
			toastr.options = {
				extendedTimeOut: 0,
				timeOut: 0,
				tapToDismiss: true,
			};

			// Mapeo de colores para el semáforo (debe coincidir con el PHP)
			var semaforoColors = {
				'Ya no responde': '#4f0e00',
				'No es candidato': '#a900d6',
				'No respondió desde el primer mensaje': '#d80000',
				'Interesado': '#14db73',
				'Mando fotografias': '#ce9e03',
				'Agendo valoración': '#35a0ea',
				'Tratamineto': '#e174f5',
				'Basura': '#cb6310',
				'Cierre': '#009346',
				'Promoción': '#00ffe8'
			};

			// Establece por defecto el rango de fechas del mes actual
			var startDate = moment().startOf('month').format('DD/MM/YYYY');
			var endDate = moment().endOf('month').format('DD/MM/YYYY');
			$("#dates").val(startDate + " - " + endDate);
			filters_applied = true;

			// Cada vez que se modifique un input dentro de los formularios de filtros, se recarga la tabla
			$(document).on('change', '.filter_forms input', function () {
				filters_applied = true;
				updateFilterBadges();
				table.ajax.reload();
			});

			$(document).on('click', '.update-stage', function (event) {
				event.preventDefault();
				var lead_id = $(this).data('id');
				var new_stage = $(this).data('value');
				var dropdownButton = $(this).closest('.dropdown').find('.btn-light');
				$.ajax({
					url: 'scripts/sales/lead_tasks/update_stage.php',
					type: 'POST',
					data: {
						lead_id: lead_id,
						new_stage: new_stage
					},
					success: function (response) {
						var data = JSON.parse(response);
						if (data.status === 'success') {
							dropdownButton.text(new_stage);
						} else {
							alert('Error al actualizar la etapa: ' + data.message);
						}
					},
					error: function () {
						alert('Error al actualizar la etapa');
					}
				});
			});

			// Handler para actualizar el semáforo
			$(document).on('click', '.update-semaforo', function (event) {
				event.preventDefault();
				var lead_id = $(this).data('id');
				var new_semaforo = $(this).data('value');
				var dropdownButton = $('#dropdownMenuButtonSemaforo_' + lead_id);
				$.ajax({
					url: 'scripts/sales/lead_tasks/update_semaforo.php',
					type: 'POST',
					data: {
						lead_id: lead_id,
						new_semaforo: new_semaforo
					},
					success: function (response) {
						var data = JSON.parse(response);
						if (data.status === 'success') {
							dropdownButton.text(new_semaforo);
							if (semaforoColors[new_semaforo]) {
								if (new_semaforo !== 'Interesado' && new_semaforo !== 'Promoción') {
									dropdownButton.css({ 'background-color': semaforoColors[new_semaforo], 'color': '#fff' });
								} else {
									dropdownButton.css({ 'background-color': semaforoColors[new_semaforo], 'color': '' });
								}
							} else {
								dropdownButton.css('background-color', '');
							}
						} else {
							alert('Error al actualizar el semáforo: ' + data.message);
						}
					},
					error: function () {
						alert('Error al actualizar el semáforo');
					}
				});
			});

			function realizarPeticion() {
				let now = new Date();
				let year = now.getFullYear();
				let month = String(now.getMonth() + 1).padStart(2, '0');
				let day = String(now.getDate()).padStart(2, '0');
				let hour = String(now.getHours()).padStart(2, '0');
				let minute = String(now.getMinutes()).padStart(2, '0');
				let second = String(now.getSeconds()).padStart(2, '0');
				let currentDateTime = `${year}-${month}-${day} ${hour}:${minute}:${second}`;
				console.log(" currentDateTime enviado :", currentDateTime);
				let secondsUntilNextMinute = 60 - now.getSeconds();
				let delay = secondsUntilNextMinute * 1000;
				setTimeout(function () {
					$.ajax({
						url: 'scripts/sales/lead_tasks/alerts.php',
						type: 'POST',
						data: {
							user_id: user_id,
							current_datetime: currentDateTime
						},
						success: function (response) {
							console.log('Respuesta del servidor:', response);
							if (response.success) {
								if (!response.notifications[0].no_task) {
									let datanoti = response.notifications[0];
									toastr.success(
										"Recordatorio: " + datanoti.name +
										" - Asunto: " + datanoti.subject + " - Notas: " + datanoti.comments, {
										positionClass: "toast-top-right",
										rtl: o,
										timeOut: 0,
										extendedTimeOut: 0,
										tapToDismiss: true
									});
								} else {
									console.log('Sin notificaciones para este momento');
								}
							} else {
								console.error('Error en la petición AJAX:', response.message);
							}
						},
						error: function (xhr, status, error) {
							console.error('Error en la petición AJAX:', error);
						},
						complete: function () {
							realizarPeticion();
						}
					});
				}, delay);
			}
			realizarPeticion();
			$('#dates').daterangepicker({
				autoUpdateInput: false,
				locale: {
					cancelLabel: 'Salir',
					applyLabel: 'Aplicar'
				}
			});
			function LoadDataTable() {
				console.time("Tiempo de carga de la tabla");
				DataTable.datetime('DD/MM/YYYY');
				table = $("#table1").DataTable({
					"serverSide": true,
					"processing": true,
					"ajax": {
						"url": "scripts/sales/load_leads.php",
						"type": "POST",
						"data": function (d) {
							if (filters_applied) {
								const seller = $("#seller_filter").serialize();
								const stage = $("#stages_filter").serialize();
								const quali = $("#qualis_filter").serialize();
								const clinic = $("#clinic_filter").serialize();
								const semaforo = $("#semaforo_filter").serialize();
								const date_range = $("#dates").val();
								if (seller.length > 0) d.seller = seller;
								if (clinic.length > 0) d.clinic = clinic;
								if (stage.length > 0) d.stage = stage;
								if (quali.length > 0) d.quali = quali;
								if (semaforo.length > 0) d.semaforo = semaforo;
								if (date_range.length > 0 && chosen_time == null) d.date_range = date_range;
								if (date_range.length == 0 && chosen_time != null) d.chosen_time = chosen_time;
							}
						},
						"dataSrc": function (json) {
							console.log(json);
							console.timeEnd("Tiempo de carga de la tabla");
							return json.data;
						},
						"error": function (response) {
							console.log('Hay un error');
							showSweetAlert();
						}
					},
					"columns": [
						{ "data": 0 },
						{ "data": 1, "className": "table-column-name" },
						{ "data": 2 },
						{ "data": 3 },
						{ "data": 4 },
						{ "data": 5 },
						{ "data": 6 },
						{ "data": 7 },
						{ "data": 8 },
						{ "data": 9, "className": "table-column-task" },
						{ "data": 10 },
						{ "data": 11 }
					],
					language: {
						url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
					},
					"lengthMenu": [
						[10, 25, 30, 50, -1],
						[10, 25, 30, 50, "Todos"]
					],
					"pageLength": 15,
					"deferRender": true,
					"order": [0, 'desc']
				});
			}
			LoadDataTable();
			$(document).on('click', '.delete_lead_button', function (e) {
				e.preventDefault();
				let lead_id = $(this).data('id');
				if (confirm('¿Estás seguro de que deseas eliminar este lead?')) {
					fetch('scripts/sales/delete_lead.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: new URLSearchParams({ lead_id: lead_id })
					})
						.then(response => response.json())
						.then(data => {
							if (data.status === 'success') {
								alert('Lead eliminado correctamente');
								table.ajax.reload();
							} else {
								alert('Error al eliminar el lead: ' + data.message);
							}
						})
						.catch(error => {
							console.error('Error en la solicitud de eliminación:', error);
							alert('Error inesperado. Verifica la consola para más detalles.');
						});
				}
			});
			const stages = [
				"Nuevo Lead",
				"En prospección",
				"Interesado",
				"Agendó valoración",
				"Valorado",
				"Dio anticipo",
				"Cerrado",
				"Canceló",
				"Reagendó",
				"Cliente",
				"No interesado"
			];
			stages.forEach(function (value, index) {
				const option = `<label class="custom_check w-100">
		<input type="checkbox" name="stage[]" value="${value}">
		<span class="checkmark"></span> ${value}
	</label>`;
				$("#selectStage").append(option);
			});
			const qualif = ["En conversación", "Descartado", "Inactivo", "Mal prospecto", "Interesado", "Seguimiento", "En negociación"];
			qualif.forEach(function (value, index) {
				const option = `<label class="custom_check w-100">
		<input type="checkbox" name="quali[]" value="${value}">
		<span class="checkmark"></span> ${value}
	</label>`;
				$("#selectQuali").append(option);
			});
		});
		$(document).on('submit', '.filter_forms', function (e) {
			e.preventDefault();
			$(this).closest(".filterDropDown").fadeOut("slow");
			filters_applied = true;
			table.ajax.reload();
		});
		$(document).on('click', '.date-btn', function (e) {
			$(".date-btn").css('background-color', '#fff');
			$(this).css('background-color', '#e0ac44');
			$("#dates").val("");
			$(this).closest(".filterDropDown").fadeOut("slow");
			filters_applied = true;
			chosen_time = $(this).data('value');
			table.ajax.reload();
		});
		$('#dates').on('apply.daterangepicker', function (ev, picker) {
			const dates_value = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY');
			$(this).val(dates_value);
			chosen_time = null;
			$(".date-btn").css('background-color', '#fff');
			filters_applied = true;
			table.ajax.reload();
		});
		$(document).on('click', '.status-notif', function (e) {
			const marker = $(this).closest('.timeline-block-right').find('.marker')
			var element = marker.prevObject.prevObject[0];
			let id_task_complete = element.dataset.notifId;
			$.ajax({
				data: { task_id: id_task_complete },
				cache: false,
				dataType: 'json',
				method: 'POST',
				url: 'scripts/sales/lead_tasks/mark_complete.php',
			})
				.done(function (response) {
					if (response.success) {
						showTasks();
					}
				})
				.fail(function (response) {
					console.error("fail", response);
				});
		});
		function showTasks() {
			const user_id = localStorage.getItem("user_id");
			$.ajax({
				url: 'scripts/sales/lead_tasks/load_all.php',
				type: 'POST',
				data: { user_id: user_id },
				dataType: 'html',
				success: function (response) {
					document.getElementById('notifications').innerHTML = response;
				},
				error: function (xhr, status, error) {
					console.error('Error en la petición AJAX:', { status: status, error: error, responseText: xhr.responseText });
				}
			});
		}
		showTasks();
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
	</script>
</body>

</html>
