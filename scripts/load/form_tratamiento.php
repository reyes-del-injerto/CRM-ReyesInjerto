<?php date_default_timezone_set('America/Mexico_City'); ?>
<div class="form-heading">
	<h4><strong>Generar recibo de Aplicación de Tratamiento</strong></h4>
</div>
<div class="card-body">
	<form method="POST" action="scripts/add/invoice.php" id="new_invoice">
		<input type="hidden" name="invoice_type" id="invoice_type" value="tratamiento">
		<input type="hidden" name="lead_id" id="lead_id">
		<div class="row">
			<div class="col-12 col-md-4 show-after">
				<div class="input-block local-forms">
					<label>Fecha <span class="login-danger">*</span></label>
					<input class="form-control" type="date" name="receipt_date" id="date" value="<?= date("Y-m-d"); ?>">
				</div>
			</div>
			<div class="col-12 col-md-4 show-after">
				<div class="input-block local-forms">
					<label>Nombre del Paciente <span class="login-danger">*</span></label>
					<input class="form-control" type="text" name="full_name" id="full_name" readonly>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Tratamiento que se realizó:<span class="login-danger">*</span></label>
					<select class="form-control select" name="treatment" id="treatment">
						<option selected disabled>Selecciona</option>
						<option value=1>Factores de Crecimiento</option>
						<option value=2>Dutasteride</option>
						<option value=3>Exosomas</option>
					</select>
				</div>
			</div>
			<div class="col-12 col-md-4 show-after">
				<div class="input-block local-forms">
					<label>Monto total (MXN) <span class="login-danger">*</span></label>
					<div class="time-icon">
						<input type="number" class="form-control" name="amount" id="amount">
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4 show-after">
				<div class="input-block local-forms">
					<label>Forma de Pago:<span class="login-danger">*</span></label>
					<select class="form-control select" name="payment_method" id="payment_method">
						<option selected disabled>Selecciona</option>
						<option value=1>Efectivo</option>
						<option value=2>Transferencia</option>
						<option value=3>Tarjeta de Débito</option>
						<option value=4>Tarjeta de Credito</option>
						<option value=5>Dólares</option>
					</select>
				</div>
			</div>
			<div class="col-12 col-md-4 show-after">
				<div class="input-block local-forms">
					<label>Sucursal:<span class="login-danger">*</span></label>
					<select class="form-control select" name="clinic" id="clinic">
						<option selected disabled>Selecciona</option>
						<option value="Pedregal">Pedregal</option>
						<option value="Santa Fe">Santa Fe</option>
					</select>
				</div>
			</div>

			<div class="col-12 col-md-6 mx-auto show-after">
				<div class="input-block local-forms">
					<label>Notas (aparecerán en el recibo)</label>
					<textarea class="form-control" rows="3" cols="30" name="notes" id="notes"></textarea>
				</div>
			</div>
			<div class="col-6 col-lg-3" style="display:none;" id="input_other_employee">
				<div class="input-block local-forms">
					<label>Nombre de quien atendió: <span class="login-danger">*</span></label>
					<div class="time-icon">
						<input type="text" class="form-control" name="other_employee" id="other_employee">
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="doctor-submit text-end">
					<button type="submit" class="btn btn-primary submit-form me-2">Siguiente</button>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$("#lead_id").val(lead_id);
	$("#full_name").val(profile.first_name + " " + profile.last_name);

	$("#employee").change(function() {
		let employee_number = parseInt($(this).val());
		if (employee_number === 5) {
			$("#input_other_employee").fadeIn("slow");
		} else {
			$("#input_other_employee").fadeOut("slow");
		}
	});

	$("#new_invoice").submit(function(e) {
		e.preventDefault();
		if (this.checkValidity()) {
			Swal.fire({
				title: '¿La información es correcta?',
				text: "",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Sí, generar recibo.',
				cancelButtonText: 'Cancelar'
			}).then((result) => {
				if (result.isConfirmed) {
					createInvoice();
				}
			});
		} else {
			Swal.fire({
				title: "Error",
				text: "Faltan algunos campos del formulario",
				icon: "error",
				timer: 1700, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
				timerProgressBar: true, // Muestra una barra de progreso
				showConfirmButton: false, // No muestra el botón de confirmación
			});
		}
	})

	function loadInvoicePDF(url) {
		document.getElementById('pdfInvoiceViewer').src = url;
	}

	function createInvoice() {
		const form = $("#new_invoice")[0];
		let method = $(form).attr('method');
		let url = $(form).attr('action');
		let formData = $(form).serialize();

		$.ajax({
				data: formData,
				cache: false,
				method: method,
				url: url,
				dataType: 'json'
			})
			.done(function(response) {
				console.log(response);
				$("#divInvoicePdf").fadeIn("slow");
				let url = response.path;
				url = url.replace("../../", "", url);
				disableForm();
				loadInvoicePDF(url);
			})
			.fail(function(response) {
				console.log(response);
				Swal.fire({
					title: "Ocurrió un error",
					text: "Por favor, contacta a administración",
					icon: "error",
					timer: 1700, // Tiempo en milisegundos (en este caso, 3000 ms = 3 segundos)
					timerProgressBar: true, // Muestra una barra de progreso
					showConfirmButton: false, // No muestra el botón de confirmación
				});
			})
	}

	function disableForm() {
		$("#new_invoice input, #new_invoice select, #new_invoice textarea, #new_invoice button").prop('disabled', true);
		console.log('disable form');
	}
</script>