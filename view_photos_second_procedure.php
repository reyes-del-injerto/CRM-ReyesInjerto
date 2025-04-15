<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";


if (isset($_GET['type']) && isset($_GET['id'])) {
	$procedure_id = $_GET['id'];
	$type = $_GET['type'];
	$title_type = "";

	if ($type === 'touchup') {
		$title_type = 'Segundo procedimiento';
	} else {
		$title_type = 'Micropigmentacion';
	}


	$sql_row = "SELECT CONCAT(sla.first_name, ' ', sla.last_name) AS name, sla.procedure_date, sla.procedure_type, ep.num_med_record,ep.clinic, ep.touchup, ep.room, ep.specialist, ep.notes FROM enf_procedures ep INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id WHERE /* sla.status = 1 AND */ ep.lead_id = ?  AND sla.status != 0";

	$sql = $conn->prepare($sql_row);
	$sql->bind_param("i", $procedure_id);

	if (!$sql->execute()) {
		throw new Exception("Error al obtener la info. del procedimiento: " . $sql->error);
	}
	$result = $sql->get_result();

	if ($result->num_rows != 1) {
		throw new Exception("Error de duplicidad. Contacta al administrador.");
	}

	$px_info = $result->fetch_object();

	$procedure_date = $px_info->procedure_date;
	$procedure_room = $px_info->room;
	$clinic = $px_info->clinic;
	echo "$procedure_room";
	$card = '
			<div class="text-center col-md-12 col-xs-12 order-md-1 order-last">
				<div class="card text-white bg-secondary ">
					<div class="card-body text-center">
						<h2 style="color:#e0ac44;" id="px_name">' . $px_info->name . ' </h2>
						 <span style"font-size:20px;" class="badge bg-dark"> '  . $title_type . ' </span>
						<p><span style="font-size:20px;" class="badge bg-secondar">#' . $px_info->num_med_record . '</span></p>
						<p><span style="font-size:20px;" class="badge bg-primary">' . $px_info->procedure_type . '</span>
							 
						</p>
					
						<p><a href="comparePhotos.php?type=' . $_GET['type'] . '&id=' . $_GET['id'] . '">Comparar fotografías</a></p>	
						<p style="font-size:20px;">
							<strong>Sala:</strong>' . $px_info->room . '<br />
							<strong>Especialista: </strong>' . $px_info->specialist . '<br />
							<strong>Fecha Procedimiento: </strong>' . $px_info->procedure_date . '</p>
					</div>
					<input type="hidden" id="num_med_record" name="num_med_record" value="' . $px_info->num_med_record . '">
					<input type="hidden" id="clinic" name="clinic" value="'.$clinic.'">
				</div>
				<input type="hidden" value="' . $_GET['id'] . '" id="px_sales_id">
				<input type="hidden" id="num_med_record" value="' . $px_info->num_med_record . '">
				 <span style"font-size:20px;" class="badge bg-dark">'.$clinic.'</span>
					
				<input type="hidden" id="room" value="' . $px_info->room . '">
				<input type="hidden" id="type" value="' . $_GET['type'] . '">
			</div>';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Fotos del Paciente | ERP | Los Reyes del Injerto</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

	<!-- FileInput-->
	<link rel="stylesheet" type="text/css" href="assets/plugins/fileinput/fileinput.css" />

	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<!-- Swiper Slider -->
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.css'>
	<!-- Fancy Cards -->
	<link rel="stylesheet" href="assets/plugins/fancycards/style.css">



	<link rel="stylesheet" href="./assets/css/uiverse.css">
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
								<li class="breadcrumb-item"><a class="nav-link active" href="index.php">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item "><a class="nav-link active" href="view_procedures.php">Procedimientos</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active"><a class="nav-link active" href="#">Fotografías de <?php echo $title_type ?>  </a></li>
						</div>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="mx-auto col-12 col-md-4">
										<div class="card text-white bg-secondary ">
											<?= $card; ?>
											<input type="hidden" id="clinic" name="clinic" value="1">
										</div>
									</div>
									<div class="col-12 col-md-8">
										<div class="card">
											<div class=" slider">
												<div class="swiper people__slide">
													<div class="swiper-wrapper">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-body">
												<h4 style='color:#e0ac44;' id="fileinput-title"></h4>

												<div class="inputfile-container">
													<input type="file" id="file" name="file[]" multiple>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="noteextra">

								</div>


								<div id="extraaa">

									<div class="extras_medical">
										<!-- <button type="button" class="button_ui">
											<span class="button_ui__text">Crear nota</span>
											<span class="button_ui__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg">
													<line y2="19" y1="5" x2="12" x1="12"></line>
													<line y2="12" y1="12" x2="19" x1="5"></line>
												</svg></span>
										</button> -->


									</div>


								</div>

								<div id="sign_step"></div>



							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
	<!-- Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel33">
						Editar Procedimiento.
					</h4>
					<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
						<i data-feather="x"></i>
					</button>
				</div>
				<div class="modal-body">
					<form id="addNoteForm" method="post">
						<div class="form-group">
							<label for="num_med_record">Número de Expediente:</label>
							<input type="text" class="form-control" id="num_med_record_form" name="num_med_record" required readonly>
							<input type="hidden" class="form-control" id="phase" name="phase" required>
							<input type="hidden" class="form-control" id="author" name="author" required>
						</div>

						<div class="form-group">
							<label for="note">Nota:</label>
							<textarea class="form-control" id="note" name="note" rows="4" required></textarea>
						</div>
						<div class="form-group">
							<label for="date">Fecha:</label>
							<input type="date" class="form-control" id="date" name="date" required>
						</div>


						<button type="submit" class="btn btn-primary">Agregar Nota</button>
					</form>


				</div>
			</div>
		</div>


		<div class=" sidebar-overlay" data-reff=""></div>

		<!-- jQuery -->
		<script src="assets/js/jquery.min.js"></script>







		<script src="assets/js/buffer.js" type="text/javascript"></script>
		<script src="assets/js/filetype.js" type="text/javascript"></script>

		<!-- Bootstrap Core JS -->
		<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

		<script src="assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
		<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.3/js/locales/es.js" type="text/javascript"></script>

		<!-- Feather Js -->
		<script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

		<!-- Slimscroll -->
		<script src="assets/js/jquery.slimscroll.js"></script>

		<script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.min.js'></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>

		<script>
			var folders, folders_name, swiper;
			const px_sales_id = $("#px_sales_id").val();
			const num_med_record = $("#num_med_record").val();
			const room = $("#room").val();
			const type = $("#type").val();




			$(document).ready(function() {

				Swal.fire({
					title: "Cargando...",
					allowOutsideClick: false,
					showConfirmButton: false,
				});


				$('#firma').on('submit', function(event) {
					event.preventDefault(); // Prevenir el comportamiento por defecto del formulario
					console.log("enviando")
					// Obtener los datos del formulario
					var formData = $(this).serialize();
					console.log("form", formData)



				});



				$('#addNoteForm').on('submit', function(event) {
					event.preventDefault(); // Prevenir el comportamiento por defecto del formulario
					console.log("enviando");

					// Obtener los datos del formulario
					var formData = $(this).serialize();
					console.log("formdata antes de agregar:", formData);

					// Obtener el valor de "type" desde los parámetros de la URL o de otra fuente
					var urlParams = new URLSearchParams(window.location.search);
					var procedure_type = urlParams.get('type'); // Obteniendo el valor de 'type' de la URL
					console.log("procedure_type:", procedure_type);

					// Agregar el valor de procedure_type al formData
					formData += '&procedure_type=' + procedure_type;
					formData += '&clinic=' + clinic;
					console.log("formdata después de agregar:", formData);

					var step = $('#addNoteForm').find('input[name="phase"]').val();
					console.log("step:", step);
					console.log("clinica para nota", clinic)

					// Enviar los datos a través de AJAX
					$.ajax({
						type: 'POST',
						url: './scripts/procedures/add_notes.php', // URL del script PHP que procesa la firma
						data: formData,
						success: function(response) {
							$("#editModal").modal("hide");
							console.log("object");

							showNotes(step);

							showSweetAlert(
								"Listo!",
								response.message,
								"success",
								1500,
								true,
								false
							);

						},
						error: function() {

						}
					});
				});





				let color = "dark";

				$(".inputfile-container").css('display', 'none');
				folders = ["pre", "diseno", "post", "24horas", "10dias", "1mes", "3meses", "6meses", "9meses", "12meses", "15meses", "18meses", "21meses", "post_alta"];
				folders_name = ["Pre Procedimiento", "Diseño", "Post Procedimiento", "24 Horas", "10 Días", "1 Mes", "3 Meses", "6 Meses", "9 Meses", "12 Meses", "15meses", "18 Meses", "21 Meses", "Post Alta"];
				var i = 0;
				let container = $(".swiper-wrapper");
				var num_med_record = $("#num_med_record").val();
				var clinic = $("#clinic").val();

				folders.forEach(function(folder) {
					let btn_disabled = (i > 0) ? 'style="display:none;"' : '';

					let swiperSlide = `
						<div class="swiper-slide">
							<div class="people__card">
								<div class="people__image">
									<img src="./assets/img/leon-footer.webp" style="width:50%;height:auto;">
								</div>
								<div class="people__info">
									<ul class="people__social">
									</ul>
									<h3 class="people__name">${folders_name[i]}</h3>
								</div>
								<div class="people__btn" ${btn_disabled}>
									<a class="view_imgs" href="#" data-clinic="${clinic}" data-step="${folder}" data-nummedrecord="${num_med_record}">Ver fotos</a>
								</div>
							</div>
						</div>`;

					$(".swiper-wrapper").append(swiperSlide);
					i++;
				});

				$('#file').fileinput({});

				swiper = new Swiper(".swiper", {
					loop: false,
					slidesPerView: "auto",
					centeredSlides: true,
					observeParents: true,
					observer: true,
				});

				swiper.slides.forEach(function(slide, index) {
					slide.addEventListener('mouseover', function() {
						$(this).css('cursor', 'pointer');
					});
					slide.addEventListener('click', function() {
						swiper.slideTo(index);
					});
				});

				swiper.on('slideChangeTransitionEnd', function() {
					//$(".inputfile-container").fadeOut('slow');
					$(".people__btn").css('display', 'none');

					$(swiper.el).find('.swiper-slide-active .people__btn').css('display', 'block');
				});

				$(document).on('click', '.button_ui', function(e) {
					$("#editModal").modal("show");
					console.log(num_med_record)
					$("#num_med_record_form").val(num_med_record);
					//$("#note").val("");
					const user_id = localStorage.getItem("user_id")
					console.log(user_id)
					$("#author").val(user_id);


					let step = $(this).data('step');




					$("#phase").val(step);


					// Función para formatear la fecha en YYYY-MM-DD
					function formatDate(date) {
						const year = date.getFullYear();
						const month = String(date.getMonth() + 1).padStart(2, '0'); // Meses empiezan en 0
						const day = String(date.getDate()).padStart(2, '0');
						return `${year}-${month}-${day}`;
					}

					// Establece la fecha actual como valor por defecto

					const today = new Date();
					document.getElementById('date').value = formatDate(today);

				});

				function showNotes(step) {
					var urlParams = new URLSearchParams(window.location.search);
					var procedure_type = urlParams.get('type'); // Obteniendo el valor de 'type' de la URL
					console.log("mostrar notas", clinic)
					$.ajax({
						url: './scripts/procedures/medical_extras.php',
						type: 'POST',
						data: {
							num_med_record: num_med_record,
							step: step,
							procedure_type: procedure_type,
							clinic: clinic,
						},
						dataType: 'json',
						success: function(response) {
							// Maneja la respuesta del servidor
							if (response.status === 'success') {
								console.log(response)
								// Vaciamos el contenido de la div
								$("#note").val("");
								$('#extraaa').empty();

								if (response.status === 'success') {
									response.data.forEach(function(item) {

										var fullNote = item.note;
										var shortNote = fullNote.length > 30 ? fullNote.substring(0, 30) + '...' : fullNote;






										// Construir el HTML con la información recibida
										var cardHtml = `
			<div class="extras_medical">
					
				<div class="notes_medical">
				
					<div class="card_medical" note-id="${item.id}" data-step="${item.phase}">
					 <div class="card_medical__actions">
                            <img src="./assets/img/svg/close.svg" alt="close icon" class="less-button">
                            <img src="./assets/img/svg/delete.svg" alt="delete note" class="delete-note">
                        </div>
					
						<h3 class="card_medical__title"></h3>
						                      <p class="card_medical__content" data-fullnote="${fullNote}" data-shortnote="${shortNote}">
                        ${shortNote}
                    </p>
                    </p>
						<div class="card_medica_footer">
							<div class="card_medical__date">
								${item.date}
							</div>
							<div class="card_medical__author">
								${item.author_name}
							</div>
						</div>
						<div class="card_medical__arrow">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="15" width="15">
														<path fill="#fff" d="M13.4697 17.9697C13.1768 18.2626 13.1768 18.7374 13.4697 19.0303C13.7626 19.3232 14.2374 19.3232 14.5303 19.0303L20.3232 13.2374C21.0066 12.554 21.0066 11.446 20.3232 10.7626L14.5303 4.96967C14.2374 4.67678 13.7626 4.67678 13.4697 4.96967C13.1768 5.26256 13.1768 5.73744 13.4697 6.03033L18.6893 11.25H4C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75H18.6893L13.4697 17.9697Z"></path>
													</svg>
						</div>
					</div>
				</div>
				
			</div>
		`;

										// Añadir el HTML a la div
										$('#extraaa').append(cardHtml);
									});
								}


							} else {
								$('#extraaa').empty();
								console.log("error")


							}
						},
						error: function(xhr, status, error) {
							// Maneja los errores de la solicitud AJAX
							$('#result').html('<p>Error en la solicitud: ' + error + '</p>');
						}
					});
				}

				$(document).on('click', '.less-button', function() {
					console.log("esconder");
					var cardContent = $(this).closest('.card_medical').find('.card_medical__content');
					var shortNote = cardContent.data('shortnote'); // Obtener el texto corto guardado
					cardContent.text(shortNote); // Restaurar el texto corto
					//	$(this).hide(); // Ocultar el botón "less"
				});


				$(document).on('click', '.card_medical__content', function() {
					console.log("mostrar");
					var fullNote = $(this).data('fullnote'); // Obtener el texto completo guardado
					$(this).text(fullNote); // Mostrar el texto completo
					$(this).siblings('.less-button').show(); // Mostrar el botón "less"
				});

				$(document).on('click', '.delete-note', function() {
					var $card = $(this).closest('.card_medical');
					var noteId = $card.attr('note-id'); // Obtener el ID de la nota
					var step = $card.data('step'); // Obtener el valor del atributo data-step
					console.log("borrar", noteId, "step", step);

					$.ajax({
						type: 'POST',
						url: './scripts/procedures/delete_note.php',
						data: {
							id: noteId,
							step: step // Enviar el step junto con el ID
						},
						success: function(response) {
							// Parsear la respuesta JSON
							var jsonResponse = JSON.parse(response);

							if (jsonResponse.status === 'success') {
								// Eliminar el elemento del DOM
								$card.closest('.extras_medical').remove();

								// Mostrar alerta y actualizar la vista
								showSweetAlert(
									"Listo!",
									jsonResponse.message,
									"success",
									1500,
									true,
									false
								);

								showNotes(step); // Llamada a la función para mostrar las notas, pasa el step si es necesario
							} else {
								alert('Hubo un error al eliminar la nota: ' + jsonResponse.message);
							}
						},
						error: function() {
							alert('Hubo un error al procesar la solicitud.');
						}
					});
				});

				function showSign(step, num_med_record) {
					$.ajax({
						url: './scripts/procedures/search_sign.php', // Cambia esto a la ruta del script PHP que manejará la petición
						type: 'POST',
						dataType: 'json',
						data: {
							fase: step,
							num_med: num_med_record
						},
						success: function(response) {
							if (response.success) {
								// Manejar el éxito
								console.log("Firma encontrada: ");
								// Limpiar el div antes de agregar la imagen
								$('#sign_step').empty();

								// Crear el párrafo "Firma:"
								var pFirma = $('<h4>').text('Firma:').css('margin-top', '1rem');

								// Crear una etiqueta de imagen y agregarla al div
								var img = $('<img>').attr('src', 'data:image/png;base64,' + response.url);

								// Añadir el párrafo y la imagen al div
								$('#sign_step').append(pFirma).append(img);

							} else {
								var firma_canva = `<p>Firma:</p>
    <canvas id="canvas" width="400" height="200" style="border: 1px solid black;"></canvas>
    <br>
    <button class="btn btn-danger" id="btnLimpiar">Limpiar</button>

    <button class="btn btn-light" id="btnCrearPdf">Guardar firma</button>
	
        <input type="checkbox" name="" id="checkboxVideollamada">
				<span> Videollamada </span>
	`;

								$('#sign_step').empty();
								$('#sign_step').append(firma_canva);
								//$('#extraaa').empty();

								const $botonCrearPdf = document.querySelector("#btnCrearPdf");
								const $canvas = document.querySelector("#canvas"),
									$btnLimpiar = document.querySelector("#btnLimpiar"),
									$id = document.querySelector("#id"),
									$nombre = document.querySelector("#nombre"),
									$apellido = document.querySelector("#apellido"),
									$direccion = document.querySelector("#direccion");
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
										)
										return;
									}
									console.log("num med", num_med_record)
									console.log("step", step)


									// Crear un objeto FormData y agregar los campos
									const formData = new FormData();

									formData.append("firma", imagenBase64);
									formData.append("fase", step);
									formData.append("num_med", num_med_record);

									// Serializar los datos para imprimir en consola
									const datosSerializados = {

										//firma: imagenBase64,
										fase: step,
										num_med: num_med_record
									};

									// Mostrar en consola los datos serializados y la imagen en base64
									console.log("Datos a enviar:", datosSerializados);

									// Mostrar un mensaje de confirmación antes de enviar
									const confirmarEnvio = confirm("¿Estás seguro de que deseas enviar la información?");
									if (!confirmarEnvio) {
										return;
									}

									// Enviar los datos al servidor
									try {
										$botonCrearPdf.disabled = true;
										$botonCrearPdf.textContent = "Enviando...";

										const respuestaHttp = await fetch("./scripts/procedures/add_signature.php", {
											body: formData,
											method: "POST",
										});

										if (!respuestaHttp.ok) {
											throw new Error(`HTTP error! status: ${respuestaHttp.status}`);
										}

										const respuestaDelServidor = await respuestaHttp.text();
										console.log("Respuesta del servidor:", respuestaDelServidor);
										console.log(step, num_med_record)
										showSign(step, num_med_record);
										showSweetAlert(
											"Listo!",
											"Firma agregada",
											"success",
											1500,
											true,
											false
										)


									} catch (error) {
										console.error("Error al enviar los datos:", error);
										showSweetAlert()
									} finally {
										$botonCrearPdf.disabled = false;
										$botonCrearPdf.textContent = "Guardar firmar";
									}
								});





								// Aquí seleccionas el checkbox después de que ha sido añadido al DOM
								const checkboxVideollamada = document.querySelector('#checkboxVideollamada');

								if (checkboxVideollamada) {
									checkboxVideollamada.addEventListener('change', function() {
										if (this.checked) {
											// Añade el texto "Videollamada" al canvas cuando se marca el checkbox
											contexto.font = "20px Arial";
											contexto.fillStyle = "black";
											contexto.fillText("Videollamada", 10, 30);
										} else {
											limpiarCanvas(); // Limpiar el canvas si se desmarca el checkbox
										}
									});
								} else {
									console.error("Checkbox Videollamada no encontrado en el DOM.");
								}









							}
						},
						error: function(xhr, status, error) {
							console.error("Error en la petición AJAX: " + status + " - " + error);
						}
					});
				}



				$(document).on('click', '.view_imgs', function(e) {
					const procedureDateee = "<?php echo $procedure_date; ?>";
					console.log("fecha de prod ", procedureDateee);

					console.log("room ", room);


					$('#file').off('filebatchuploadcomplete filebatchpreupload fileuploaded filepredelete').fileinput('destroy');

					let totalFiles = 0;
					let filesUploaded = 0;
					e.preventDefault();
					let num_med_record = $(this).data('nummedrecord');
					let step = $(this).data('step');
					let clinic = $(this).data('clinic');
					let name = document.getElementById("px_name").innerText;
					console.log(name)
					let user_id = localStorage.getItem("user_id")
					console.log("userid", user_id)

					Swal.fire({
						title: "Cargando...",
						allowOutsideClick: false,
						showConfirmButton: false,
					});


					showSign(step, num_med_record);
					console.log("step a enviar", step)
					console.log("type a enviar", type)
					$.ajax({
							data: {
								num_med_record: num_med_record,
								step: step,
								type: type,
								clinic:clinic
							},
							dataType: "json",
							method: "POST",
							url: "scripts/photos/load_dir.php",
						})
						.done(function(response) {
							console.log(response);
							const upload_url = `scripts/photos/bunny_px_images.php?num_med_record=${num_med_record}&type=${type}&step=${step}&procedure_date=${procedureDateee}&room=${room}&name=${name}&user_id=${user_id}&clinic=${clinic}`;
							$(".inputfile-container").fadeIn("slow");
							$("#fileinput-title").html("Viendo fotos de: " + step);
							$("#fileinput-title").html('');
							var buttonHtml = `<button type="button" class="button_ui"  data-step="${step}">
					<span class="button_ui__text">Crear nota</span>
					<span class="button_ui__icon"></span>
				</button>`;




							showNotes(step)


							$('#noteextra').empty();
							$('#noteextra').append(buttonHtml);
							//$('#extraaa').empty();


							$('#file').fileinput({
								allowedFileExtensions: ["jpg", "png", "heic", "jpeg", "mov", "mp4"],
								language: "es",
								uploadUrl: upload_url,
								uploadAsync: true,
								showRemove: false,
								showCancel: false,
								initialPreview: response.initialPreview,
								initialPreviewConfig: response.initialPreviewConfig,
								initialPreviewAsData: true,
								overwriteInitial: false,
							}).on('filebatchpreupload', function(event, data) {
								totalFiles = data.files.length;
								filesUploaded = 0;
							}).on('filebatchuploadcomplete', function(event, files, extraData) {

							}).on('fileuploaded', function(event, data, previewId, index) {
								filesUploaded++;
							}).on('filepredelete', function(event, key, jqXHR, data) {
								var abort = true;
								if (confirm("La imagen se borrará permanentemente, ¿estás seguro/a?")) {
									abort = false;
								}
								return abort;
							});
							$(".kv-file-rotate,.file-drag-handle").css('display', 'none');

						})
						.fail(function(response) {
							console.log(response.responseText);
						}).always(function() {
							Swal.close();
						});






				});


				$(document).on('click', '.kv-file-zoom', function() {
					console.log("iamgen ")
					// Obtener el elemento padre más cercano con la clase .file-preview-frame
					var parentElement = $(this).closest('.file-preview-frame');

					// Buscar el elemento de la imagen dentro del elemento padre
					var imageUrl = parentElement.find('img').attr('src');

					console.log(imageUrl);

					//	$('.file-zoom-detail').attr('src', imageUrl);


					// nuevo codigo

					// Modificar la URL para eliminar "thumb"
					var modifiedUrl = imageUrl.replace('/thumb/', '/');

					console.log(modifiedUrl);

					// Actualizar el atributo src de .file-zoom-detail con la URL modificada
					$('.file-zoom-detail').attr('src', modifiedUrl);


				});


				Swal.close();
			});
		</script>






</body>


</html>