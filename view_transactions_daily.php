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
	<title>Gastos diarios | ERP | Los Reyes del Injerto</title>
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

	<link rel="stylesheet" href="./assets/css/cortes_diarios.css">

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


				<div class=" header_invoices">
					<h4>Mostrando ingresos del dia: <span id="date_title"></span> - en <span id="clinic_show"></span> </h4>
					<div class="datepiccker_input">

						<p>Seleccione para cambiar: </p> <input class="input-group" type="text" id="datepicker">

						<div id="titles">
							<div id="period" class="dropdown">
								<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
									Clinica:
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
									<li> <a class="dropdown-item options_perd" href="#" data-clinic="Santa Fe" active current>Santa Fe</a></li>
									<li> <a class="dropdown-item options_perd" href="#" data-clinic="Pedregal">Pedregal</a></li>
									<li> <a class="dropdown-item options_perd" href="#" data-clinic="Queretaro">Queretaro</a></li>

								</ul>
							</div>


						</div>
					</div>


				</div>



				<div class="table-responsive">
					<table id="expensesTable" class="table table-striped">
						<thead>
							<tr>
								<th class="">Id</th>
								<th class="ps-0">Fecha</th>
								<th>Nombre</th>
								<th>Concepto</th>
								<th>Descripcion</th>
								<th>Importe</th>
								<th>Cantidad dls</th>
								<th>Precio dls </th>
								<th>Forma de Pago</th>
								<th>Imagen</th>
								<th>Sucursal</th>
								<th>options</th>

							</tr>
						</thead>
						<tbody class="text-dark" id="tBodyExpenseTable">
							<!-- Aquí se insertarán los datos mediante JavaScript -->
						</tbody>
					</table>
				</div>

				<h3 id="totales">Corte del dia: <span id="dia_corte"> </span> </h3>

				<div class="totales_div">
					<div class="table-responsive">
						<table id="totalTable" class="table table-striped">
							<thead>
								<tr>
									<th>Total Efectivo</th>
									<th>Total Tarjeta</th>
									<th>Total Depósito</th>
									<th>Total Transferencia</th>
									<th>Cierre</th>
								</tr>
							</thead>
							<tbody class="text-dark" id="totalBodyExpenseTable">
								<!-- Aquí se insertarán los datos mediante JavaScript -->
							</tbody>
						</table>
					</div>
				</div>



				<div class="autorce">

					<div id="sign_step"></div>
					<div class="row" id="divPdfAssessment">
						<div class="col-12">
							<div class="card">
								<div class="card-body">
									<iframe id="pdfViewer" src="" width="100%" height="1000px" style="border: none;"></iframe>
								</div>
								<a target="_blank" href="" id="pdfInvoiceDownloadAssesment" download>Descargar</a>



							</div>
						</div>
					</div>
					<div class="option_delete"></div>
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
	<!-- 	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> -->
	<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {

			let resultadoo = "11" + 1;
			console.log(resultadoo)

			var today = new Date();
			var formattedDate = today.getFullYear() + '-' +
				String(today.getMonth() + 1).padStart(2, '0') + '-' +
				String(today.getDate()).padStart(2, '0');

			// Inicializa con la fecha de hoy
			let parseDateView = formattedDate
			const title = document.getElementById("date_title");
			title.innerHTML = formattedDate;
			const dia_corte = document.getElementById("dia_corte");
			dia_corte.innerHTML = formattedDate;

			var clinic = "Santa Fe";
			const clinicSelected = document.getElementById("clinic_show");
			clinicSelected.innerText = clinic;

			$('.options_perd').on('click', function(event) {
				event.preventDefault(); // Evita que el enlace recargue la página
				clinic = $(this).data('clinic'); // Obtén el valor del atributo 'data-clinic'
				console.log("click en", clinic);

				// Actualiza el texto del elemento clinic_show
				clinicSelected.innerText = clinic;

				table.ajax.reload();
				fetchTotals(formattedDate, clinic);
				console.log(formattedDate);

				const dateParts = formattedDate.split('-'); // Divide la fecha en partes [YYYY, MM, DD]
				const dateForFilename = `${dateParts[2]}${dateParts[1]}${dateParts[0].slice(-2)}`; // 'DDMMYY'

				console.log("Fecha formateada para el click", dateForFilename);
				loadAssessmentPDF(clinic, dateForFilename);
				showSign(formattedDate, clinic);
			});

			// Función para agregar la delegación de eventos
			function addDeleteEventListeners() {
				console.log("función cargada");

				// Delegación de eventos: asigna el evento al cuerpo de la tabla (o al contenedor adecuado)
				document.querySelector('#expensesTable tbody').addEventListener('click', function(event) {
					// Verifica si el elemento clicado tiene la clase 'delete_record'
					if (event.target.closest('.delete_record')) {
						let button = event.target.closest('.delete_record');
						let recordId = button.getAttribute('data-id');
						let type_payment = button.getAttribute('data-type');
						console.log("botón presionado, gasto:", recordId, "-", type_payment);

						if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
							console.log("a borrar");
							// Realiza la petición POST para eliminar el registro
							fetch('scripts/finance/expenses/delete_expense.php', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json'
									},
									body: JSON.stringify({
										id: recordId,
										type_payment: type_payment
									}) // Envía el ID en el cuerpo de la solicitud
								})
								.then(response => response.json())
								.then(data => {
									if (data.success) {
										console.log("Registro eliminado exitosamente");
										// Aquí puedes actualizar la tabla o volver a cargar los datos
										window.location.reload();
									} else {
										console.error("Error al eliminar el registro:", data.message);
										alert("No se pudo eliminar el registro. Inténtalo de nuevo.");
									}
								})
								.catch(error => {
									console.error("Error en la petición:", error);
									alert("Ocurrió un error. Inténtalo de nuevo.");
								});
						}
					}
				});
			}





			// Configuración global predeterminada para todos los datepickers
			$.datepicker.setDefaults({
				showOn: "both",
				buttonImageOnly: true,
				buttonImage: "calendar.gif",
				buttonText: "Calendar",
				dateFormat: 'yy-mm-dd' // Establece el formato de fecha en YYYY-MM-DD
			});

			$(".selector").datepicker({
				autoSize: true,
				dateFormat: 'yy-mm-dd' // Asegúrate de que el formato sea consistente
			});

			// Inicialización específica para el datepicker con posición personalizada
			$("#datepicker").datepicker({
				dateFormat: 'yy-mm-dd', // Formato de la fecha
				beforeShow: function(input, inst) {
					// Calcula la posición del input
					var offset = $(input).offset();
					var height = $(input).outerHeight();

					// Mueve el calendario a la izquierda y más arriba del input
					setTimeout(function() {
						inst.dpDiv.css({
							top: offset.top - height - 40, // Ajusta para mover más arriba (-height y -10px de margen)
							left: offset.left - inst.dpDiv.outerWidth() - 10 // Mueve a la izquierda
						});
					}, 1);
				},
				onSelect: function(dateText) {
					// Actualiza la variable formattedDate con la fecha seleccionada
					formattedDate = dateText;
					$("#date_title").text(dateText);
					$("#dia_corte").text(dateText);




					table.ajax.reload(); // Recarga la tabla con la nueva fecha
					showSign(dateText, clinic);
					console.log("reloaaad");
					fetchTotals(dateText, clinic);



					const [year, month, day] = dateText.split('-');
					const yearShort = year.slice(-2);
					const fechaFormato = `${day}${month}${yearShort}`;
					loadAssessmentPDF(clinic, fechaFormato);
				}
			});

			// Inicializar DataTables
			var table = $('#expensesTable').DataTable({

				ajax: {
					url: 'scripts/finance/expenses/load_all_daily.php',
					type: 'POST',
					data: function(d) {
						// Obtener la fecha del título actualizado
						d.fecha = $("#date_title").text()
						d.clinic = clinic;
					},
					dataSrc: 'data'
				},
				columns: [{
						data: 'id'
					},
					{
						data: 'fecha'
					},
					{
						data: 'nombre'
					},
					{
						data: 'tipo'
					},
					{
						data: 'public_notes'
					},
					{
						data: 'importe',
						render: function(data) {
							return formatCurrency(data);
						}
					},
					{
						data: 'amount_conversion',
						render: function(data) {
							return formatCurrency(data);
						}
					},
					{
						data: 'conversion',
						render: function(data) {
							return formatCurrency(data);
						}
					},
					{
						data: 'metodo_de_pago'
					},
					{
						data: 'imagen',
						render: function(data) {
							// Verifica si el dato es '#'
							if (data == '#') {
								// Retorna el mensaje si el archivo no está disponible
								return 'Archivo no disponible, visite al px';
							} else {
								// Retorna el enlace con la imagen si el archivo está disponible
								return `<a href="${data}" target="_blank"><img src="./assets/img/pdf-file.png" alt="Imagen PDF" style="max-width: 50px;"></a>`;
							}
						}
					},

					{
						data: 'sucursal'
					},
					{
						data: 'options'
					}
				],
				order: [], // Desactiva el ordenamiento por defecto
				paging: false, // Desactiva la paginación
				// pageLength: -1, // Alternativa: Mostrar todos los registros en una página si deseas mantener la paginación
				scrollY: '800px', // Opcional: Hacer la tabla scrollable si hay muchos datos
				scrollCollapse: true // Permitir que la tabla colapse si hay menos datos de los que caben en la pantalla

			});
			addDeleteEventListeners();
			// Mostrar la firma inicial para la fecha actual
			showSign(formattedDate);
			fetchTotals(formattedDate, clinic);




			// Obtener la fecha actual
			let hoy = new Date();

			// Extraer el día, mes y año
			let dia = hoy.getDate(); // Día del mes (1-31)
			let mes = hoy.getMonth() + 1; // Mes (0-11, por eso se le suma 1)
			let anio = hoy.getFullYear().toString().slice(-2); // Obtener los últimos dos dígitos del año

			// Formatear el día y mes para que tengan dos dígitos
			dia = dia < 10 ? '0' + dia : dia;
			mes = mes < 10 ? '0' + mes : mes;

			// Concatenar para obtener el formato ddmmaaa
			let fechaFormato = `${dia}${mes}${anio}`;


			loadAssessmentPDF(clinic, fechaFormato)

			function loadAssessmentPDF(clinica, timestamp) {
				const url = `./files/cdmx/corte-caja/corte_caja_${clinica}_${timestamp}.pdf`;
				console.log("cargando pdf:", url)
				fetch(url, {
						method: 'HEAD',
					})
					.then((response) => {
						if (
							response.status === 200 &&
							response.headers.get('Content-Type') === 'application/pdf'
						) {
							$('#pdfViewer').attr('src', url)
							$('#pdfInvoiceDownloadAssesment').attr('href', url)
							$('#divPdfAssessment').fadeIn('slow')
						} else {
							$('#pdfViewer').attr('src', ''); // Elimina el PDF actual
							$('#pdfInvoiceDownloadAssesment').attr('href', '#'); // Opcionalmente, limpiar el enlace de descarga
							$('#divPdfAssessment').fadeOut('slow'); // Ocultar el div que contiene el PDF

						}
					})
					.catch((error) => {
						console.error('Error al verificar la URL:', error)
					})
			}

			function showSign(selectedDate, clinica) {
				console.log("buscando firma de ", selectedDate, " y ", clinic);
				$.ajax({
					url: './scripts/finance/expenses/search_sign_by_day.php', // Cambia esto a la ruta del script PHP que manejará la petición
					type: 'POST',
					dataType: 'json',
					data: {
						dia: selectedDate,
						clinic: clinica
					},
					success: function(response) {
						if (response.success) {
							// Manejar el éxito
							// Limpiar el div antes de agregar la imagen
							$('#sign_step').empty();

							// Crear el párrafo "Firma:"
							var pFirma = $('<h4>').text('Firma:').css('margin-top', '1rem');

							// Crear una etiqueta de imagen y agregarla al div
							var img = $('<img>')
								.attr('src', 'data:image/png;base64,' + response.firma)
								.attr('alt', 'Texto alternativo aquí') // Agrega el texto alternativo
								.addClass('img_firma'); // Agrega la clase _img_firma



							// Añadir el párrafo y la imagen al div
							$('#sign_step').append(pFirma).append(img);

							// Ahora, si la condición es verdadera, agregamos el botón "Eliminar" a option_delete
							var deleteButton = $('<button>')
								.text('Eliminar') // Texto del botón
								.attr('data-dia', selectedDate) // Atributo dia con el valor seleccionado
								.attr('data-clinic', clinica) // Atributo clinic con el valor seleccionado
								.addClass(' btn btn-danger') // Añade una clase al botón para estilizarlo
								.on('click', function() {
									// Confirmación de eliminación
									var confirmDelete = confirm('¿Estás seguro de que quieres eliminar la firma para el día: ' + selectedDate + ' y clínica: ' + clinica + '?');

									if (confirmDelete) {
										// Realizar la petición AJAX para eliminar la firma
										$.ajax({
											url: './scripts/finance/expenses/delete_sign.php', // Ruta al script PHP para eliminar la firma
											type: 'POST',
											dataType: 'json',
											data: {
												dia: selectedDate,
												clinic: clinica
											},
											success: function(response) {
												if (response.success) {
													alert('Firma eliminada correctamente.');
													window.location.reload();
													// Realizar cualquier acción adicional si es necesario
												} else {
													alert('No se pudo eliminar la firma.');
												}
											},
											error: function() {
												alert('Hubo un error al intentar eliminar la firma.');
											}
										});
									} else {
										console.log('La eliminación fue cancelada.');
									}
								});


							// Añadir el botón al div con clase 'option_delete'
							$('.option_delete').empty().append(deleteButton); // Limpiar el div y añadir el botón


						} else {
							var user_id = localStorage.getItem('user_id')
							var firma_canva = `
							<div id="lol"><button class="btn btn-primary" id="btnCrearfirma">Generar corte</button>  
							</div>
							
							<div id="auth">
							<canvas id="canvas" width="400" height="200" style="border: 1px solid black;"></canvas>
<br>
<div id="accions_canva">
<button class="btn btn-light" id="btnLimpiar">Limpiar</button>

							</div>
							`;







							$('#sign_step').empty(); // Limpia el contenido previo
							$('#sign_step').append(firma_canva); // Inyecta el contenido solo si la condición es verdadera

							const userId = localStorage.getItem('user_id'); // Obtener user_id del localStorage
							const authDiv = document.getElementById('auth'); // Referencia al div
							const btnCrearfirma = document.getElementById('btnCrearfirma'); // Referencia al div

							// Mostrar o esconder el div según el user_id
							if (userId === '1') {
								authDiv.style.display = 'block'; // Mostrar el div
								console.log("es admin")
								btnCrearfirma.innerText = "Firmar"; // Asignar el texto directamente

							} else {
								console.log("no es admin")
								authDiv.style.display = 'none'; // Ocultar el div
							}

							const $botonCrearPdf = document.querySelector("#btnCrearfirma");
							const $canvas = document.querySelector("#canvas"),
								$btnLimpiar = document.querySelector("#btnLimpiar");
							const contexto = $canvas.getContext("2d");
							const COLOR_PINCEL = "black";
							const COLOR_FONDO = "white";
							const GROSOR = 2;

							let xAnterior = 0,
								yAnterior = 0,
								xActual = 0,
								yActual = 0;
							let haComenzadoDibujo = false;

							const obtenerXReal = (clientX) => clientX - $canvas.getBoundingClientRect().left;
							const obtenerYReal = (clientY) => clientY - $canvas.getBoundingClientRect().top;

							const limpiarCanvas = () => {
								contexto.fillStyle = COLOR_FONDO;
								contexto.fillRect(0, 0, $canvas.width, $canvas.height);
							};

							limpiarCanvas();
							$btnLimpiar.onclick = limpiarCanvas;

							const onClicOToqueIniciado = evento => {
								xAnterior = xActual;
								yAnterior = yActual;
								xActual = obtenerXReal(evento.clientX);
								yActual = obtenerYReal(evento.clientY);
								contexto.beginPath();
								contexto.fillStyle = COLOR_PINCEL;
								contexto.fillRect(xActual, yActual, GROSOR, GROSOR);
								contexto.closePath();
								haComenzadoDibujo = true;
							};

							const onMouseODedoMovido = evento => {
								evento.preventDefault();
								if (!haComenzadoDibujo) return;

								let target = evento;
								if (evento.type.includes("touch")) {
									target = evento.touches[0];
								}
								xAnterior = xActual;
								yAnterior = yActual;
								xActual = obtenerXReal(target.clientX);
								yActual = obtenerYReal(target.clientY);
								contexto.beginPath();
								contexto.moveTo(xAnterior, yAnterior);
								contexto.lineTo(xActual, yActual);
								contexto.strokeStyle = COLOR_PINCEL;
								contexto.lineWidth = GROSOR;
								contexto.stroke();
								contexto.closePath();
							};

							const onMouseODedoLevantado = () => {
								haComenzadoDibujo = false;
							};

							["mousedown", "touchstart"].forEach(nombreDeEvento => {
								$canvas.addEventListener(nombreDeEvento, onClicOToqueIniciado);
							});

							["mousemove", "touchmove"].forEach(nombreDeEvento => {
								$canvas.addEventListener(nombreDeEvento, onMouseODedoMovido);
							});

							["mouseup", "touchend"].forEach(nombreDeEvento => {
								$canvas.addEventListener(nombreDeEvento, onMouseODedoLevantado);
							});

							$botonCrearPdf.addEventListener("click", async () => {
								// Verificar que todos los campos estén llenos

								// Convertir el contenido del canvas a base64
								const imagenBase64 = $canvas.toDataURL("image/png");

								// Comprobar si el canvas no está vacío
								const canvasVacio = imagenBase64 === document.createElement('canvas').toDataURL("image/png");
								if (canvasVacio) {
									showSweetAlert(
										"Advertencia!",
										response.message,
										"warning",
										1500,
										true,
										false
									);
									return;
								}

								// Crear un objeto FormData y agregar los campos
								const formData = new FormData();

								formData.append("firma", imagenBase64);
								formData.append("dia", selectedDate);
								formData.append("clinic", clinic);


								// Serializar los datos para imprimir en consola

								// Mostrar un mensaje de confirmación antes de enviar
								const confirmarEnvio = confirm("¿Estás seguro de que deseas enviar la información?");
								if (!confirmarEnvio) {
									return;
								}

								// Enviar los datos al servidor
								try {
									$botonCrearPdf.disabled = true;
									$botonCrearPdf.textContent = "Enviando...";

									const response = await fetch("./scripts/finance/expenses/add_sign_by_day.php", {
										method: "POST",
										body: formData
									});

									const result = await response.json();

									if (result.success) {
										showSweetAlert(
											"Éxito!",
											"Archivo generado correctamente",
											"success",
											1500,
											true,
											false
										);
										limpiarCanvas();
									} else {
										showSweetAlert(
											"Error!",
											result.message,
											"error",
											1500,
											true,
											false
										);
									}
								} catch (error) {
									console.error("Error al enviar la firma:", error);
									showSweetAlert(
										"Error!",
										"Ocurrió un error al enviar la firma.",
										"error",
										1500,
										true,
										false
									);
								} finally {
									$botonCrearPdf.disabled = false;
									$botonCrearPdf.textContent = "Guardaar firma";
								}


								//genera pdf de corte:

								// Recopilar los datos de la tabla totalTable
								const totalTableBody = document.getElementById('totalBodyExpenseTable');
								const rows = totalTableBody.querySelectorAll('tr');
								let tableData = [];

								rows.forEach(row => {
									let rowData = [];
									row.querySelectorAll('td').forEach(cell => {
										rowData.push(cell.innerText.trim());
									});
									tableData.push(rowData);
								});

								// Convertir los datos de la tabla en un formato JSON
								const jsonData = JSON.stringify(tableData);
								const userId = localStorage.getItem('user_id');

								// Crear un objeto FormData y agregar los datos de la tabla
								const formData2 = new FormData();
								formData2.append("tableData", jsonData);
								formData2.append("clinic", clinic);
								formData2.append("fecha", $("#date_title").text());
								formData2.append("user_id", userId);
								formData2.append("firma", imagenBase64);


								console.log("datos a enviar2:", formData2)

								try {
									// Enviar los datos al servidor para generar el PDF
									const response = await fetch('./scripts/finance/expenses/generate_cash_closing_daily.php', {
										method: 'POST',
										body: formData2
									});

									console.log("Cargando corte del dia:", response);

									if (response.ok) {
										const jsonResponse = await response.json();

										if (jsonResponse.success) {
											const relativePath = jsonResponse.path; // Obtiene la ruta relativa del archivo
											const baseURL = './'; // Añade el directorio base a la ruta

											// Construye la URL completa
											const fileURL = baseURL + relativePath;
											console.log(fileURL)
											// Abre el PDF en una nueva pestaña
											window.open(fileURL, '_blank');
											window.location.reload();
										} else {
											console.error('Error al generar el PDF:', jsonResponse.message);
											showSweetAlert(
												"Error!",
												"No se pudo generar el PDF.",
												"error",
												1500,
												true,
												false
											);
										}
									} else {
										console.error('Error al generar el PDF:', response.statusText);
										showSweetAlert(
											"Error!",
											"No se pudo generar el PDF.",
											"error",
											1500,
											true,
											false
										);
									}
								} catch (error) {
									console.error('Error en la solicitud:', error);
									showSweetAlert(
										"Error!",
										"Ocurrió un error al intentar generar el PDF.",
										"error",
										1500,
										true,
										false
									);
								}



							});
						}
					},
					error: function() {
						showSweetAlert(
							"Error!",
							"Ocurrió un error al buscar la firma.",
							"error",
							1500,
							true,
							false
						);
					}
				});
			}

			function fetchTotals(day, clinic) {
				console.log("fetch totals", day, " - ", clinic)
				const url = 'scripts/finance/expenses/load_total_daily.php'; // Ruta al archivo PHP


				fetch(url, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded'
						},
						body: new URLSearchParams({
							'fecha': day, // Cambia por la fecha deseada o por una variable si es necesario
							'clinic': clinic
						})
					})
					.then(response => response.json())
					.then(data => {
						console.log('Datos recibidos:', data); // Agrega esto para depurar
						if (data.success === 'true') {
							updateTotalsTable(data.totals);
						} else {
							console.error('Error al obtener los totales:', data);
							updateTotalsTable([]); // Pasar un array vacío para limpiar la tabla
						}
					})
					.catch(error => {
						console.error('Error en la solicitud AJAX:', error);
					});
			}

			function updateTotalsTable(totals) {
				console.log(totals)
				const totalTableBody = document.getElementById('totalBodyExpenseTable');
				const thead = document.querySelector('#totalTable thead');

				totalTableBody.innerHTML = ''; // Limpia el contenido previo
				thead.innerHTML = ''; // Limpia los encabezados previos

				// Obtener todos los métodos únicos de los datos recibidos
				const methods = ['Efectivo', 'Dolarés', 'Tarjeta', 'Depósito', 'Transferencia', 'Otro', 'Enlace digital', 'TDC', 'TDD'];

				// Crear encabezados de tabla dinámicamente
				const headerRow = document.createElement('tr');
				methods.forEach(method => {
					const th = document.createElement('th');
					th.textContent = `Total ${method}`;
					headerRow.appendChild(th);
				});

				// Agregar la columna de cierre
				const cierreTh = document.createElement('th');
				cierreTh.textContent = 'Cierre';
				headerRow.appendChild(cierreTh);

				// Añadir la fila de encabezado al thead
				thead.appendChild(headerRow);

				// Crear una fila para los totales
				const row = document.createElement('tr');
				let totalCierre = 0;

				methods.forEach(method => {
					const total = getTotalByMethod(totals, method);
					const cell = document.createElement('td');
					cell.textContent = formatCurrency(total);

					row.appendChild(cell);

					// Acumulamos el total para la columna 'Cierre'
					totalCierre += parseFloat(total) || 0;
				});

				// Agregar la columna de cierre con el total acumulado
				const cierreCell = document.createElement('td');
				cierreCell.textContent = formatCurrency(totalCierre);
				row.appendChild(cierreCell);

				// Añadir la fila al cuerpo de la tabla
				totalTableBody.appendChild(row);
			}

			function getTotalByMethod(totals, method) {
				const total = totals.find(item => item.metodo_de_pago === method);
				return total ? total.total_importe : '0';
			}

			function formatCurrency(value) {
				// Formato de moneda mexicana
				return parseFloat(value).toLocaleString('es-MX', {
					style: 'currency',
					currency: 'MXN'
				});
			}





		});
	</script>



</body>

</html>