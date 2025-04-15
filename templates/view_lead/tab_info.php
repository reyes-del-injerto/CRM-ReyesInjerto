<!-- TAB: Seguimiento del Lead -->
<div id="tab-info" class="tab tab-active">
	<div class="row">
		<div class="col-12 col-md-6">
			<form id="update_lead" action="scripts/sales/update_lead.php" method="POST">
				<div class="row">
					<div class="col-12">
						<div class="form-heading">

							<strong>Información del Lead </strong>
							<button id="editLeadForm" style="margin-bottom: 25px;" type="button" class="btn btn-light position-relative">
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									Editar
								</span>
								<i style="cursor:pointer;" class=" fa fa-pencil" id=""></i>
							</button>
							<input type="hidden" name="id" id="id" value="<?= $_GET['id']; ?>">
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Nombre (s) <span class="login-danger">*</span></label>
							<input class="form-control" type="text" name="first_name" id="first_name" required disabled>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Apellido (s)</label>
							<input class="form-control" type="text" name="last_name" id="last_name" disabled>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Clínica *</label>
							<select class="form-control select" name="clinic" id="clinic" required disabled>
								<option value="" selected disabled>Selecciona</option>
								<option value="CDMX">CDMX</option>

								<option value="Queretaro">Queretaro</option>
							</select>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Origen:</label>
							<select class="form-control select" name="origin" id="origin" required disabled>
								<option value="" selected disabled>Selecciona</option>
								<option value="Facebook">Facebook</option>
								<option value="Instagram">Instagram</option>
								<option value="Tiktok">Tiktok</option>
								<option value="Google">Google</option>
								<option value="Whatsapp">Whatsapp</option>
								<option value="Referido">Referido</option>
								<option value="Organico">Orgánico</option>
								<option value="Recomendado">Recomendado</option>
								<option value="Pagina">Pagina</option>
								<option value="Px">Ya es px</option>
								<option value="Campaña">Campaña publicitaria</option>
								<option value="Otro">Otro</option>
								<option value="Px">Desconocido</option>
							</select>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Celular <span class="login-danger">*</span></label>
							<input class="form-control" type="text" name="phone" id="phone" required disabled>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Interesado en:<span class="login-danger">*</span></label>
							<select class="form-control select" name="interested_in" id="interested_in" required disabled>
								<option value="" selected disabled>Selecciona</option>
								<option value="Capilar">Capilar</option>
								<option value="Barba">Barba</option>
								<option value="Ambos">Ambos</option>
								<option value="Tratamientos">Tratamientos</option>
								<option value="Micro">Micro</option>
								<option value="Se desconoce">Se desconoce</option>
							</select>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Etapa<span class="login-danger">*</span></label>
							<select class="form-control select" name="stage" id="stage" required disabled>
								<option value="" selected disabled>Selecciona</option>
								<option value="Nuevo Lead">Nuevo Lead</option>
								<option value="Lead en Prospección">Lead en Prospección</option>
								<option value="Prospecto Interesado">Prospecto Interesado</option>
							</select>
						</div>
					</div>
					<div class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Propietaria (o):<span class="login-danger">*</span></label>
							<select class="form-control select" name="seller" id="seller" required disabled>
								<option value="" selected disabled>Selecciona</option>
								<option value="Janeth Ruíz">Janeth Ruíz</option>
								<option value="Marisol Olmos">Marisol Olmos</option>
								<option value="Adriana Silva">Adriana Silva</option>
								<option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
								<option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
								<option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
								<option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
							</select>
						</div>
					</div>

					<div id="contenedor_evaluator" class="col-12 col-md-6 col-xl-4">
						<div class="input-block local-forms">
							<label>Valorado por: (o):<span class="login-danger">*</span></label>
							<select class="form-control select" name="evaluator" id="evaluator" disabled>
								<option value="" selected disabled>Selecciona</option>
								<option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
								<option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
								<option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
								<option value="Dra. Samanta Soto">Dra. Samanta Soto</option>
								<option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
								<option value="Marisol Olmos">Marisol Olmos</option>
								<option value="Janeth Ruiz">Janeth Ruiz</option>
								<option value="Adriana Silva">Adriana Silva</option>
								<option value="Dr. Luis Andres Peña Melendez">Dr. Luis Andres Peña Melendez</option>
							</select>
						</div>
					</div>


					<div class="col-12">
						<div class="input-block local-forms">
							<label>Notas:<span class="login-danger">*</span></label>
							<textarea rows=5 class="form-control" type="text" name="notes" id="notes" disabled></textarea>
						</div>
					</div>
					<div class="col-12">
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-primary" id="btn_update_lead" style="display:none;">Guardar Cambios</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="col-12 col-md-6">
			<div class="col-12">
				<div class="form-heading">
					<h4><strong>Actividades:</strong></h4>
					<button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#miModal" data-type="task"><i class="fa fa-tasks"></i></button>
				</div>
			</div>
			<div class="timeline-container">
				<h1 class="project-name"></h1>
				<div id="timeline">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('update_lead');
		const button = document.getElementById('btn_update_lead');

		// Asegurar que el campo oculto tenga el ID correcto desde la URL
		const urlParams = new URLSearchParams(window.location.search);
		const id = urlParams.get("id");

		if (id) {
			const idInput = document.getElementById("id");
			if (idInput) {
				idInput.value = id;
			}
		}

		// Deshabilitar inputs al enviar el formulario
		form.addEventListener('submit', function(event) {
			const inputs = form.querySelectorAll('input, select, textarea');
			inputs.forEach(input => {
				input.disabled = true;
			});
		});

		// Add event listener to the stage select
		$('#stage').on('change', function() {
			const selectedValue = $(this).val();
			console.log('Valor seleccionado en Stage:', selectedValue);
			if (selectedValue === 'Valorado') {
				$('#contenedor_evaluator').show();
				$('#evaluator').prop('required', true);
			} else {
				$('#contenedor_evaluator').hide();
			}
		});
	});
</script>
