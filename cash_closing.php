<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

//! Falta añadir permisos

session_start();
/* if (!isset($_SESSION['user_name']) || !in_array(2, $_SESSION['user_permissions'])) {
	header('Location: login.php'); // Redirigir al formulario de inicio de sesión
	exit();
} */

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Corte de Caja | ERP | Los Reyes del Injerto</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

	<!-- Fileinput -->
	<link href="assets/plugins/fileinput/fileinput.css" media="all" rel="stylesheet" type="text/css" />

	<!-- Datatables CSS -->
	<link rel="stylesheet" href="assets/plugins/datatables/css/datatables.min.css">
	<link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">
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
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Ventas</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Corte de Caja</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<form method="POST" action="scripts/finance/cash_closing/upload_register.php" id="new_register">
									<div class="row">
										<div class="col-6 col-md-3">
											<div class="input-block local-forms">
												<label>Fecha: <span class="login-danger">*</span></label>
												<input class="form-control" type="date" name="date" id="date" value="<?= date('Y-m-d'); ?>" required>
											</div>
										</div>
										<div class="col-6 col-md-3">
											<div class="input-block local-forms">
												<label>Total Efectivo (MXN): <span class="login-danger">*</span></label>
												<input class="form-control amount" type="number" name="efectivo_mxn" id="efectivo_mxn" required>
											</div>
										</div>
										<div class="col-6 col-md-3">
											<div class="input-group input-block local-forms">
												<label>Total Efectivo (USD): <span class="login-danger">*</span></label>
												<input class="form-control " type="number" name="efectivo_usd" id="efectivo_usd" required>
												<button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#exchange">Conv.</button>
											</div>
										</div>
										<div class="col-6 col-md-3">
											<div class="input-block local-forms">
												<label>Total Efectivo (Convertido): <span class="login-danger">*</span></label>
												<input class="form-control" type="number" name="efectivo_convertido" id="efectivo_convertido" readonly>
											</div>
										</div>
										<div class="col-6 col-md-3 show-after">
											<div class="input-block local-forms">
												<label>Total Tarjeta: <span class="login-danger">*</span></label>
												<div class="time-icon">
													<input type="number" class="form-control amount" name="tarjeta" id="tarjeta" required>
												</div>
											</div>
										</div>
										<div class="col-6 col-md-3 show-after">
											<div class="input-block local-forms">
												<label>Total Depósito: <span class="login-danger">*</span></label>
												<div class="time-icon">
													<input type="number" class="form-control amount" name="deposito" id="deposito" required>
												</div>
											</div>
										</div>
										<div class="col-6 col-md-3 show-after">
											<div class="input-block local-forms">
												<label>Total Transferencia: <span class="login-danger">*</span></label>
												<div class="time-icon">
													<input type="number" class="form-control amount" name="transferencia" id="transferencia" required>
												</div>
											</div>
										</div>
										<div class="col-6 col-md-3 show-after">
											<div class="input-block local-forms">
												<label>Cierre: <span class="login-danger">*</span></label>
												<div class="time-icon">
													<input type="number" class="form-control" name="cierre" id="cierre" required>
												</div>
											</div>
										</div>
										<div class="col-6 col-md-3 show-after">
											<div class="input-block local-forms">
												<label>Sucursal: <span class="login-danger">*</span></label>
												<select class="form-control" id="clinic" name="clinic" required>
													<option value="" selected disabled>Selecciona</option>
													<option value="Santafe">Santa Fe</option>
													<option value="Pedregal">Pedregal</option>
													<option value="Queretaro">Queretaro</option>
												</select>
											</div>
										</div>
										<div class="col-12 col-md-9 show-after">
											<div class="input-block local-forms">
												<label>Notas: <span class="login-danger">*</span></label>
												<div class="time-icon">
													<textarea class="form-control" cols=5 name="notes" id="notes"></textarea>
												</div>
											</div>
										</div>
										<div class="col-12">
											<div class="doctor-submit text-end">
												<button type="submit" class="btn btn-primary submit-form me-2">Generar</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<table class="table border-0 custom-table comman-table datatable mb-0 table-striped" id="cashClosingTable">
									<thead>
										<tr>
											<th>Clinica</th>
											<th>Folio</th>
											<th>Cierre (Total):</th>
											<th>Fecha</th>
											<th>Status</th>
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
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<h3 class="text-bold" id="file_upload_title"></h3>
								<div class="inputfile-container" style="display:none;">
									<input type="file" id="file" name="file[]" multiple>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12" id="divPdf" style="display:none;">
					<div class="card">
						<div class="card-body">
							<iframe id="pdfViewer" src="" width="100%" height="800px" style="border: none;"></iframe>
						</div>
						<a id="pdfDownloader" href="" target="_blank" download>Descargar</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="exchange" tabindex="-1" aria-labelledby="exchange" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="">Conversión USD a MXN</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<label>Ingresa el tipo de cambio al cual tomaste el dólar:</label>
					<input type="number" class="form-control" name="exchange_rate" id="exchange_rate" value=0>
				</div>
				<div class="modal-footer">
					<button type="button" class="submit-form btn btn-primary" id="btnExchangeRate">Actualizar</button>
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

	<script src="assets/plugins/moment/moment.min.js"></script>
	<script src="assets/plugins/datetimepicker/datetimepicker.min.js"></script>
	<!-- Datatables JS -->
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

	<!-- Fileinput -->
	<script src="assets/plugins/fileinput/fileinput.js?1.0" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/locales/es.js" type="text/javascript"></script>
	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		let table;
		$(document).ready(function() {
			DataTable.datetime('DD/MM/YYYY');

			table = $("#cashClosingTable").DataTable({
				ajax: 'scripts/finance/cash_closing/load_all.php',
				autoWidth: false,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
				},
				scrollX: true,
				"order": [
					[4, "desc"]
				],
			});


			$("#btnExchangeRate").click(function(e) {
				e.preventDefault();

				let total = 0;
				let efectivo_usd = $("#efectivo_usd").val();
				efectivo_usd = parseFloat(efectivo_usd) || 0;

				let exchange_rate = $("#exchange_rate").val();
				exchange_rate = parseFloat(exchange_rate) || 0;

				const efectivo_convertido = efectivo_usd * exchange_rate;
				$("#efectivo_convertido").val(efectivo_convertido);

				$('.amount').each(function() {
					total += parseFloat($(this).val()) || 0;
				});
				total += parseFloat($("#efectivo_convertido").val()) || 0;

				$("#cierre").val(total);
				$("#exchange").modal("hide");
			});
			$("#efectivo_usd").change(function(e) {
				e.preventDefault();
				$("#exchange").modal("show");
			});
			$(".amount").change(function(e) {
				e.preventDefault();
				let total = 0;
				$('.amount').each(function() {
					total += parseFloat($(this).val()) || 0;
				});
				total += parseFloat($("#efectivo_convertido").val()) || 0;

				$("#cierre").val(total);
			});
			$("#new_register").submit(function(e) {

				e.preventDefault();
				if (this.checkValidity() === true) {

					let method = $(this).attr('method');
					let url = $(this).attr('action');
					let formData = $(this).serialize();

					$.ajax({
							data: formData,
							cache: false,
							method: method,
							url: url,
						})
						.done(function(response) {
							if (response.success) {
								showSweetAlert("Listo!", response.message, "success");
								table.ajax.reload();
							} else {
								showSweetAlert("Error", response.message, "error");
								console.log(response);
							}
							//cargarPDF('files/cdmx/corte-caja-test.pdf');
						})
						.fail(function(response) {
							console.log(response.responseText);
							showSweetAlert();
						})
				}
			})

			$(document).on('click', '.load_files', function(e) {
				const public_id = $(this).data('public-id');
				$("#file_upload_title").html(`Cargar documentos para el corte con folio: ${public_id}`);
				e.preventDefault();
				$(".inputfile-container").fadeIn("slow");


				$.ajax({
						data: {
							'public_id': public_id,
						},
						dataType: "json",
						method: "POST",
						url: "scripts/finance/cash_closing/load_files.php",
					})
					.done(function(response) {
						$('#file').fileinput('destroy');
						$('#file').fileinput({
							allowedFileExtensions: ["jpg", "png", "jpeg", "pdf", "docx", "xlsx"],
							language: "es",
							uploadUrl: `scripts/finance/cash_closing/upload_files.php?public_id=${public_id}`,
							showRemove: false,
							showCancel: false,
							initialPreview: response.initialPreview,
							initialPreviewConfig: response.initialPreviewConfig,
							initialPreviewDownloadUrl: response.initialPreviewDownloadUrl,
							initialPreviewAsData: true,
							overwriteInitial: false,
							otherActionButtons: '<button type="button" class="kv-file-view btn btn-sm btn-kv btn-default btn-outline-secondary" title="Edit"{dataKey}><i class="fa fa-eye"></i></button>'
						});
						$(".kv-file-rotate,.file-drag-handle,.kv-file-zoom").css('display', 'none');
						$('html, body').animate({
							scrollTop: $(document).height() - $(window).height()
						}, 'slow');
					})
					.fail(function(response) {
						console.log(response);
					});

			});
			$(document).on('click', '.approve', function(e) {
				const public_id = $(this).data('public-id');

				e.preventDefault();

				Swal.fire({
					title: "",
					text: `¿Aprobar Corte de Caja ${public_id}?`,
					icon: "success",
					showCancelButton: true,
					confirmButtonColor: "#d33",
					cancelButtonColor: "#3085d6",
					confirmButtonText: "Sí, aprobar",
					cancelButtonText: "Cancelar",
				}).then((result) => {
					if (result.isConfirmed) {
						$.ajax({
								method: "POST",
								url: "scripts/finance/cash_closing/update_status.php",
								data: {
									public_id: public_id
								},
								dataType: 'json'
							})
							.done(function(response) {
								Swal.fire({
									title: "Corte aprobado",
									didClose: () => {
										location.reload();
									}
								});
							})
							.fail(function(response) {
								console.log(response);
							});
					}
				});
			});
			$(document).on('click', '.kv-file-view', function(e) {
				let key = $(this).data('key');
				let [filename, folder] = key.split('&');
				folder = folder.replace('public_id=', '');
				let url = `files/cdmx/corte-caja/${folder}/${filename}`;
				cargarPDF(url);
			});

			$(document).on("click", ".delete", function(e) {
				const receipt_id = $(this).data("id");
				const public_id = $(this).data('public-id');

				Swal.fire({
					title: "¿Estás seguro/a?",
					text: "Esta acción no se puede revertir",
					icon: "error",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Sí, eliminar",
					cancelButtonText: "Cancelar",
				}).then((result) => {
					if (result.isConfirmed) {
						$.ajax({
								data: {
									receipt_id: receipt_id,
									public_id: public_id
								},
								cache: false,
								dataType: "json",
								method: "POST",
								url: "scripts/finance/cash_closing/delete_register.php",
							})
							.done(function(response) {
								if (response.success) {
									table.ajax.reload();
									showSweetAlert("Listo!", response.message, "success");
								} else {
									console.log(response);
									showSweetAlert("Error", response.message, "error");
								}
							})
							.fail(function(response) {
								console.error(response);
								showSweetAlert();
							});
					}
				});
			});


			function cargarPDF(url) {
				$("#divPdf").fadeIn("slow");
				document.getElementById('pdfViewer').src = url;
				document.getElementById('pdfDownloader').href = url;
				$('html, body').animate({
					scrollTop: $(document).height() - $(window).height()
				}, 'slow');
			}

		});
	</script>
</body>

</html>