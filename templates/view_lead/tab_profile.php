<!-- TAB: Perfil del Px -->
<div id="tab-profile" class="tab" style="display:none;">
	<div class="row">
		<div class="col-lg-12">
			<div class="doctor-personals-grp">
				<div class="card">
					<div class="card-body">
						<div class="tab-content-set ignielHorizontal" id="files_tabs">
							<ul class="nav">
								<li>
									<a href="#" data-tab="summary" id="summary"><span class="set-about-icon me-2"><img src="assets/img/icons/menu-icon-02.svg" alt></span>Resumen</a>
								</li>
								<li>
								<li>
									<a href="#" data-tab="procedure" id="procedure-payment"><span class="set-about-icon me-2"><img src="assets/img/icons/menu-icon-02.svg" alt></span>Pago de Proced.</a>
								</li>
								<li>
									<a href="#" data-tab="photos"><span class="set-about-icon me-2"><img src="assets/img/icons/menu-icon-02.svg" alt></span>Fotos de Valoración</a>
								</li>
								<li data-tab="hc">
									<a href="#" data-tab="hc"><span class="set-about-icon me-2"><img src="assets/img/icons/menu-icon-02.svg" alt></span>Historia Clínica</a>
								</li>
								<li data-tab="id">
									<a href="#" data-tab="id"><span class="set-about-icon me-2"><img src="assets/img/icons/menu-icon-02.svg" alt></span>Identificación Oficial</a>
								</li>
								<li data-tab="labs">
									<a href="#" data-tab="labs"><span class="set-about-icon me-2"><img src="assets/img/icons/menu-icon-02.svg" alt></span>Laboratorios</a>
								</li>
							</ul>
						</div>
						<div id="tab-summary" class="tab tab-files">
							<div class="row">
								<div class="col-12">
									<div class="card">
										<div class="card-body">
											<form id="update_summary" action="scripts/sales/update_lead_summary.php" method="POST">
												<div class="row">
													<div class="col-12">
														<div class="form-heading">
															<h4>
																<strong>Información del paciente </strong>

															</h4>
															<input type="hidden" name="id" id="i_id" value="<?= $_GET['id']; ?>">
														</div>
													</div>
													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>Nombre (s) <span class="login-danger">*</span></label>
															<input class="form-control" type="text" name="first_name" id="i_first_name" required disabled>
														</div>
													</div>
													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>Apellido (s)<span class="login-danger">*</span></label>
															<input class="form-control" type="text" name="last_name" id="i_last_name" required disabled>
														</div>
													</div>
													<div class="col-12 col-md-2 col-xl-2">
														<div class="input-block select-gender">
															<label class="gen-label">Género<span class="login-danger">*</span></label>
															<div class="form-check-inline">
																<label class="form-check-label">
																	<input type="radio" name="gender" id="g_Hombre" value="Hombre" class="form-check-input" required><b>Hombre</b>
																</label>
															</div>
															<div class="form-check-inline">
																<label class="form-check-label">
																	<input type="radio" name="gender" id="g_Mujer" value="Mujer" class="form-check-input" required><b>Mujer</b>
																</label>
															</div>
														</div>
													</div>
													<div class="col-12 col-md-2 col-xl-2">
														<div class="input-block local-forms">
															<label>Clínica *</label>
															<select class="form-control select" name="clinic" id="i_clinic" required disabled>
																<option disabled>Selecciona</option>
																<option value="CDMX" selected>CDMX</option>
																<option value="Queretaro" selected>Queretaro</option>
															</select>
														</div>
													</div>
													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>Teléfono Celular Principal (+1 si es de USA) <span class="login-danger">*</span></label>
															<input class="form-control" type="text" name="phone_1" id="i_phone_1" required disabled>
														</div>
													</div>
													<!-- <div class="mt-4 col-12 col-md-6 col-xl-6">
																							<div class="input-block local-forms">
																								<label>Teléfono Celular Secundario (+1 si es de USA) <span class="login-danger"></span></label>
																								<input class="form-control" type="text" name="phone_2" id="i_phone_2">
																							</div>
																						</div> -->
													<!-- <div class="col-12 col-md-6 col-xl-6">
																							<div class="input-block local-forms">
																								<label>Email </label>
																								<input class="form-control" type="email" name="email" id="email">
																							</div>
																						</div> -->
													<!-- <div class="col-12 col-sm-12">
																							<div class="input-block local-forms">
																								<label>Dirección (dejar en blanco si no la tienes)<span class="login-danger">*</span></label>
																								<textarea class="form-control" name="address" id="address" rows="3" cols="30"></textarea>
																							</div>
																						</div>
																						<div class="col-12 col-sm-12">
																							<div class="input-block local-forms">
																								<label>Alergias o cuidados médicos necesarios<span class="login-danger">*</span></label>
																								<textarea class="form-control" name="allergies" id="allergies" rows="3" cols="30" required></textarea>
																							</div>
																						</div> -->
													<div class="col-12">
														<div class="form-heading">
															<h4>
																<strong>Información de la valoración</strong>

															</h4>
														</div>
													</div>
													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>Fecha de Valoración</label>
															<input class="form-control" type="date" name="assessment_date" id="i_assessment_date" required disabled>
														</div>
													</div>
													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>¿Por qué medio nos conoció?</label>
															<div class="time-icon">
																<input type="text" class="form-control" name="first_meet_type" id="i_first_meet_type" required disabled>
															</div>
														</div>
													</div>
													<!-- <div class="col-12 col-md-4 col-xl-4">
																							<div class="input-block local-forms">
																								<label>¿Por qué medio se cerró? </label>
																								<input class="form-control" type="text" name="closure_type" id="i_closure_type" required>
																							</div>
																						</div> -->

													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>La valoración se hizo ...</label>
															<select class="form-control select" name="assessment" id="i_assessment_type" required disabled>
																<option value="" selected disabled>Selecciona</option>
																<option value="Virtual">Sólo Virtual</option>
																<option value="Presencial">Sólo Presencial</option>
																<option value="Virtual y después presencial">Virtual y después presencial</option>
															</select>
														</div>
													</div>
													<div class="col-12 col-md-4 col-xl-4">
														<div class="input-block local-forms">
															<label>Realizó la valoración:</label>
															<select class="form-control select" name="closer" id="i_closer" required disabled>
																<option value="" selected disabled>Selecciona</option>
																<option value="Janeth Ruiz">Janeth Ruiz</option>
																<option value="Marisol Olmos">Marisol Olmos</option>
																<option value="Adriana Silva">Adriana Silva</option>
																<option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
																<option value="Dra. Lizbeth Carmona">Dra. Lizbeth Carmona</option>
																<option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
																<option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
																<option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
																<option value="Dra. Ana Karen">Dra. Ana Karen</option>
															</select>
														</div>
													</div>
													<div class="col-12">
														<div class="form-heading">
															<h4>
																<strong>Información del procedimiento</strong>

															</h4>
														</div>
													</div>
													<div class="col-12 col-md-6 col-xl-4">
														<div class="input-block local-forms">
															<label>Fecha del procedimiento (DD/MM/YYYY) <span class="login-danger">*</span></label>
															<input class="form-control" type="date" name="procedure_date" id="i_procedure_date" required disabled>
														</div>
													</div>
													<div class="col-12 col-md-6 col-xl-4">
														<div class="input-block local-forms">
															<label>Tipo de Injerto</label>
															<select class="form-control select" name="procedure_type" id="i_procedure_type" required disabled>
																<option selected disabled>Selecciona</option>
																<option value="Capilar">Capilar</option>
																<option value="Barba">Barba</option>
																<option value="Ambos">Ambos</option>
															</select>
														</div>
													</div>

													<div class="col-12 col-sm-12">
														<div class="input-block local-forms">
															<label style="font-size:12px;">¿Qué se le ofreció?<span class="login-danger">*</span></label>
															<textarea class="form-control" rows="3" cols="30" name="purpose" id="i_purpose" required disabled></textarea>
														</div>
													</div>
													<div class="col-12">
														<div class="form-heading">
															<h4><b>Cotización del Procedimiento (MXN)</b></h4>
														</div>
													</div>
													<div class="col-12 col-md-6 col-xl-4">
														<label>Costo total en Efectivo o Tarjeta de Débito <span class="login-danger">*</span></label>
														<input class="form-control" type="number" name="quoted_cash_amount" id="i_quoted_cash_amount" required>
													</div>
													<div class="col-12 col-md-6 col-xl-4">
														<label>Costo total en Tarjeta de Crédito<span class="login-danger">*</span></label>
														<input class="form-control" type="number" name="quoted_cc_amount" id="i_quoted_cc_amount" required>
													</div>
													<div class="col-12 col-md-6 col-xl-4">
														<label>¿Incluye Meses?<span class="login-danger">*</span></label>
														<input class="form-control" type="text" name="installments" id="i_installments" required>
													</div>
													<div class="col-12">
														<div class="doctor-submit text-end">
															<button type="submit" class="btn btn-primary submit-form me-2">Validar información</button>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="tab-procedure" class="tab tab-files">
							<div class="row">
								<!-- <div class="col-12 text-center">
									<label for="p_collection_notes"><strong>Notas de Cobro</strong></label>
									<textarea class="form-control" id="p_collection_notes" name="p_collection_notes" rows="4" required></textarea>
									<div class="col-12 mt-4">
										<div class="d-flex justify-content-end">
											<button type="submit" class="btn btn-primary" id="btn_update_collection_notes" style="display:none;">Guardar Cambios</button>
										</div>
									</div>
								</div> -->
								<div class="col-12">
									<div class="card">
										<div class="card-body">
											<div class="main-container">
												<div class="current-balance">
													<h2 id="h2_pending_amount"> </h2>
												</div>
												<div class="content-container">
													<table class="purchase-history">
														<thead>
															<tr>
																<td>Fecha</td>
																<td>Tipo</td>
																<td>Monto</td>
															</tr>
														</thead>
														<tbody id="tbody_payments_table">

														</tbody>
													</table>
													<table class="purchase-history">
														<tbody>
															<tr>
																<td colspan="3" class="add text-center text-success">Cotización en Tarjeta: <span id="quoted_cc_amount"><strong></strong></span></td>
															</tr>
															<tr>
																<td colspan="3" class="add text-center text-dark">Cotización en Efectivo: <span id="quoted_cash_amount"><strong></strong></span></td>
															</tr>
															<tr>
																<td colspan="3" class="add text-center text-dark">¿Se ofrecieron meses?: <span style="color:#e0ac44" id="installments"><strong></strong></span></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="tab-photos" class="tab tab-files" style="display:none;">
							<div class="row">
								<div class="col-sm-12">
									<div class="card">
										<div class="form-heading">
											<h4>Fotos de Valoración</h4>
										</div>
										<div class="card-body">
											<div class="inputfile-container">
												<input type="file" id="filephotos" name="filephotos[]" accept="image/*" multiple>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="tab-hc" class="tab tab-files" style="display:none;">
							<div class="row">
								<div class="col-sm-12">
									<div class="card">
										<div class="form-heading">
											<h4>Historia Clínica</h4>
										</div>
										<form id="healthForm" class="mb-3">
											<div class="form-group">
												<label for="health_conditions" class="form-label">Enfermedades relevantes</label>
												<input type="hidden" name="lead_id" id="lead_id">
												<input type="text" class="form-control" id="health_conditions" name="health_conditions" placeholder="Escriba aquí (si no aplica, escriba 'ninguna')" required>
											</div>

											<button type="submit" class="btn btn-primary mt-3">Enviar</button>
											<button type="button" id="editButton" class="btn btn-secondary mt-3" style="display: none;">Editar</button>
										</form>



										<div class="card-body">
											<div class="inputfile-container">
												<input type="file" id="filehc" name="filehc[]" accept="image/*,.pdf,.docx,.doc" multiple>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="tab-id" class="tab tab-files" style="display:none;">
							<div class="row">
								<div class="col-sm-12">
									<div class="card">
										<div class="form-heading">
											<h4>Identificación Oficial</h4>
										</div>
										<div class="card-body">
											<div class="inputfile-container">
												<input type="file" id="fileid" name="fileid[]" accept="image/*,.pdf,.docx,.doc" multiple>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="tab-labs" class="tab tab-files" style="display:none;">
							<div class="row">
								<div class="col-sm-12">
									<div class="card">
										<div class="form-heading">
											<h4>Laboratorios</h4>
										</div>
										<div class="card-body">
											<div class="inputfile-container">
												<input type="file" id="filelabs" name="filelabs[]" accept="image/*,.pdf,.docx,.doc" multiple>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	let urlParams = new URLSearchParams(window.location.search);
