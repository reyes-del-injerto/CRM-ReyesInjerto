<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

// Función que obtiene la información del procedimiento
function obtenerInfoProcedimiento($conn, $procedure_id, $procedure_type)
{
	$sql_row = "SELECT CONCAT(sla.first_name, ' ', sla.last_name) AS name, sla.procedure_date, sla.procedure_type, ep.num_med_record, ep.touchup, ep.room, ep.specialist, ep.notes,  sla.status  
                FROM enf_procedures ep 
                INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id 
                WHERE ep.lead_id = ? AND sla.status != 0";

	// Si se recibe un procedure_type, agrega la condición
	if (!empty($procedure_type)) {
		$sql_row .= " AND sla.procedure_type = ?";
	}

	$sql = $conn->prepare($sql_row);

	// Verifica si hay un filtro para el tipo de procedimiento
	if (!empty($procedure_type)) {
		$sql->bind_param("is", $procedure_id, $procedure_type);
	} else {
		$sql->bind_param("i", $procedure_id);
	}

	if (!$sql->execute()) {
		throw new Exception("Error al obtener la info. del procedimiento: " . $sql->error);
	}

	$result = $sql->get_result();

	if ($result->num_rows > 1) {
		throw new Exception("Error de duplicidad. Contacta al administrador.");
	}

	return $result->fetch_object();  // Retorna el objeto con los datos del procedimiento
}

// Ejemplo de uso de la función
$procedure_id = $_GET['id'];
$procedure_type = $_GET['procedure_type'] ?? null;  // Si no hay un procedure_type, será null
$px_info = obtenerInfoProcedimiento($conn, $procedure_id, $procedure_type);

