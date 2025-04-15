<?php
/*! Permiso : 1 / Usuarios / Registrar Usuarios! */
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

require_once "./scripts/common/connection_db.php";

session_start();
// Verifica si el user_id es diferente de 1 y 20
if ($_SESSION['user_id'] != 1 && $_SESSION['user_id'] != 20  && $_SESSION['user_id'] != 7 && $_SESSION['user_id'] != 41 && $_SESSION['user_id'] != 18) {
	header('Location: login.php');
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Vacaciones | Reyes del Injerto</title>
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
								<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item"><a href="#">Personal</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active"><a href="view_holidays.php">Vacaciones</a></li>
							</ul>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-header">
								<!-- Table Header -->
								<div class="page-table-header mb-2">
									<div class="row align-items-center">
										<div class="col">
											<div class="doctor-table-blk">
												<h3>Control y Registro de Vacaciones</h3>

												<div class="doctor-search-blk">
													<div class="add-group">
														<a type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
															<img src="assets/img/icons/plus.svg" alt>
														</a>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
								<!-- /Table Header -->
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table border-0 custom-table comman-table datatable mb-0 table-striped" id="holidaysTable">
										<thead>
											<tr>
												<th>ID</th>
												<th>Empleado</th>
												<th>Inicio</th>
												<th>Fin</th>
												<th>Notas</th>
												<th>Autorizó</th>
												<th>Opciones</th>
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
	<div class="sidebar-overlay" data-reff=""></div>

	<!-- Modal -->
	<div class="modal fade" id="addHolidayModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Añadir nuevas vacaciones</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" id="formNewHoliday">
						<div class="row">
							<div class="col-12">
								<div class="input-block local-forms">
									<label><strong>Empleado:</strong><span class="login-danger">*</span></label>
									<select name="employee" id="employee" data-placeholder="Selecciona un empleado" required>

									</select>
								</div>
							</div>
							<div class="col-12">
								<div class="input-block local-forms">
									<label><strong>Inicio:</strong><span class="login-danger">*</span></label>
									<input type="date" class="form-control" name="start" id="start" required>
								</div>
							</div>
							<div class="col-12">
								<div class="input-block local-forms">
									<label><strong>Fin:</strong><span class="login-danger">*</span></label>
									<input type="date" class="form-control" name="end" id="end" required>
								</div>
							</div>
							<div class="col-12">
								<div class="input-block local-forms">
									<label>Notas</label>
									<textarea class="form-control" name="notes" id="notes" rows="3" cols="30"></textarea>
								</div>
							</div>
							<div class="col-12">
								<div class="input-block local-forms">
									<label><strong>Autorizó:</strong><span class="login-danger">*</span></label>
									<input type="text" class="form-control" name="approved_by" id="approved_by" required>
								</div>
							</div>
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary submit-form">Agregar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
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

	<!-- Sweet Alert-->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- Datatables JS -->
	<script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatables/js/datatables.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>
	<script>
		$(document).ready(function() {
			loadHolidaysTable();

			$('#addHolidayModal').on('show.bs.modal', function(e) {
				// Aquí puedes ejecutar cualquier acción que necesites cuando la modal se abre
				console.log("modal")
				$.ajax({
						cache: false,
						dataType: "json",
						method: 'POST',
						url: './scripts/Admin/holidays/get_employees.php',
					})
					.done(function(response) {
						console.log(response)
						if (response.success) {
							$.each(response.data, function(index, value) {
								// Para cada opción, crear un nuevo elemento <option> con el valor correspondiente y agregarlo al <select>
								$('#employee').append(`<option value="${value.id}">${value.name}</option>`);
							});

							$("#employee").select2({
								tags: true,
								dropdownParent: $("#addHolidayModal .modal-body")
							});
						}
					})
					.fail(function(response) {
						console.error(response);
					});
				// Por ejemplo, puedes realizar alguna acción específica, como cargar contenido adicional, etc.
			});


			$("#formNewHoliday").submit(function(e) {
				e.preventDefault();

				if (this.checkValidity()) {
					$(".submit-form").attr('disabled', 'true');

					let form = $("#formNewHoliday").serialize();
					console.log(form)
					$.ajax({
							data: form,
							cache: false,
							dataType: "json",
							method: 'POST',
							url: 'scripts/Admin/holidays/add_holiday.php',
						})
						.done(function(response) {
							console.log(response)
							if (response.success) {
								Swal.fire({
									title: "¡Listo!",
									text: response.message,
									icon: "success",
									showConfirmButton: true,
									confirmButtonText: "OK",
									allowOutsideClick: false,
									allowEscapeKey: false
								}).then((result) => {
									if (result.isConfirmed) {
										location.reload(); // Recargar la página
									}
								});
							} else if (response.success == false) {
								Swal.fire({
									title: "Error",
									text: response.message,
									icon: "error",
									//backdrop: "linear-gradient(yellow, orange)",
									background: "white",
									timer: 2300, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
									timerProgressBar: true, // Muestra una barra de progreso
									showConfirmButton: false, // No muestra el botón de confirmación
								});
							}
						})
						.fail(function(response) {
							console.error(response);
							Swal.fire({
								title: "Error",
								text: response,
								icon: "error",
								background: "white",
								timer: 2300, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
								timerProgressBar: true, // Muestra una barra de progreso
								showConfirmButton: false, // No muestra el botón de confirmación
							});
						}).always(function() {
							// Oculta la alerta de carga, independientemente de si la solicitud AJAX fue exitosa o no
						//	Swal.close();
						});
				} else {
					
				}
			});


			$(document).on('click', '.delete_holiday', function(e) {
				e.preventDefault();

				const holiday_id = $(this).data('id');

				Swal.fire({
					title: '¿Estás seguro/a?',
					text: "Esta acción no se puede deshacer",
					icon: 'error',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Sí, eliminar',
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					if (result.isConfirmed) {
						// Si el usuario confirma, carga el formulario
						$.ajax({
								data: {
									holiday_id: holiday_id,
								},
								cache: false,
								dataType: "json",
								method: 'POST',
								url: 'scripts/Admin/holidays/delete_holiday.php',
							})
							.done(function(response) {
								if (response.success) {
									loadHolidaysTable();

									Swal.fire({
										title: "Listo!",
										text: response.message,
										icon: "success",
										showConfirmButton: false,
										timer: 3000, // Tiempo en milisegundos (1.5 segundos)
										timerProgressBar: true,
									});
								} else if (response.success == false) {
									console.log(response);
									Swal.fire({
										title: "Error",
										text: response.message,
										icon: "error",
										//backdrop: "linear-gradient(yellow, orange)",
										background: "white",
										timer: 2300, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
										timerProgressBar: true, // Muestra una barra de progreso
										showConfirmButton: false, // No muestra el botón de confirmación
									});
								}
							})
							.fail(function(response) {
								console.error(response)
								Swal.fire({
									title: "Ocurrió un error",
									text: "Por favor, contacta a administración",
									icon: "error",
									timer: 1700, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
									timerProgressBar: true, // Muestra una barra de progreso
									showConfirmButton: false, // No muestra el botón de confirmación
								});
							});
					}
				});
			});


			function loadHolidaysTable() {
				$('#holidaysTable').DataTable().destroy();
				let jquery_datatable = $("#holidaysTable").DataTable({
					ajax: './scripts/Admin/holidays/all_holidays.php',
					autoWidth: false,
					language: {
						url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
					},
					scrollX: true,
					ordering: false, // Desactiva el ordenamiento automático
					drawCallback: function(settings) {
						// Add class to the last <td> in each row after the table is drawn
						$('#usersTable tbody tr td:last-child').addClass('text-end');
					}
				});
				const setTableColor = () => {
					document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
						dt.classList.add('pagination-primary')
					})
				}
				setTableColor()
				jquery_datatable.on('draw', setTableColor);

			}
		});
	</script>
</body>

</html>