let leadId = urlParams.get('id');

if (leadId) {
    document.getElementById('lead_id').value = leadId;
}

	document.getElementById('healthForm').addEventListener('submit', function(event) {
		// Prevenir el recargo de la página
		event.preventDefault();

		// Crear objeto con los datos del formulario
		let formData = new FormData(this);

		// Enviar los datos usando AJAX (fetch)
		fetch('scripts/sales/leads/add_healthInfo.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json()) // Asegurarse de que se maneje como JSON
			.then(result => {
				// Manejar la respuesta del servidor
				console.log(result);
				if (result.success) {
					alert(result.message); // Mensaje de éxito
					// Deshabilitar el campo de enfermedades
					document.getElementById('health_conditions').setAttribute('disabled', true);
					document.getElementById('editButton').style.display = 'inline-block'; // Mostrar el botón de editar
				} else {
					alert(result.message); // Mensaje de error
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert("Hubo un error al enviar el formulario."); // Mensaje genérico de error
			});
	});

	// Manejar el evento del botón de editar
	document.getElementById('editButton').addEventListener('click', function() {
		// Hacer que el campo de enfermedades sea editable nuevamente
		document.getElementById('health_conditions').removeAttribute('disabled');
		this.style.display = 'none'; // Ocultar el botón de editar
	});
</script>