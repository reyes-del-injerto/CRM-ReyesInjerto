<?php
// Configuración y conexión a la base de datos
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

session_start();
require_once "scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

if (!isset($_SESSION['user_name'])) {
	header('Location: login.php');
	exit();
}

setlocale(LC_TIME, 'es_ES');

$currentDate = new DateTime();
$startDate = new DateTime($currentDate->format('Y-m-01'));
$endDate = new DateTime($currentDate->format('Y-m-t'));
$startDateString = $startDate->format('d/m/Y');
$endDateString = $endDate->format('d/m/Y');

// Obtener el valor del parámetro 'clinic' de la URL
$clinic = isset($_GET['clinic']) ? $conn->real_escape_string($_GET['clinic']) : null;

// Si la clínica es "CDMX", establecer filtro para "Santafe" y "Pedregal"
if ($clinic === 'CDMX') {
    $clinic_filter = "('Santafe', 'Pedregal')";
} else {
    $clinic_filter = "('{$clinic}')";
}

// Consultar gastos generales por categoría y subcategoría para el mes actual
$sql_categories = "
SELECT 
    c.id AS category_id,
    c.name AS category_name,
    IFNULL(SUM(t.amount), 0) AS total_expensed_amount,
    c.amount
FROM 
    ad_categories c
LEFT JOIN 
    ad_transactions t ON c.id = t.cat_id
    AND t.date BETWEEN '{$startDate->format('Y-m-d 00:00:00')}' AND '{$endDate->format('Y-m-d 23:59:59')}'
" . ($clinic ? " AND t.clinic IN {$clinic_filter}" : "") . "
WHERE 
    visible = 1
GROUP BY 
    c.id, c.name;
";

$result_categories = $conn->query($sql_categories);
$categories = [];

if ($result_categories->num_rows > 0) {
	while ($row = $result_categories->fetch_assoc()) {
		$categories[$row['category_id']] = [
			'name' => $row['category_name'],
			'id' => $row['category_id'],
			'amount' => $row['amount'],
			'total_expensed' => $row['total_expensed_amount'],
			'subcategories' => [],
			'subcategories_total' => 0
		];
	}
}

// Consultar gastos por subcategoría para el mes actual
$sql_subcategories = "
SELECT 
    s.id AS subcategory_id,
    s.category_id,
    s.name AS subcategory_name,
    s.description,
    IFNULL(SUM(t.amount), 0) AS total_expensed
FROM 
    ad_subcategories s
LEFT JOIN 
    ad_transactions t ON s.id = t.subcategory
    AND t.date BETWEEN '{$startDate->format('Y-m-d 00:00:00')}' AND '{$endDate->format('Y-m-d 23:59:59')}'
" . ($clinic ? " AND t.clinic IN {$clinic_filter}" : "") . "
GROUP BY 
    s.id, s.category_id, s.name, s.description;
";

$result_subcategories = $conn->query($sql_subcategories);

if ($result_subcategories->num_rows > 0) {
	while ($row = $result_subcategories->fetch_assoc()) {
		if (isset($categories[$row['category_id']])) {
			$categories[$row['category_id']]['subcategories'][] = [
				'name' => $row['subcategory_name'],
				'description' => $row['description'],
				'amount' => abs($row['total_expensed']) // Convertir el monto a positivo
			];
			$categories[$row['category_id']]['subcategories_total'] += $row['total_expensed'];
		}
	}
}

// Función de comparación para ordenar por el número de subcategorías
function compare_subcategories($a, $b)
{
	return count($b['subcategories']) - count($a['subcategories']);
}

// Ordenar las categorías usando la función de comparación personalizada
usort($categories, 'compare_subcategories');

// Calcular el monto total mensual
$total_amount_monthly = 0;
foreach ($categories as $category) {
	$total_amount_monthly += $category['amount'];
}
$total_amount_monthly_formatted = "$" . number_format($total_amount_monthly, 2, ".", ",");

// Calcular el monto total de los gastos en el rango de fechas
$startDateSQL = $startDate->format('Y-m-d 00:00:00');
$endDateSQL = $endDate->format('Y-m-d 23:59:59');

$expensed_amount_sql = "
SELECT 
    IFNULL(SUM(amount), 0) AS expensed_amount 
FROM 
    ad_transactions 
WHERE 
    amount < 0 AND date BETWEEN '{$startDateSQL}' AND '{$endDateSQL}'
" . ($clinic ? " AND clinic IN {$clinic_filter}" : "") . ";
";