// Ahora $px_info contiene la información y puede ser utilizada más abajo en el HTML
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

	<title>Notificaciones | ERP | Los Reyes del Injerto</title>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">
	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<!-- Select2 CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
	<!-- Datetimepicker -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/datetimepicker/datetimepicker.min.css">
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
			<div class="content"> <!-- Page Header -->
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item">Enfermería</li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item"><a href="view_procedures.php">Procedimientos</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item active">Notificaciones</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-12">
								<p>En esta sección enviarás notificaciones del <span class="text-danger">procedimiento en curso. </span>Tales como:<br><br>
									-<b> Inicio y Término del Procedimiento.</b><br>
									-<b> Infiltración, Extracción, Incisión, Implantación. </b><br>
									-<b> Reportes del pizarrón por hora transcurrida</b></p>
								<br>
								-<b> Recuerda que puedes seleccionar al tipo de proced. que pertenecen: </b></p>
								<!-- Button trigger modal -->



								<div id="types_procedures" class="row">
									<div class="col-sm-12">
										<ul class="nav nav-tabs justify-content-end">
											<li class="nav-item">
												<a class="nav-link active" aria-current="page" href="#" data-procedure="1">Proced.</a>
											</li>
											<li class="nav-item">
												<a class="nav-link " href="#" data-procedure="2">2do Proced</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" href="#" data-procedure="3">Micro</a>
											</li>
										</ul>
									</div>
								</div>


							</div>

						</div>
						<div class="row">
							<div class="col-12">
								<div class="col-12 text-center">
									<h3 class="my-4">Mostrando px: <?= $px_info->name; ?></h3>

									<button type="button" class="btn btn-primary text-center" data-bs-toggle="modal" data-bs-target="#modalSelectNotif">
										Nueva notificación
									</button><br><br>
								</div>
							</div>
						</div>
						<div class="row">
							<div id="form_notification" class="form-notifications" style="display:none;">
								<div class="row">
									<div class="col-12 text-center">
										<form method="POST" id="form_notif" action="scripts/procedures/notifications/add.php" enctype="multipart/form-data">
											<div class="row">
												<div class="col-12">
													<div class="form-heading">
														<h4 id="form_title"></h4>
													</div>
												</div>
												<div class="col-12 col-md-6 col-xl-6">
													<input type="hidden" id="lead_id" name="lead_id" value="<?= $_GET['id']; ?>">
													<input type="hidden" id="user_id" name="user_id" value="">
													<input type="hidden" id="notif_type" name="notif_type" value="">
													<input type="hidden" id="num_med" name="num_med" value="<?= $px_info->num_med_record; ?>">

													<input type="hidden" id="touchup" name="touchup" value="<?= $px_info->touchup; ?>">
													<input type="hidden" name="process" id="process">
													<input type="hidden" name="specialist" id="specialist" value="<?= $px_info->specialist; ?>">
													<input type="hidden" name="room" id="room" value="<?= $px_info->room; ?>">
													<div class="input-block local-forms">
														<label>Nombre del px<span class="login-danger"> *</span></label>
														<select class="form-control" name="px_data" required>
															<option selected value="<?= $px_info->name . ' - ' . $px_info->num_med_record; ?>"><?= $px_info->name . ' - ' . $px_info->num_med_record; ?></option>
														</select>
													</div>
												</div>
												<div id="dynamics_inputs" class="text-center"></div>
												<div class="col-12">
													<div class="input-block local-forms">
														<button type="submit" class="btn btn-primary submit-form me-2">Enviar Notificación</button>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-body">
										<div class="activity">
											<div class="activity-box">
												<ul class="activity-list ">
													<li>
														<h4>
															<span class="badge bg-danger">Seleccione un procedimiento</span>
														</h4>
													</li>
												</ul>

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="row">
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="modalSelectNotif" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Selecciona el tipo de notificacióoon</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row text-center">
						<span>Notificación sugerida:</span>
						<hr>
						<div id="next_notification"></div>
						<hr class="mt-4">
						<button data-process="-1" class="mt-4 btn btn-outline-danger btn-xs btn-notif-select" href="#">Enviar incidencia</button>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
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

	<!-- DatetimePicker -->
	<script src="assets/plugins/moment/moment.min.js"></script>
	<script src="assets/plugins/datetimepicker/datetimepicker.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script src="assets/plugins/fileupload/fileupload.min.js"></script>
	<script src="assets/js/app.js"></script>
	<script>
		const lead_id = $("#lead_id").val();
		const specialist = $("#specialist").val();



		$(document).ready(function(e) {

			const urlParams_id = new URLSearchParams(window.location.search);
			const id_pr = urlParams_id.get('id');

			// Recargar las notificaciones enviando el tipo de procedimiento
			cargarNotificaciones(id_pr, 1);
			showNextNotif(1)
			set_notifProcedure(1)

			function set_notifProcedure(procedure_type) {
				document.getElementById('notif_type').value = procedure_type;
			}

			document.querySelectorAll('#types_procedures .nav-link').forEach(link => {
				link.addEventListener('click', function(event) {
					event.preventDefault(); // Prevenir la acción predeterminada

					// Eliminar la clase 'active' de todos los enlaces
					document.querySelectorAll('#types_procedures .nav-link').forEach(link => {
						link.classList.remove('active');
					});

					// Añadir la clase 'active' al enlace clicado
					this.classList.add('active');

					// Obtener el tipo de procedimiento desde el atributo 'data-procedure'
					const procedureType = this.getAttribute('data-procedure');

					// Actualizar el valor del input oculto con el tipo de procedimiento
					set_notifProcedure(procedureType);

					// Obtener el ID del procedimiento desde la URL (si es necesario)
					const urlParams = new URLSearchParams(window.location.search);
					const id_pr = urlParams.get('id');

					// Recargar las notificaciones enviando el tipo de procedimiento
					cargarNotificaciones(id_pr, procedureType);
					showNextNotif(procedureType)
					console.log("ocultnado form")
					$('#form_notification').fadeOut('slow');

				});
			});

			// Función para cargar las notificaciones, ahora acepta 'procedureType'
			function cargarNotificaciones(id, procedureType) {
				// Obtener el user_id desde localStorage
				const user_id = localStorage.getItem('user_id');

				// Verificar si user_id existe
				if (!user_id) {
					console.error("No se encontró 'user_id' en localStorage.");
					return;
				}

				console.log("Cargando notificaciones de:", id, "para el usuario:", user_id, "con el tipo de procedimiento:", procedureType);

				$.ajax({
					url: `scripts/procedures/notifications/load_all.php?id=${id}&user_id=${user_id}&procedure_type=${procedureType}`, // Enviamos id, user_id y procedure_type en la URL
					type: "GET", // Tipo de petición
					dataType: "json", // Esperamos una respuesta en formato JSON
					success: function(response) {
						// Verificamos si la respuesta tiene éxito
						if (response.success) {
							// Vaciamos el contenido del div con clase 'activity-list' y cargamos los datos obtenidos
							$(".activity-list").html(response.data.join('')); // Une los datos en un solo string HTML
						} else {
							// Muestra el mensaje de error recibido en la respuesta
							$(".activity-list").html(`<h4><span class="badge bg-danger">${response.message}</span></h4>`);

						}
					},
					error: function(xhr, status, error) {
						// En caso de error, se puede mostrar un mensaje o manejarlo de otra manera
						console.error("Error al cargar las notificaciones:", error);
						$(".activity-list").html("<p>Error al cargar las notificaciones.</p>");
					}
				});

			}

			const urlParams = new URLSearchParams(window.location.search);
			const px_id = urlParams.get('id'); // 'user_id' debe ser el nombre del parámetro en la URL

			// Llamar a la función con el ID obtenido de la URL
			//cargarNotificaciones(px_id, 1);


			function showNextNotif(procedure_type) {
				$.ajax({
					method: "POST",
					url: "scripts/procedures/notifications/load_modal.php",
					dataType: 'json',
					data: {
						lead_id: lead_id,
						procedure_type: procedure_type,
					}
				}).done(function(response) {
					console.log("ready load modal", response)
					$("#next_notification").html(response.next_notif);
				}).fail(function(response) {
					showSweetAlert("Error", response.responseText, "error");
				});
			}

			showNextNotif(1)






			$('#form_notif').submit(function(e) {
				e.preventDefault()

				if (this.checkValidity()) {
					//$('.submit-form').attr('disabled', 'true');

					const method = $(this).attr('method');
					const url = $(this).attr('action');
					const inputFile = $(this).find('input[type="file"]');
					let contentType, processData;
					let formData;
					let notifType;

					console.log("pre", formData);

					if (inputFile.length > 0) {
						formData = new FormData(this);
						// Extraer el valor de notif_type
						notifType = formData.get('notif_type');
						console.log("🚀 ~ $ ~ formData:", formData);
						contentType = false;
						processData = false;
					} else {
						formData = $(this).serialize();
						console.log("errorzaso 🚀 ~ $ ~ formData:", formData);
						// Extraer el valor de notif_type de la cadena de consulta
						const params = new URLSearchParams(formData);
						notifType = params.get('notif_type');
					}

					$.ajax({
							data: formData,
							contentType: contentType,
							processData: processData,
							dataType: 'json',
							method: method,
							url: url,
							beforeSend: function() {
								Swal.fire({
									title: 'Cargando...',
									text: 'Por favor, espera',
									showConfirmButton: false,
									allowOutsideClick: false,
								});
							},
						})
						.done(function(response) {
							Swal.close();
							if (response.success) {
								Swal.fire({
									title: 'Gracias!',
									text: response.message,
									icon: 'success',
									timer: 4000,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading();
										// Obtener el px_id de los parámetros de la URL
										const urlParams = new URLSearchParams(window.location.search);
										const px_id = urlParams.get('id');
										// Pasar notifType a la función cargarNotificaciones
										cargarNotificaciones(px_id, notifType);
										showNextNotif(notifType)
										$('#form_notification').fadeOut('slow');
									},

								});

							} else {
								$('.submit-form').removeAttr('disabled');
								showSweetAlert('Error', response.message, 'error');
							}
						})
						.fail(function(response) {
							$('.submit-form').removeAttr('disabled');
							console.log(response.responseText);
							showSweetAlert('Error!', response.message, 'error');
						});
				}

			})
		});

		$(document).on('click', '.btn-notif-select', function(e) {
			e.preventDefault();

			const current_process_number = $(this).data('process')
			$('#process').val(current_process_number)

			const time_input = `
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="input-block local-forms">
                            <label>A las<span class="login-danger"> *</span></label>
                            <input type="text" class="form-control datetimepicker-time" name="hour" required>
                        </div>
                    </div>`;

			const procedure_finished_input = `
                    <div class="col-12 col-xl-6">
                        <div class="input-block local-forms">
                            <label>Unidades Foliculares<span class="login-danger"> *</span></label>
                            <input type="number" class="form-control" name="uf" required>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="input-block local-forms">
                            <label>Folículos<span class="login-danger"> *</span></label>
                            <input type="number" class="form-control" name="hair_follicles" required>
                        </div>
                    </div>
                    <div class="input-block local-forms">
                        <label>Especialista a cargo<span class="login-danger"> *</span></label>
                        <select class="form-control" name="specialist" required>
                            <option selected value="${specialist}">${specialist}</option>
                        </select>
                    </div>`;

			const notes_input = `    
                    <div class="col-12">
                        <div class="input-block local-forms">
                            <label>Notas</label>
                            <textarea class="form-control" name="comments" rows=3></textarea>
                        </div>
                    </div>
                </div>`;

			const upload_photo_input = `
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <label>Selecciona archivo<span class="login-danger" id="uploadFileRequired"> *</span></label>
                        <input class="form-control" id="uploadFile" type="file" name="file" accept="image/*" required="required">
                    </div>
                </div>`;

			const extraccion_hour_input = `
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <div class="input-block local-forms">
                            <label>La foto corresponde a<span class="login-danger"> *</span></label>
                            <select class="form-control" name="photo_type" id="photo_type" required>
                                <option selected disabled>Selecciona</option>
                                <option data-tipo="hora" value="1er hora extracción">Primera hora de extracción</option>
                                <option data-tipo="hora" value="2da hora extracción">Segunda hora de extracción</option>
                                <option data-tipo="hora" value="3ra hora extracción">Tercera hora de extracción</option>
                                <option data-tipo="hora" value="4ta hora extracción">Cuarta hora de extracción</option>
								<option data-tipo="hora" value="5ta hora extracción">Quinta hora de extracción</option>
                                <option data-tipo="final" value="Conteo final extracción">Conteo final de extracción</option>
                            </select>
                        </div>
                    </div>
                </div>`;

			const implantacion_hour_input = `
                <div class="col-12 col-md-6 col-xl-6">
                    <div class="input-block local-forms">
                        <div class="input-block local-forms">
                            <label>La foto corresponde a<span class="login-danger"> *</span></label>
                            <select class="form-control" name="photo_type" id="photo_type" required>
                                <option selected disabled>Selecciona</option>
                                <option data-tipo="hora" value="1er hora implantación">Primera hora de implantación</option>
                                <option data-tipo="hora" value="2da hora implantación">Segunda hora de implantación</option>
                                <option data-tipo="hora" value="3ra hora implantación">Tercera hora de implantación</option>
                                <option data-tipo="hora" value="4ta hora implantación">Cuarta hora de implantación</option>
                                <option data-tipo="hora" value="5ta hora implantación">Quinta hora de implantación</option>
                                <option data-tipo="hora" value="6ta hora implantación">Sexta hora de implantación</option>
                                <option data-tipo="hora" value="7ta hora implantación">Septima hora de implantación</option>
                                <option data-tipo="hora" value="8va hora implantación">Octava hora de implantación</option>
                             

                            </select>
                        </div>
                    </div>
                </div>`;

			let notifications = {
				"-1": {
					title: 'Enviar incidencia',
					inputs: upload_photo_input + notes_input
				},
				0: {
					title: 'Px firmó documentos',
					inputs: `<div class="row">
											<div class="col-12 col-xl-6">
													<div class="input-block local-forms">
															<label>Terminó de firmar a las<span class="login-danger"> *</span></label>
															<input type="text" class="form-control datetimepicker-time" name="hour" required>
													</div>
											</div>
											<div class="col-12 col-xl-6">
													<div class="input-block local-forms">
															<label>Especialista a cargo<span class="login-danger"> *</span></label>
															<select class="form-control" name="specialist" required>
																	<option selected value="${specialist}">${specialist}</option>
															</select>
													</div>
											</div>
											<div class="col-12 col-xl-6">
													<div class="input-block local-forms">
															<label>Meta de extracción: <span class="login-danger"> *</span></label>
															<input class="form-control" type="text" name="goal" required>
													</div>
											</div>
											<div class="col-12">
													<div class="input-block local-forms">
															<label>Notas:</label>
															<textarea class="form-control" name="comments" rows=3></textarea>
													</div>
											</div>
									</div>`,
				},
				1: {
					title: 'Inicio infiltración',
					inputs: time_input + notes_input,
				},
				2: {
					title: 'Término infiltración',
					inputs: time_input + notes_input,
				},
				3: {
					title: 'Inicio extracción',
					inputs: time_input + notes_input,
				},
				3.1: {
					title: 'Reporte Pizarrón de Extracción',
					inputs: upload_photo_input + extraccion_hour_input + notes_input,
				},
				4: {
					title: 'Término extracción',
					inputs: time_input + notes_input,
				},
				5: {
					title: 'Inicio de infiltración',
					inputs: time_input + notes_input,
				},
				6: {
					title: 'Término de infiltración',
					inputs: time_input + notes_input,
				},
				7: {
					title: 'Inicio de incisiones',
					inputs: time_input + notes_input,
				},
				8: {
					title: 'Término de incisiones',
					inputs: time_input + notes_input,
				},
				9: {
					title: 'Inicio de implantación',
					inputs: time_input + notes_input,
				},
				9.1: {
					title: 'Reporte Pizarrón de implantación',
					inputs: upload_photo_input + implantacion_hour_input + notes_input,
				},
				10: {
					title: 'Término de procedimiento',
					inputs: time_input + procedure_finished_input + notes_input,
				},
			}

			$('#modalSelectNotif').modal('hide')
			$('#form_title').html(notifications[current_process_number].title)
			$('#dynamics_inputs').html(notifications[current_process_number].inputs)
			$('#form_notification').fadeIn('slow')

			if (current_process_number == "-1") {
				$("#uploadFile").removeAttr('required');
				$("#uploadFileRequired").hide()
			} else {
				$("#uploadFile").attr('required');
				$("#uploadFileRequired").show()
			}

			if ($('.datetimepicker-time').length > 0) {
				$(function() {
					$('.datetimepicker-time').datetimepicker({
						format: 'LT',
						icons: {
							up: 'fas fa-angle-up',
							down: 'fas fa-angle-down',
							next: 'fas fa-angle-right',
							previous: 'fas fa-angle-left',
						},
					})
				})
			}
		});

		$(document).on('click', '.delNotif', function(e) {
			e.preventDefault()
			const room = $("#room").val();
			const message_id = $(this).data('messageid')
			console.log("borrar lead room message:", lead_id, room, )



			const method = 'POST'
			const url = 'scripts/procedures/notifications/delete.php'
			Swal.fire({
				title: '¿Estás seguro?',
				text: 'Eliminarás la notificación de la plataforma. Esta acción no se puede revertir.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sí, borrar.',
				cancelButtonText: 'Cancelar',
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
							data: {
								lead_id: lead_id,
								room: room,
								message_id: message_id,
							},
							dataType: 'json',
							method: method,
							url: url,
							beforeSend: function() {
								Swal.fire({
									title: 'Cargando...',
									text: 'Por favor, espera',
									showConfirmButton: false,
									allowOutsideClick: false,
								})
							},
						})
						.done(function(response) {
							console.log(response)
							if (response['success']) {
								Swal.fire({
									title: 'Listo',
									text: response.message,
									icon: 'success',
									timer: 2000,
									timerProgressBar: true,
									didOpen: () => {
										Swal.showLoading()
										const timer = setTimeout(() => {
											location.reload()
										}, 1500)
									}
								})
							} else {
								console.log(response)
								showSweetAlert('Error', response.message, 'error')
							}
						})
						.fail(function(response) {
							console.log(response.responseText)
							showSweetAlert('Error', response.responseText, 'error')
						})
				}
			})
		})
	</script>


</body>

</html>