<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
setlocale(LC_TIME, 'es_ES');

require_once "scripts/common/connection_db.php";

if (isset($_COOKIE['recordar_token'])) {
	$token = $_COOKIE['recordar_token'];

	$sql = "SELECT user_id, user_name, user_department FROM u_tokens WHERE token = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $_COOKIE['recordar_token']);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$userId = $row['user_id'];
		$userName = $row['user_name'];
		$userDepartment = $row['user_department'];

		$_SESSION['user_id'] = $userId;
		$_SESSION['user_name'] = $userName;
		$_SESSION['user_department'] = $userDepartment;

		// Obtener los permisos del usuario
		$sql = "SELECT permission_id FROM u_permission_assignment WHERE user_id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$result = $stmt->get_result();

		$user_permissions = [];
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$user_permissions[] = $row['permission_id'];
			}
		}
		$_SESSION['user_permissions'] = $user_permissions;
	} else {
		header("Location: login.php");
		exit();
	}
} else {
	header("Location: login.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Tratamientos | ERP | Los Reyes del Injerto</title>
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
	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
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
								<li class="breadcrumb-item active">Enfermería</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Lista de Tratamientos</li>
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
							</div>
							<div class="card-body">
								<!-- Table Header -->
								<div class="page-table-header mb-2">
									<div class="row align-items-center">
										<div class="col">
											<div class="doctor-table-blk">
												<div class="doctor-search-blk">
													<a type="button" class="btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Añadir nuevo <i class="fa fa-plus"></i></a>
												</div>
											</div>
										</div>
										<div class="col-auto text-end float-end ms-auto download-grp">
										</div>
									</div>
								</div>
								<!-- /Table Header -->
								<div class="table-responsive">
									<table class="table table-striped" id="table1">
										<thead>
											<tr>
												<th>Fecha Último Trat.</th>
												<th>Exp. No.</th>
												<th>Paciente</th>
												<th>Sexo</th>
												<th>Acciones</th>
												<!-- <th>Última Aplicación</th> -->
												<!-- <th>Observaciones</th> -->
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
	</div>

	<!-- Modal ADD-->
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Agregar nuevo paciente de Tratamientos</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formNewPatient" method="POST">
						<div class="row">
							<div class="col-12">
								<div class="input-block local-forms">
									<label>Número de Expediente<span class="login-danger">*</span></label>
									<input class="form-control" type="number" name="num_med_record" required>
									
								</div>
							</div>
							
							<div class="col-12">
								<div class="input-block local-forms">
									<label>Nombre del Paciente<span class="login-danger">*</span></label>
									<input class="form-control" type="text" name="name" required>
								</div>
							</div>
							<div class="col-6">
								<div class="">
									<label for="share-with">Sexo:</label>
									<select id="sex" name="sex" class="form-control">
										<option value=0 selected disabled>Selecciona ...</option>
										<option value="Masculino">Masculino</option>
										<option value="Femenino">Femenino</option>

									</select>
								</div>
							</div>
							<div class="col-6">
								<div class="">
									<label for="share-with">Clinica:</label>
									<select id="clinic" name="clinic" class="form-control">
										<option value=0 selected disabled>Selecciona ...</option>
										<option value="santafe">Santa Fe</option>
										<option value="pedregal">Pedregal</option>

									</select>
								</div>
							</div>


							
							
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Añadir</button>
					</form>
				</div>
			</div>
		</div>
	</div>


	<!-- BEGIN MODAL UPDATE -->
	<div class="modal fade" id="treatmentModal" tabindex="-1" aria-labelledby="treatmentModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="treatmentModalLabel">
						Actualizar paciente
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formUpdatePatient" method="POST">
						<div class="row">

							<div class="col-12">
								<div class="input-block local-forms">
									<label>Nombre del Paciente<span class="login-danger">*</span></label>
									<input id="namepx_edit" class="form-control" type="text" name="name" required>
								</div>
							</div>

							<input style="display: none;" id="id_trax" class="form-control hiden" type="number" name="num_med_record" required>
						</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</form>
			</div>
		</div>
	</div>
	<!-- END MODAL -->
	<div class="sidebar-overlay" data-reff=""></div>

	<!-- jQuery -->
	<script src="assets/js/jquery.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

	<!-- Feather Js -->
	<script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

	<!-- Slimscroll -->
	<script src="assets/js/jquery.slimscroll.js"></script>

	<!-- DatetimePicker -->
	<script src="assets/plugins/moment/moment.min.js"></script>
	<script src="assets/plugins/datetimepicker/datetimepicker.min.js"></script>


	<!-- Sweet Alert-->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>


	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>

	<script>
		$(document).ready(function() {

			function loadHolidaysTable() {
				console.log("Starting AJAX request");

				$.ajax({
					url: './scripts/protocol/load_all.php',
					method: 'GET', // Tipo de datos esperado de la respuesta
					success: function(response) {
						console.log(response.data)


						if (response.success) {
							// Inicializar DataTable con los datos recibidos
							$('#table1').DataTable().destroy();
							let jquery_datatable = $("#table1").DataTable({
								data: response.data,
								columns: [
                                    {
										title: "Exp. No.",
										data: "num_med_record"
									},
                                    {
										title: "Paciente",
										data: "link_name"
									},
									
									{
										title: "Sexo",
										data: "sex"
									},
									{

										title: "Último tratamiento",
										data: "date",
										render: function(data, type, row) {
											// Si la fecha es nula, mostrar "Sin información"
											if (data === null || data === '') {
												return "Sin información";
											} else {

												// Extraer la fecha y la hora
												var dateTimeSplit = data.split(' ');
												var dateSplit = dateTimeSplit[0].split('-');
												// Formatear la fecha como 'DD/MM/YYYY'
												return dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0];
											}

										}
									},
									{
										title: "Acciones",
										data: "0",
										render: function(data, type, row) {
											return data;
										},
										orderable: false
									}



								],
								autoWidth: false,
								language: {
									url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
								},
								scrollX: true,
								order: [
									[0, 'asc']
								] // Ordena por la primera columna en orden descendente
							});

							// Función para ajustar el color de la paginación
							const setTableColor = () => {
								document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
									dt.classList.add('pagination-primary');
								});
							};

							// Llamar a la función para ajustar el color de la paginación
							setTableColor();

							// Ajustar el color de la paginación en cada redibujo de la tabla
							jquery_datatable.on('draw', setTableColor);
						} else {
							console.error("Error en la respuesta del servidor: ", response);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error("AJAX error handler");
						console.error("Error en la llamada AJAX: ", textStatus, errorThrown);
					}
				});
			}

			loadHolidaysTable(); // Llamar a la función para cargar la tabla

			$("#formNewPatient").submit(function(e) {
				e.preventDefault();


				if (this.checkValidity()) {
					$(".submit-form").attr('disabled', 'true');
					const method = "POST";
					const url = "./scripts/protocol/add_protocol.php";
					const formData = $("#formNewPatient").serialize();
					console.log(formData)
					$.ajax({
							data: formData,
							cache: false,
							method: method,
							url: url,
							dataType: 'json'
						})
						.done(function(response) {
							if (response.success) {
								console.log(response);
								Swal.fire({
									title: "Listo!",
									text: response.message,
									icon: "success",
									showConfirmButton: false,
									timer: 1500, // Tiempo en milisegundos (1.5 segundos)
									timerProgressBar: true,
								}).then(function() {
									//location.reload()

								});
								$("#exampleModal").modal("hide");
								loadHolidaysTable(); // Llamar a la función para cargar la tabla



								//window.location.href = "view_treatment.php?id=" + response.id;
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
							console.log("salio mal")
							console.log("--", response.responseText);
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
			})

			$(document).on("click", ".edit", function(e) {
				console.log("editar")

				var transactionId = $(this).data('transaction-id');
				console.log('El data-transaction-id del elemento presionado es:', transactionId);

				var nombrePaciente = $(this).closest('tr').find('.single_procedure').text();
				console.log('El nombre del paciente es:', nombrePaciente);

				// Asignar el nombre al campo de entrada
				$('#namepx_edit').val(nombrePaciente);



				$('#id_trax').val(transactionId);
				$("#treatmentModal").modal("show");
			});


			$("#formUpdatePatient").submit(function(e) {
				e.preventDefault()
				const formData = $("#formUpdatePatient").serialize();
				console.log("fd", formData)

				const method = "POST";
				const url = "./scripts/protocol/update_treatment.php";
				console.log(formData)
				$.ajax({
						data: formData,
						cache: false,
						method: method,
						url: url,
						dataType: 'json'
					})
					.done(function(response) {
						if (response.success) {
							console.log(response);
							Swal.fire({
								title: "Listo!",
								text: response.message,
								icon: "success",
								showConfirmButton: false,
								timer: 1500, // Tiempo en milisegundos (1.5 segundos)
								timerProgressBar: true,
							}).then(function() {
								//location.reload()

							});
							$("#treatmentModal").modal("hide");
							loadHolidaysTable(); // Llamar a la función para cargar la tabla



							//window.location.href = "view_treatment.php?id=" + response.id;
						} else {
							console.log(response);
							console.log("fallo la respuesta")
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
						console.log("salio mal")
						console.log("--", response.responseText);
						Swal.fire({
							title: "Ocurrió un error",
							text: "Por favor, contacta a administración",
							icon: "error",
							timer: 1700, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
							timerProgressBar: true, // Muestra una barra de progreso
							showConfirmButton: false, // No muestra el botón de confirmación
						});
					})




			});

		});
	</script>
</body>

</html>