$result_expensed_amount = $conn->query($expensed_amount_sql);
$total_expensed = 0;
if ($result_expensed_amount->num_rows > 0) {
	$row = $result_expensed_amount->fetch_assoc();
	$total_expensed_raw = $row['expensed_amount'] * -1;
	$total_expensed = "$" . number_format($total_expensed_raw, 2, ".", ",");
}

// Definir un valor predeterminado para $formatted_total_sum
$formatted_total_sum = "$0.00";

$sql_sum = "SELECT SUM(amount) AS total_amount FROM ad_categories";
$result = $conn->query($sql_sum);

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$total_sum = $row['total_amount'];
	$formatted_total_sum = number_format($total_sum, 1, '.', ',');
}

$message = "Configuración de presupuesto del <span>{$startDateString} a {$endDateString}</span>";

// Mostrar la suma total de los gastos (opcional, solo para depuración)
echo "La suma total de los gastos es: " . $formatted_total_sum;
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<!-- <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png"> -->
	<title>Config. de Presupuestos | RDI CDMX</title>
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
		.card {
			padding: 1rem;
			background-color: #fff;
			box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
			/* max-width: 320px; */
			border-radius: 20px;
			height: 330px;
		}

		.card_titles {
			display: flex;
			align-items: start;
			flex-direction: column;
			padding: 5px 3px;

		}

		.card_titles p {
			margin: 0rem;
			color: #000;
			text-align: center;
			font-size: 24px;
			font-style: normal;
			font-weight: 800;
			line-height: normal;

		}

		.card_titles h6 {
			margin: 0rem;
			color: #000;

			font-size: 13px;
			font-style: normal;
			font-weight: 500;
			line-height: normal;

		}

		.title {
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.title span {
			position: relative;
			padding: 0.5rem;
			background-color: #10B981;
			width: 1.5rem;
			height: 1.5rem;
			border-radius: 9999px;
		}

		.title span svg {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			color: #ffffff;
			height: 1rem;
		}

		.title-text {
			margin-left: 0.5rem;
			color: #374151;
			font-size: 18px;
		}

		.percent {
			color: #02972f;
			font-weight: 600;

		}

		.percent_budget {
			color: lightcoral;
			font-weight: 600;

		}

		.data {
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
		}

		.data p {


			color: #1F2937;

			line-height: 2.5rem;
			font-weight: 700;
			text-align: left;
		}

		.data .range {
			position: relative;
			background-color: #E5E7EB;
			width: 100%;
			height: 0.5rem;
			border-radius: 0.25rem;
		}

		.data .range {
			background-color: #e0e0e0;
			/* Color de fondo de la barra de progreso */
			width: 100%;
			/* Ancho completo */
			height: 5px;
			/* Altura de la barra de progreso */
			border-radius: 0.25rem;
			overflow: hidden;
			/* Oculta cualquier contenido que se desborde */
			margin-top: 40px;
		}

		.data .range .fill {
			background-color: #10B981;
			/* Color de la barra de progreso */
			height: 100%;
			border-radius: 0.25rem;
			transition: width 0.3s ease;
			/* Transición suave cuando cambia el ancho */
		}

		.p-title {
			margin-top: 1rem;
			margin-bottom: 1rem;
			color: #1F2937;
			font-size: 1.25rem;
			line-height: 2.5rem;
			font-weight: 700;
			text-align: left;
		}

		.subcategories {
			display: flex;
			flex-direction: column;
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
								<li class="breadcrumb-item"><a href="index.html">Inicio </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Finanzas</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Configuración de Presupuestos</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->

				<div class="good-morning-blk">
					<div class="row">
						<div class="col-md-8">
							<div class="morning-user">
								<h2><?= $message; ?></h2>
								<h4>Tienes presupuestado gastar: <span class="text-success"><?= $formatted_total_sum; ?></span></h4>
								<h4>Se han registrado gastos por: <span class="text-danger"><?= $total_expensed; ?></span></h4>
								<div class="btn-group">
								<button type="button" class="btn btn-primary dropdown-toggle" id="clinicButton" data-bs-toggle="dropdown" aria-expanded="false">
									Clinica
								</button>
								<ul class="dropdown-menu">
									<li><a class="dropdown-item item_clinic" href="#" data-clinic="Santafe">Santa Fe</a></li>
									<li><a class="dropdown-item item_clinic" href="#" data-clinic="Pedregal">Pedregal</a></li>
									<li><a class="dropdown-item item_clinic" href="#" data-clinic="CDMX">CDMX</a></li>
									<li><a class="dropdown-item item_clinic" href="#" data-clinic="Queretaro">Queretaro</a></li>
								</ul>
							</div>
							</div>
						</div>
						<div class="col-md-4 position-blk">
							<div class="morning-img">
								<img src="https://preclinic.dreamstechnologies.com/html/template/assets/img/morning-img-01.png" alt="">
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<?php
					if (!empty($categories)) {
						foreach ($categories as $category) {
							$categoryId = $category['id'];
							$asignado = $category['amount'];
							$gastado = $category['subcategories_total'];
					?>
							<div class="col-12 col-md-6 col-lg-6">
								<div class="card">
									<div class="title">
										<div style="display:flex; align-items:center;">
											<span>
												<svg width="20" fill="currentColor" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
													<path d="M1362 1185q0 153-99.5 263.5t-258.5 136.5v175q0 14-9 23t-23 9h-135q-13 0-22.5-9.5t-9.5-22.5v-175q-66-9-127.5-31t-101.5-44.5-74-48-46.5-37.5-17.5-18q-17-21-2-41l103-135q7-10 23-12 15-2 24 9l2 2q113 99 243 125 37 8 74 8 81 0 142.5-43t61.5-122q0-28-15-53t-33.5-42-58.5-37.5-66-32-80-32.5q-39-16-61.5-25t-61.5-26.5-62.5-31-56.5-35.5-53.5-42.5-43.5-49-35.5-58-21-66.5-8.5-78q0-138 98-242t255-134v-180q0-13 9.5-22.5t22.5-9.5h135q14 0 23 9t9 23v176q57 6 110.5 23t87 33.5 63.5 37.5 39 29 15 14q17 18 5 38l-81 146q-8 15-23 16-14 3-27-7-3-3-14.5-12t-39-26.5-58.5-32-74.5-26-85.5-11.5q-95 0-155 43t-60 111q0 26 8.5 48t29.5 41.5 39.5 33 56 31 60.5 27 70 27.5q53 20 81 31.5t76 35 75.5 42.5 62 50 53 63.5 31.5 76.5 13 94z">
													</path>
												</svg>
											</span>
											<div class="card_titles">
												<p class=""><?= htmlspecialchars($category['name']) ?></p>
												<h6 id="disponible_<?= $categoryId ?>">Disponible: $<span id="disponible_v_<?= $categoryId ?>"></span></h6>
											</div>
										</div>
										<div style="display: flex; flex-direction: column;">
											<p id="asignado_v_<?= $categoryId ?>" class="badge bg-success">
												Asignado: $<?= number_format($asignado, 2, ".", ",") ?>
											</p>
											<p id="gastado_v_<?= $categoryId ?>" class="badge bg-danger">
												Gastado: $<?= number_format($gastado, 2, ".", ",") ?>
											</p>
										</div>
									</div>
									<div class="data" data-asignado="<?= $asignado ?>" data-gastado="<?= $gastado ?>">
										<?php if (!empty($category['subcategories'])) { ?>
											<div class="subcategories">
												<p>Gastos por subcategorías:</p>
												<?php foreach ($category['subcategories'] as $subcategory) { ?>
													<div class="subcategory">
														<span data-bs-toggle="tooltip" title="<?= htmlspecialchars($subcategory['description']) ?>"><?= htmlspecialchars($subcategory['name']) ?></span>
														<span><?= "$" . number_format($subcategory['amount'], 2, ".", ",") ?></span>
													</div>
												<?php } ?>
											</div>
										<?php } ?>
										<div class="range">
											<div class="fill"></div>
										</div>
									</div>
								</div>
							</div>
					<?php
						}
					}
					?>
				</div>







			</div>
		</div>

		<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Agregar nueva categoría</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="formAddCategory" method="post">
							<!-- Campo de texto para el nombre -->
							<label for="nombre">Nombre de la categoría:</label>
							<input type="text" class="form-control" id="cat_name" name="cat_name" required>
							<br>

							<!-- Campo de email -->
							<label for="email">Monto:</label>
							<input type="number" step="0.01" min=0 class="form-control" id="cat_amount" name="cat_amount" required>
							<br>

					</div>
					<div class=" modal-footer">
						<button type="submit" class="btn btn-primary">Guardar</button>
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

	<!-- Numeral JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

	<!-- Sweet Alert JS -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script src="assets/js/app.js"></script>

	<script type="text/javascript">
		var clinica = new URLSearchParams(window.location.search).get('clinic');
				if (clinica) {
					// Actualizar el texto del botón con la clínica guardada
					$('#clinicButton').text(clinica);

					// Marcar como activo el item correspondiente en el dropdown
					$('.item_clinic').each(function() {
						if ($(this).data('clinic') === clinica) {
							$(this).addClass('active'); // Agregar clase active al elemento correspondiente
						} else {
							$(this).removeClass('active'); // Quitar clase active de otros elementos
						}
					});
				}


				$(document).on('click', '.item_clinic', function(e) {
					e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
					const clinica = $(this).data('clinic'); // Obtener el valor de la clínica

					// Actualizar el texto del botón con la clínica seleccionada
					$('#clinicButton').text(clinica);

					// Llama a la función para cargar pacientes con el valor de la clínica seleccionada
					//loadPatientsByClinic(clinica);
					window.location.href = `view_budget.php?clinic=${clinica}`;

					// Remover la clase 'active' de todos los elementos y añadirla al seleccionado
					$('.item_clinic').removeClass('active'); // Quitar la clase active de todos
					$(this).addClass('active'); // Agregar clase active al elemento seleccionado
				});





		$(document).ready(function() {
			$('[data-bs-toggle="tooltip"]').tooltip();
			$('[data-bs-toggle="tooltip"]').on('click', function() {
				$(this).tooltip('show');
			});

			// Opcional: Ocultar el tooltip cuando se haga clic fuera de él
			$(document).on('click', function(e) {
				if (!$(e.target).closest('[data-bs-toggle="tooltip"]').length) {
					$('[data-bs-toggle="tooltip"]').tooltip('hide');
				}
			});


			$("#formAddCategory").submit(function(e) {
				e.preventDefault();
				const method = "POST";
				const url = "scripts/finance/categories/add_category.php";

				const formData = $(this).serialize();

				Swal.fire({
					title: "Cargando...",
					allowOutsideClick: false,
					showConfirmButton: false,
				});
				$.ajax({
						data: formData,
						cache: false,
						dataType: "json",
						method: method,
						url: url,
					})
					.done(function(response) {
						if (response.success) {
							Swal.fire({
								title: "Listo!",
								text: response.message,
								icon: "success",
								showConfirmButton: false,
								timer: 2500, // Tiempo en milisegundos (1.5 segundos)
								timerProgressBar: true,
							}).then(function() {
								window.location.href = "view_budget.php";
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
						console.log(response.responseText);
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

			const cards = document.querySelectorAll('.card');

			cards.forEach(card => {
				// Obtener los elementos asignado y gastado
				const asignadoElement = card.querySelector('[id^="asignado_v_"]');
				const gastadoElement = card.querySelector('[id^="gastado_v_"]');

				// Extraer los identificadores únicos
				const id = asignadoElement.id.split('_')[2];

				// Obtener los valores de asignado y gastado desde el HTML
				const asignadoText = asignadoElement.innerText;
				const gastadoText = gastadoElement.innerText;

				// Extraer solo los números y convertirlos a float
				const asignado = Math.abs(parseFloat(asignadoText.replace(/[^0-9.-]+/g, '')));
				const gastado = Math.abs(parseFloat(gastadoText.replace(/[^0-9.-]+/g, '')));

				// Calcular el valor disponible
				const disponible = asignado - gastado;

				// Formatear el valor disponible a 2 decimales con coma como separador de miles
				const disponibleFormatted = disponible.toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});

				// Actualizar el contenido del elemento disponible
				document.getElementById(`disponible_${id}`).innerText = `Disponible: $${disponibleFormatted}`;
			});

			document.querySelectorAll('.data').forEach(dataElement => {
				// Obtener los valores asignado y gastado desde los atributos data-*
				const asignado = parseFloat(dataElement.getAttribute('data-asignado'));
				const gastado = parseFloat(dataElement.getAttribute('data-gastado'));

				// Convertir el monto gastado a positivo si es negativo
				const gastadoPositivo = Math.abs(gastado);

				// Calcular el porcentaje gastado
				const porcentajeGastado = Math.min(100, Math.max(0, (gastadoPositivo / asignado) * 100));

				// Obtener el ID del contenedor principal para construir el ID del elemento disponible
				const categoryId = dataElement.closest('.card').querySelector('.card_titles p').textContent.trim();

				// Establecer el contenido de los elementos
				const disponibleElement = dataElement.querySelector(`#disponible_v_${categoryId}`);
				if (disponibleElement) {
					disponibleElement.textContent = (asignado - gastadoPositivo).toFixed(2);
				} else {
					console.warn(`Elemento con ID 'disponible_v_${categoryId}' no encontrado.`);
				}

				// Establecer el ancho de la barra de progreso
				const fillElement = dataElement.querySelector('.range .fill');
				if (fillElement) {
					fillElement.style.width = porcentajeGastado + '%';
				} else {
					console.warn('Elemento con clase .range .fill no encontrado.');
				}
			});



		});
	</script>
</body>

</html>