
<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

require_once "scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

session_start();
echo "hello";
if (!isset($_SESSION['user_name']) || !in_array(2, $_SESSION['user_permissions'])) {
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
	<title>Preclinic - Medical & Hospital - Bootstrap 5 Admin Template</title>
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
								<li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
								<li class="breadcrumb-item active">Admin Dashboard</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<div class="good-morning-blk">
					<div class="row">
						<div class="col-md-12">
							<div class="morning-user">
								<h3>Lista de Permisos</h3>
							</div>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="row">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-header">
							</div>
							<div class="card-body">

						<!-- Table Header -->
						<div class="page-table-header mb-2">
							<div class="row align-items-center">
								<div class="col">
									<div class="doctor-table-blk">
												<div class="doctor-search-blk">
													<div class="top-nav-search table-search-blk">

													</div>
													<div class="add-group">
														<a type="button" class="btn btn-primary add-pluss ms-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
														<img src="assets/img/icons/plus.svg" alt=""></a>
													
													</div>
												</div>
											</div>
										</div>
										<div class="col-auto text-end float-end ms-auto download-grp">
											<a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-01.svg" alt=""></a>
											<a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-02.svg" alt=""></a>
											<a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-03.svg" alt=""></a>
											<a href="javascript:;"><img src="assets/img/icons/pdf-icon-04.svg" alt=""></a>

										</div>
									</div>
								</div>
								<!-- /Table Header -->

								<div class="table-responsive">
									<table class="table border-0 custom-table comman-table datatable mb-0" id="permissionsTable">
										<thead>
											<tr>
												<th>ID</th>
												<th>Categoría</th>
												<th>Permiso</th>
												<th>Descripción</th>
												<th>Clínica</th>
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

	<div class="modal fade text-left" id="modalNewPermission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel33">
						Editar permiso.
					</h4>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<i data-feather="x"></i>
					</button>
				</div>
				<form action="scripts/add/permission.php" method="POST" id="formNewPermission">
					<div class="modal-body">
						<label for="permission_id">ID</label>
						<div class="form-group">
							<input id="permission_id" name="permission_id" type="text" placeholder="ID" class="form-control" />
						</div>
						<label for="cat">Categoría </label>
						<div class="form-group">
							<input id="cat" name="cat" type="text" placeholder="" class="form-control" />
						</div>
						<label for="name">Nombre </label>
						<div class="form-group">
							<input id="name" name="name" type="text" placeholder="" class="form-control" />
						</div>
						<label for="description">Descripción </label>
						<div class="form-group">
							<textarea id="description" name="description" type="text" placeholder="" class="form-control" rows=3></textarea>
						</div>
						<label for="clinic">Clínica </label>
						<div class="form-group">
							<input id="clinic" name="clinic" type="text" placeholder="" class="form-control" />
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
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

	<div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel33">
						Editar permiso.
					</h4>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<i data-feather="x"></i>
					</button>
				</div>
				<form action="scripts/update/permission.php" method="POST" id="formUpdatePermission">
					<div class="modal-body">
						<label for="permission_id">ID</label>
						<div class="form-group">
							<input id="permission_id" name="permission_id" type="text" placeholder="ID" class="form-control" readonly />
						</div>
						<label for="cat">Categoría </label>
						<div class="form-group">
							<input id="cat" name="cat" type="text" placeholder="" class="form-control" />
						</div>
						<label for="name">Nombre </label>
						<div class="form-group">
							<input id="name" name="name" type="text" placeholder="" class="form-control" />
						</div>
						<label for="description">Descripción </label>
						<div class="form-group">
							<input id="description" name="description" type="text" placeholder="" class="form-control" />
						</div>
						<label for="clinic">Clínica </label>
						<div class="form-group">
							<input id="clinic" name="clinic" type="text" placeholder="" class="form-control" />
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
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

	<!-- Sweet alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.5/dist/sweetalert2.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>
	<script>
		$(document).ready(function() {

			let jquery_datatable = $("#permissionsTable").DataTable({
				ajax: 'scripts/load/permissions.php',
				autoWidth: false,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
				},
				scrollX: true,
				order: [
					[0, 'desc']
				],
				drawCallback: function(settings) {
					// Add class to the last <td> in each row after the table is drawn
					$('#permissionsTable tbody tr td:last-child').addClass('text-end');
				}
			});
			const setTableColor = () => {
				document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
					dt.classList.add('pagination-primary')
				})
			}
			setTableColor()
			jquery_datatable.on('draw', setTableColor)

			$("#formUpdatePermission").submit(function(e) {
				e.preventDefault();
				let formData = $(this).serialize();
				let method = $(this).attr('method');
				let url = $(this).attr('action');

				Swal.fire({
					title: "Actualizando...",
					allowOutsideClick: false,
					showConfirmButton: false,
				});

				$.ajax({
					method: method,
					url: url,
					data: formData,
					dataType: 'json'
				}).done(function(response) {
					if (response.success) {

						Swal.fire({
							title: 'Listo!',
							text: response.message,
							icon: 'success',
							timer: 2000, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
							timerProgressBar: true, // Muestra una barra de progreso
							showConfirmButton: false
						});
					} else {
						Swal.fire({
							title: 'Error',
							text: response.message,
							icon: 'error',
							timer: 2500, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
							timerProgressBar: true, // Muestra una barra de progreso
							showConfirmButton: false // No muestra el botón de confirmación
						});
					}
				}).fail(function(response) {
					console.log(response);
				}).always(function() {
					// Oculta la alerta de carga, independientemente de si la solicitud AJAX fue exitosa o no
					Swal.close();
				});
			});
		});
		$(document).on('click', '.btn-edit', function(e) {
			e.preventDefault();
			const permission_id = $(this).data('permissionid');
			let fields = ["permission_id", "cat", "name", "description", "clinic"];

			let tr = $(this).closest('tr');
			let i = 0;

			tr.find('td').each(function() {
				$("#" + fields[i]).val($(this).text());
				i++;
			})
			$("#inlineForm").modal("show");

		})
		$(document).on('click', '.btn-delete', function(e) {
			e.preventDefault();
			const permission_id = $(this).data('permissionid');
		})
	</script>
</body>

</html>