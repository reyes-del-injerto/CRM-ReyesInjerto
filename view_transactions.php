<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

session_start();
$permissions_needed = array(3, 4, 5, 6);


?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<!-- <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png"> -->
	<title>Gastos | ERP | Los Reyes del Injerto</title>
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

	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

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
								<li class="breadcrumb-item"><a href="index.html">Inicio </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Finanzas</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Gastos</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="good-morning-blk">
					<div class="row">
						<div class="col-12 col-md-4">
							<label>Ver ingresos de</label>
							<input class="form-control" name="dates" id="dates" required>
						</div>
						<div class="col-12 col-md-3">
							<label>Sucursal:</label>
							<select class="form-control" name="clinic" id="clinic" <?= $disabled; ?>>
								<option value="" disabled>Selecciona</option>
								<option value="Santafe">Santa Fe</option>
								<option value="Pedregal">Pedregal</option>
								<option value="Queretaro">Queretaro</option>
								<option value="Ambas" selected>Todas</option>
							</select>
						</div>
						<div class="col-12 col-md-5">
							<h2 class="mt-4" id="total"></h2>
						</div>
					</div>
					<div class="row mt-4">
						<div class="col-12 col-md-4">
							<button type="button" class="btn btn-outline-warning" id="add_new_expense">Nuevo gasto</button>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table id="expensesTable" class="table table-striped">
						<thead>
							<tr>
								<th class="ps-0">ID</th>
								<th>Descripción</th>
								<th>Monto</th>
								<th>Categoría</th>
								<th>Forma de Pago</th>
								<th>Fecha</th>
								<th>Clínica</th>
								<th>Opc</th>
							</tr>
						</thead>
						<tbody class="text-dark " id="tBodyExpenseTable">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="sidebar-overlay" data-reff=""></div>

	<!-- BEGIN MODAL -->
	<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="expenseModalLabel">
						Añadir Gasto
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="#" id="expenseForm" class="formTransactions">
						<div class="row">
							<input type="hidden" value=1 name="type_submit" id="type_submit" class="form-control">
							<input type="hidden" value="" name="transaction_id" id="transaction_id" class="form-control">
							<input type="hidden" value="" name="user_id" id="user_id" class="form-control">
							<script>
								const user_id = localStorage.getItem("user_id")
								document.getElementById("user_id").value = user_id;
							</script>
							<div class="col-md-12">
								<div class="">
									<textarea required id="description" name="description" type="text" class="form-control" placeholder="Descripción de la transacción" rows="3"></textarea>
								</div>
							</div>
							<div class="col-md-12 mt-4">
								<div class="">

									<label for="exampleDataList" class="form-label">Establecimiento</label>
									<input required id="store" name="store" list="datalistOptions" type="text" class="form-control" placeholder="Establecimiento" />
									<datalist id="datalistOptions">
										<option value="Sams club">
										<option value="Office depot">
										<option value="Walmart">
										<option value="Littel Caesar´s">
										<option value="Hotel Paragon">
										<option value="Farmacia del Ahorro">
										<option value="Oxxo">
										<option value="Home Depot">
										<option value="Seven eleven">
									</datalist>
								</div>
							</div>

							<div class="col-md-6 mt-6">
								<div class="">
									<label for="share-with">Categoría:</label>
									<select required id="cat_id" name="cat_id" class="form-control" required>
										<option value="" selected disabled></option>
									</select>
								</div>
							</div>
							<div class="col-md-6 mt-6">
								<div class="">
									<label for="share-with">Subcategoría:</label>
									<select required id="sub_cat_id" name="sub_cat_id" class="form-control" required>
										<option value="" selected disabled></option>
									</select>
								</div>
							</div>

							<div class="col-md-6 mt-4">
								<div class="">
									<input required id="date" name="date" type="date" class="form-control" />
								</div>
							</div>


							<div class="col-md-6 mt-4">
								<div>
									<label for="payment_method_id">Método de Pago:</label>
									<select required id="payment_method_id" name="payment_method_id" class="form-control">
										<option value="" selected disabled>Selecciona ...</option>
										<option value="1">Efectivo</option>
										<option value="2">Tarjeta</option>
										<option value="3">Transferencia</option>
										<option value="4">Depósito</option>
									</select>
								</div>
							</div>

							<div class="col-md-6 mt-4">
								<div class="">
									<label for="share-with">Monto de la compra: </label>
									<input required id="amount" name="amount" type="number" step="0.01" class="form-control" placeholder="" />
								</div>
							</div>
							<div class="col-md-6 mt-4">
								<div>
									<label for="clinicModal">Sucursal</label>
									<select required id="clinicModal" name="clinic" class="form-control">
										<option value="" selected disabled>Selecciona ...</option>
										<option value="Santafe">Santa Fe</option>
										<option value="Pedregal">Pedregal</option>
										<option value="Queretaro">Queretaro</option>
									</select>
								</div>
							</div>

						</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="btnEditExpense" class="btn btn-success btn-update-event">
						Guardar cambios
					</button>
					<button type="submit" id="btnSubmitExpense" class="btn btn-outline-dark btn-add-event">
						Añadir gasto
					</button>
				</div>
				</form>
			</div>
		</div>
	</div>
	<!-- END MODAL -->
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

	<!-- DataTables -->
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/accent-neutralise.js"></script>
	<script src="//cdn.datatables.net/plug-ins/2.0.7/filtering/type-based/diacritics-neutralise.js"></script>

	<!-- Sweet Alert JS -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>

	<!-- Export To Excel -->
	<script lang="javascript" src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>

	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

	<script>
		let table;



		$(document).ready(function() {
			DataTable.datetime('DD/MM/YYYY');
			moment.locale('es');

			$('input[name="dates"]').daterangepicker({
				startDate: moment().startOf('month'),
				endDate: moment(),
				maxDate: moment(),
			});

			$("#clinic,#dates").change(function(e) {
				e.preventDefault();
				table.ajax.reload();
			});

			$("#add_new_expense").click(function(e) {
				e.preventDefault();
				$('#expenseForm')[0].reset();
				$(".btn-update-event").hide();
				$(".btn-add-event").show();
				$("#type_submit").val(1);
				$("#expenseModal").modal("show");
			});

			$("#expenseForm").submit(function(e) {
				e.preventDefault()
				const formData = $(this).serialize();
				const type_submit = $("#type_submit").val();

				(type_submit == 1) ? addTransaction(formData): updateTransaction(formData);
			});
			loadTable();
			loadCats();

		});

		function loadTable() {
			table = $("#expensesTable").DataTable({
				"ajax": {
					"url": "scripts/finance/expenses/load_all.php",
					"type": "POST",
					"data": function(d) {
						console.log("respuesta??: ", d)
						d.dates = $("#dates").val();
						d.clinic = $("#clinic").val();
					},
					"dataSrc": function(json) {
						console.log("respuesta??2: ", json)
						const total = parseTotal(json.total);
						$("#total").html(`Total: ${total}`);
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
				order: [
					[0, 'desc']
				],
				"lengthMenu": [
					[10, 25, 50, -1],
					[10, 25, 50, "Todos"]
				],
				"pageLength": -1,
			});
		}

		function loadCats() {
			$.ajax({
				method: "POST",
				url: "scripts/finance/expenses/load_cats.php",
				dataType: 'json'
			}).done(function(response) {
				const cats = response.cats;

				if (response.success) {
					cats.forEach(function(value) {
						const cat_option = `<option  value=${value.id}>${value.name}</option>`;
						$("#cat_id").append(cat_option);
					});
				}
			}).fail(function(response) {
				showSweetAlert();
			});
		}

		$("#cat_id").change(function() {
			const selectedOption = $(this).find('option:selected');
			console.log(selectedOption)
			const cat_name = selectedOption.data('value');
			const cat_id = selectedOption.val();
			console.log(cat_id)

			if (cat_id == 15) {
				console.log("sies")
				$("#sub_cat_id").replaceWith('<input id="sub_cat_id" name="sub_cat_id" type="text" class="form-control" placeholder="Subcategoría" required>');
			} else {
				if ($("#sub_cat_id").is("input")) {
					$("#sub_cat_id").replaceWith('<select id="sub_cat_id" name="sub_cat_id" class="form-control" required><option value="" selected disabled></option></select>');
				}
				loadSubCats(cat_id);
			}
		});

		function loadSubCats(cat_id) {
			$.ajax({
				method: "POST",
				url: "scripts/finance/expenses/load_sub_cats.php",
				data: {
					cat_id: cat_id
				},
				dataType: 'json'
			}).done(function(response) {
				const sub_cats = response.sub_cats;
				$("#sub_cat_id").empty(); // Clear previous subcategories

				if (response.success) {
					sub_cats.forEach(function(value) {
						const sub_cat_option = `<option value=${value.id}>${value.name}</option>`;
						$("#sub_cat_id").append(sub_cat_option);
					});
				}
			}).fail(function(response) {
				showSweetAlert();
			});
		}
		$(document).on("click", ".delete", function(e) {
			e.preventDefault();
			const transaction_id = $(this).data('transaction-id');
			Swal.fire({
				title: '¿Estás seguro de borrar el gasto?',
				text: "Esta acción no se puede revertir.",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sí, borrar.',
				cancelButtonText: 'Cancelar'
			}).then((result) => {
				if (result.isConfirmed) {
					console.log(transaction_id)
					$.ajax({
							data: {
								transaction_id: transaction_id
							},
							dataType: "json",
							method: "POST",
							url: "scripts/finance/expenses/delete_transaction.php",
						})
						.done(function(response) {
							console.log(response)
							if (response.success) {
								Swal.fire({
									title: 'Listo',
									text: response.message,
									icon: 'success',
									timer: 2000,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading()
										table.ajax.reload();
									}
								});
							}
						})
						.fail(function(response) {
							console.log(response.responseText);
							showSweetAlert();
						});
				}
			});
		});

		$(document).on("click", ".edit", function(e) {
			e.preventDefault();
			console.log("edit")
			$("#type_submit").val(0); // update

			$("#expenseModalLabel").html("Actualizar Gasto");
			$("#btnSubmitExpense").hide();
			$("#btnEditExpense").show();
			$("#expenseModal").modal("show");

			const transaction_id = $(this).data('transaction-id');
			console.log("🚀 ~ $ ~ transaction_id:", transaction_id)

			$.ajax({
				method: "POST",
				url: "scripts/finance/expenses/load_single.php",
				data: {
					transaction_id: transaction_id
				},
				dataType: 'json'
			}).done(function(response) {
				const t = response.transaction;
				console.log(t)

				if (response.success) {
					let ParseAmount = t.amount * -1;
					$("#transaction_id").val(t.id);
					$("#description").val(t.description);
					$("#date").val(t.date.split(" ")[0]); // Aquí extraemos solo la parte de la fecha
					$("#amount").val(ParseAmount);
					$("#store").val(t.store);
					$("#cat_id").val(t.cat_id);
					$("#payment_method_id").val(t.payment_method_id);
					$("#clinicModal").val(t.clinic);
				}
			}).fail(function(response) {
				console.log(response.responseText);
				showSweetAlert();
			});
		});

		function updateTransaction(formData) {
			$.ajax({
				method: "POST",
				url: "scripts/finance/expenses/update_transaction.php",
				dataType: 'json',
				data: formData
			}).done(function(response) {
				table.ajax.reload();
				showSweetAlert("Listo", response.message, "success");
				$("#expenseModal").modal("hide");
			}).fail(function(response) {
				console.log(response);
				showSweetAlert();
			});
		}

		function addTransaction(formData) {
			$.ajax({
				method: "POST",
				url: "scripts/finance/expenses/add_transaction.php",
				dataType: 'json',
				data: formData
			}).done(function(response) {
				showSweetAlert("Listo", response.message, "success");
				$("#expenseModal").modal("hide");
				table.ajax.reload();
			}).fail(function(response) {
				console.log(response);
				showSweetAlert();
			});
		}

		function parseTotal(total) {
			let total_parsed = total * -1;
			total_parsed = total_parsed.toLocaleString('es-MX', {
				style: 'currency',
				currency: 'MXN',
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});

			return total_parsed;
		}
	</script>


	<script>
		$(document).ready(function() {
			// Obtén el user_id del localStorage
			const user_id = localStorage.getItem("user_id");
			// Asigna el valor de user_id al campo oculto en el formulario
			$("#user_id").val(user_id);

			// Revisa si el user_id es 20
			if (user_id == 20) {
				 // Habilita "Efectivo" y "Transferencia"
				 $("#payment_method_id").val("");  // Limpia cualquier selección previa

				// Deshabilita todas las demás opciones excepto "Transferencia"
				$("#payment_method_id option").each(function() {
					if ($(this).val() != "1" && $(this).val() != "3") {
						$(this).prop("disabled", true); // Deshabilita las otras opciones
					}
				});
			} else {
				// Si el user_id no es 20, asegura que todas las opciones estén habilitadas
				$("#payment_method_id option").prop("disabled", false);
			}
		});
	</script>

</body>

</html>