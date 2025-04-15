<?php
/*! Permiso : 1 / Usuarios / Registrar Usuarios! */
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

require_once "scripts/common/connection_db.php";

session_start();
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
	<title>Añadir Usuario</title>
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
	<style>
		.v-center {
			min-height: 200px;
			display: flex;
			justify-content: center;
			flex-flow: column wrap;
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
								<li class="breadcrumb-item"><a href="index.php">Inicio </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Usuarios</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Añadir Usuario</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<div class="row">
					<div class="col-sm-12">

						<div class="card">
							<div class="card-body">
								<form id="formNewUser" method="POST" action="scripts/Admin/users/add_user.php">
									<div class="row">
										<div class="col-12">
											<div class="form-heading">
												<h4>Añadir Usuario</h4>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-6">
											<div class="input-block local-forms">
												<label>Nombre y Apellido<span class="login-danger">*</span></label>
												<input class="form-control" name="nombre" type="text" required>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-6">
											<div class="input-block local-forms">
												<label>Usuario<span class="login-danger">*</span></label>
												<input class="form-control" name="usuario" type="text" required>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-6">
											<div class="input-block local-forms cal-icon">
												<label>Contraseña <span class="login-danger">*</span></label>
												<input class="form-control" name="contrasena" type="password" required>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-6">
											<div class="input-block local-forms cal-icon">
												<label>Repetir Contraseña <span class="login-danger">*</span></label>
												<input class="form-control" name="verif_contrasena" type="password" required>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-6">
											<div class="input-block local-forms cal-icon">
												<label>Clínica <span class="login-danger">*</span></label>
												<select class="form-control" name="clinic" required>
													<option value=0 selected disabled>Selecciona</option>
													<option value="Santa Fe">CDMX</option>
													<option value="Queretaro">Queretaro</option>
													<option value="Todas">Todas</option>
												</select>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-6">
											<div class="input-block local-forms cal-icon">
												<label>Departamento <span class="login-danger">*</span></label>
												<select class="form-control" name="department" required>
													<option value=0 selected disabled>Selecciona</option>
													<option value="Ventas">Ventas</option>
													<option value="Gerencia">Gerencia</option>
													<option value="Medicos">Medicos</option>
													<option value="Recepcion">Recepcion</option>
													<option value="Enfermeria">Enfermeria</option>
													<option value="Marketing">Marketing</option>
													<option value="Recepcion">Recepcion</option>
													<option value="Development">Development</option>
													<option value="Medicos">Medicos</option>

												</select>
											</div>
										</div>

										<div class="container">
											<div class="row">
												<div class="col-5">
													<div class="list-group" id="list1">
														<a href="#" class="list-group-item active">Permisos Disponibles <input title="toggle all" type="checkbox" class="all pull-right"></a>
														<?php
														$sql = "SELECT * FROM u_permissions";

														$query = $conn->query($sql);
														if ($query) {
															if ($query->num_rows > 0) {
																while ($row = $query->fetch_object()) {
																	echo "<a href='#' class='list-group-item'>{$row->name} <input type='checkbox' value='{$row->id}' class='pull-right'></a>";
																}
															}
														} ?>
													</div>
												</div>
												<div class="col-md-2 v-center">
													<button type="button" title="Send to list 2" class="btn btn-default center-block add"><i class="fa fa-arrow-right"></i></button>
													<button type="button" title="Send to list 1" class="btn btn-default center-block remove"><i class="fa fa-arrow-left"></i></button>
												</div>
												<div class="col-5">
													<div class="list-group" id="list2">
														<a href="#" class="list-group-item active">Permisos Asignados <input title="toggle all" type="checkbox" class="all pull-right"></a>
													</div>
												</div>
											</div>
											<div class="col-12">
												<div class="doctor-submit text-end">
													<button type="submit" class="btn btn-primary submit-form me-2">Crear Usuario</button>
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

		<!-- SweetAlert -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				/* const currentPath = window.location.pathname;
				alert(currentPath)
				var sidebarLinks = document.querySelectorAll('.sidebar-menu a');
				sidebarLinks.forEach(function(link) {
					alert(link.getAttribute('href'))
					if (link.getAttribute('href') === currentPath) {
						link.parentNode.classList.add('active');
					}
				});*/

				$('.add').click(function() {
					$('.all').prop("checked", false);
					var items = $("#list1 input:checked:not('.all')");
					var n = items.length;
					if (n > 0) {
						items.each(function(idx, item) {
							var choice = $(item);
							choice.prop("checked", false);
							choice.parent().appendTo("#list2");
						});
					} else {
						alert("Choose an item from list 1");
					}
				});

				$('.remove').click(function() {
					$('.all').prop("checked", false);
					var items = $("#list2 input:checked:not('.all')");
					items.each(function(idx, item) {
						var choice = $(item);
						choice.prop("checked", false);
						choice.parent().appendTo("#list1");
					});
				});

				/* toggle all checkboxes in group */
				$('.all').click(function(e) {
					e.stopPropagation();
					var $this = $(this);
					if ($this.is(":checked")) {
						$this.parents('.list-group').find("[type=checkbox]").prop("checked", true);
					} else {
						$this.parents('.list-group').find("[type=checkbox]").prop("checked", false);
						$this.prop("checked", false);
					}
				});

				$('[type=checkbox]').click(function(e) {
					e.stopPropagation();
				});

				/* toggle checkbox when list group item is clicked */
				$('.list-group a').click(function(e) {

					e.stopPropagation();

					var $this = $(this).find("[type=checkbox]");
					if ($this.is(":checked")) {
						$this.prop("checked", false);
					} else {
						$this.prop("checked", true);
					}

					if ($this.hasClass("all")) {
						$this.trigger('click');
					}
				});

				$("#formNewUser").submit(function(e) {
					e.preventDefault();
					const method = $(this).attr('method');
					const url = $(this).attr('action');

					const formData = new FormData(this); // Crea FormData directamente a partir del formulario

					// Obtener los permisos seleccionados
					const assigned = $("#list2 a:not(:first-child)>input");
					const permissionArray = [];

					assigned.each(function() {
						const permission_id = $(this).val();
						permissionArray.push(permission_id);
					});

					const permissionsString = permissionArray.join(',');
					formData.append('permissions', permissionsString);

					// Mostrar alerta de carga
					Swal.fire({
						title: "Cargando...",
						allowOutsideClick: false,
						showConfirmButton: false,
					});

					$.ajax({
							data: formData,
							processData: false,
							contentType: false,
							cache: false,
							dataType: "json",
							method: method,
							url: url,
						})
						.done(function(response) {
							if (response.success) {
								Swal.fire({
									title: "Listo!",
									text: "Usuario registrado :)",
									icon: "success",
									showConfirmButton: false,
									timer: 3000, // Tiempo en milisegundos (3 segundos)
									timerProgressBar: true,
								}).then(function() {
									// Puedes descomentar la siguiente línea para redirigir después de que el usuario se registre
									// window.location.href = "view_users.php";
								});
							} else {
								Swal.fire({
									title: "Error",
									text: response.message,
									icon: "error",
									background: "white",
									timer: 2300, // Tiempo en milisegundos
									timerProgressBar: true,
									showConfirmButton: false,
								});
							}
						})
						.fail(function(response) {
							console.error(response);
							Swal.fire({
								title: "Ocurrió un error",
								text: "Por favor, contacta a administración",
								icon: "error",
								timer: 1700, // Tiempo en milisegundos
								timerProgressBar: true,
								showConfirmButton: false,
							});
						});
				});

			});
		</script>
</body>

</html>