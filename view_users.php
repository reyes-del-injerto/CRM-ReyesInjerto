

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
	<title>Reyes del Injerto | Ver Usuarios</title>
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
								<li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active"></li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<div class="good-morning-blk">
					<div class="row">
						<div class="col-md-12">
							<div class="morning-user">
								<h3>Lista de Usuarios</h3>
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
														<a href="new_user.php" class="btn btn-primary add-pluss ms-2"><img src="assets/img/icons/plus.svg" alt=""></a>
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
									<table class="table border-0 custom-table comman-table datatable mb-0 table-striped" id="usersTable">
										<thead>
											<tr>
												<th>ID</th>
												<th>Nombre</th>
												<th>Usuario</th>
												<th>Último acceso</th>
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
	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>
	<script>
		$(document).ready(function() {

			let jquery_datatable = $("#usersTable").DataTable({
				ajax: 'scripts/load/users.php',
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
					$('#usersTable tbody tr td:last-child').addClass('text-end');
				}
			});

			$(".app-listing .selectBox").on("click", function() {
				$(this).parent().find("#checkBoxes").fadeToggle();
				$(this).parent().parent().siblings().find("#checkBoxes").fadeOut();
			});

			$(".invoices-main-form .selectBox").on("click", function() {
				$(this).parent().find("#checkBoxes-one").fadeToggle();
				$(this)
					.parent()
					.parent()
					.siblings()
					.find("#checkBoxes-one")
					.fadeOut();
			});
		});
	</script>

</body>

</html>