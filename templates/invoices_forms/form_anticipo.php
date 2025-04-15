<div class="form-heading">
	<h4><strong>Generar recibo de Anticipo de Procedimiento</strong></h4>
</div>
<div class="card-body">
	<form method="POST" action="scripts/sales/add_advance_receipt.php" id="new_invoice">
		<input type="hidden" name="lead_id" id="r_lead_id">
		<input type="hidden" name="userid" id="userid">

		<div class="row">
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Fecha en que se expide<span class="login-danger">*</span></label>
					<input class="form-control" type="date" name="receipt_date" id="r_receipt_date" value="<?= date("Y-m-d"); ?>" required>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Nombre del Paciente<span class="login-danger">*</span></label>
					<input class="form-control" type="text" name="patient_name" id="r_patient_name" required>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Tipo de Injerto:<span class="login-danger">*</span></label>
					<select class="form-control select" name="procedure_type" id="r_procedure_type" required readonly>
						<option value="" selected disabled>Selecciona</option>
						<option value="Capilar">Capilar</option>
						<option value="Barba">Barba</option>
						<option value="Ambos">Ambos</option>
					</select>
				</div>
			</div>
			<div class="mb-3"></div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Fecha del Anticipo<span class="login-danger">*</span></label>
					<input type="date" class="form-control" name="payment_date" id="r_payment_date" required>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Método de pago:<span class="login-danger">*</span></label>
					<select class="form-control select" name="payment_method" id="r_payment_method" required onchange="toggleDollarInputs()">
						<option value="" selected disabled>Selecciona</option>
						<option value="Transferencia">Transferencia</option>
						<option value="TDC">Tarjeta de crédito</option>
						<option value="TDD">Tarjeta de débito</option>
						<option value="Enlace digital">Enlace digital</option>
						<option value="Depósito">Depósito</option>
						<option value="Efectivo">Efectivo</option>
						<option value="Dólares">Dólares</option>
						<option value="Otro">Otro</option>
					</select>
				</div>
			</div>

			<!-- Cantidad DLS -->
			<div class="col-12 col-md-4" id="dollar_amount" style="display:none;">
				<div class="input-block local-forms">
					<label>Dólares <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="advance_amount_dls" id="r_advance_amount_dls" placeholder="0" min=0>
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>

			<!-- Precio DLS -->
			<div class="col-12 col-md-4" id="price_dls" style="display:none;">
				<div class="input-block local-forms">
					<label>Precio del dolar <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="price_dls" id="r_price_dls" placeholder="0" min="0" step="0.01">
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>

			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Monto del Anticipo (MXN) <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="advance_amount" step="0.01" id="r_advance_amount_mxn" placeholder="0" min=0 required>
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>

			<div class="mb-3"></div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Costo total del procedimiento (MXN) <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="total_amount" id="r_total_amount" placeholder="0" min=0 step=1 required>
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Importe a liquidar (MXN) <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="pending_amount" id="r_pending_amount" placeholder="0" min=0 required readonly>
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block ">
					<label>Fecha del procedimiento<span class="login-danger">*</span></label>
					<input type="date" class="form-control" name="procedure_date" id="r_procedure_date" required disabled readonly>
				</div>
				<label class="no-style" id="cbx_open_date">
					<input type="checkbox" name="open_date" id="r_open_date" value="1" disabled><strong>Fecha abierta para 2024</strong>
				</label>
			</div>
			<div class="mb-3"></div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Generado en:<span class="login-danger">*</span></label>
					<select class="form-control select" name="clinic" id="i_clinic" required>
						<option value="" selected disabled>Selecciona</option>
						<option value="Queretaro">Queretaro</option>
						<option value="Pedregal">Pedregal</option>
						<option value="Santa Fe">Santa Fe</option>
					</select>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Notas (aparecerán en el recibo)</label>
					<textarea class="form-control" rows="3" cols="30" name="public_notes" id="public_notes"></textarea>
				</div>

				<input type="checkbox" name="ingles" id="ingles">
				<label for="ingles">Generar en ingles</label>
			</div>
			<div class="col-12">
				<div class="doctor-submit text-end">
					<button type="submit" class="btn btn-primary submit-form me-2">Generar</button>
				</div>
			</div>
		</div>
	</form>

	<script>
	function toggleDollarInputs() {
		const paymentMethod = document.getElementById('r_payment_method').value;
		const dollarAmountDiv = document.getElementById('dollar_amount');
		const priceDlsDiv = document.getElementById('price_dls');

		if (paymentMethod === "Dólares") {
			dollarAmountDiv.style.display = "block";
			priceDlsDiv.style.display = "block";
		} else {
			dollarAmountDiv.style.display = "none";
			priceDlsDiv.style.display = "none";
			document.getElementById('r_advance_amount_mxn').value = ''; // Limpiar campo si no es dólares
		}

		calculateMXN();
	}

	function calculateMXN() {
		const advanceAmountDLS = parseFloat(document.getElementById('r_advance_amount_dls').value) || 0;
		const priceDLS = parseFloat(document.getElementById('r_price_dls').value) || 0;
		const advanceAmountMXN = document.getElementById('r_advance_amount_mxn');
		const totalAmount = parseFloat(document.getElementById('r_total_amount').value) || 0;

		// Si se han ingresado ambos valores, calcula el monto en MXN
		if (priceDLS > 0) {
			const mxnAmount = advanceAmountDLS * priceDLS;
			advanceAmountMXN.value = mxnAmount.toFixed(2);
		} else {
			advanceAmountMXN.value = ''; // Limpiar si no hay precio
		}

		// Calcular el importe a liquidar
		const pendingAmount = totalAmount - parseFloat(advanceAmountMXN.value || 0);
		document.getElementById('r_pending_amount').value = pendingAmount.toFixed(2);
	}

		// Escuchar cambios en el campo de monto de anticipo en dólares
		// Escuchar cambios en los campos relevantes
	document.getElementById('r_advance_amount_dls').addEventListener('input', calculateMXN);
	document.getElementById('r_price_dls').addEventListener('input', calculateMXN);
	document.getElementById('r_payment_method').addEventListener('change', toggleDollarInputs);
	</script>

</div>


<script>
	$(document).ready(function() {
		// Obtener el user_id de localStorage

		getProfileData();
		// Cargar otros valores en el formulario
		async function getProfileData() {
			console.log("recarga de todos los datos form anticipo")
			try {

				const response = await $.ajax({
					data: {
						lead_id: lead_id,
					},
					cache: false,
					dataType: 'json',
					method: 'POST',
					url: 'scripts/sales/load_info_profile_px_invoices.php',
				})

				if (response.success) {
					console.log("nuevos datos", response.profile[0].procedure_date)
					let nuevoDatoFecha = response.profile[0].procedure_date
					$("#r_procedure_date").val("");
					console.log("post asig", document.getElementById("r_procedure_date").value)
					console.log("INTER", nuevoDatoFecha)
					$("#r_procedure_date").val(nuevoDatoFecha);
					console.log("post asig", document.getElementById("r_procedure_date").value)
					//return response.profile[0]
				} else {
					throw new Error(response.message)
				}
			} catch (error) {
				/* console.log("getProfileData error", response)

				showSweetAlert('Error', error.message, 'error', 2300, true, false)
				console.error('Ocurrió un error:', error)
				throw error */
			}
		}

		console.log("volver a cargar")
		reloadValues();

		function reloadValues() {
			const userId = localStorage.getItem("user_id");
			console.log("el user id es: ", userId);

			// Asignar el user_id al input con id="user_id"
			if (userId) {
				console.log("if si")
				console.log(document.getElementById("userid"))
				$("#userid").val(userId);
				console.log("post asig", document.getElementById("userid"))

			}

			$("#r_lead_id").val(lead_id);
			$("#r_patient_name").val(profile.first_name + " " + profile.last_name);
			$("#r_procedure_type").val(profile.procedure_type);
			$("#r_total_amount").val(profile.quoted_cash_amount);
			$("#r_procedure_type").prop('disabled', true);

			if (profile.procedure_date == "2030-01-01") {
				$("#cbx_open_date").css('display', 'block');
				$("#r_procedure_date").prop('disabled', true);
				$("#r_procedure_date").css('display', 'none');
				$("#r_open_date").prop('checked', true);
			} else {
				$("#cbx_open_date").css('display', 'none');
				$("#r_procedure_date").prop('disabled', false);
				$("#r_procedure_date").css('display', 'block');
				$("#r_procedure_date").val(profile.procedure_date);
			}
		}


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
						$("#r_procedure_type").prop('disabled', false);
						createInvoice();
					}
				});
			} else {
				Swal.fire({
					title: "Error",
					text: "Faltan algunos campos del formulario",
					icon: "error",
					timer: 1700,
					timerProgressBar: true,
					showConfirmButton: false,
				});
			}
		});

		function setPendingAmount() {
			let total_amount = parseFloat($("#r_total_amount").val()) || 0;
			let advance_amount = parseFloat($("#r_advance_amount_mxn").val()) || 0;
			let pending_amount = total_amount - advance_amount;
			$("#r_pending_amount").val(pending_amount.toFixed(2));
		}

		$(document).ready(function() {
			$("#r_total_amount").on("input", setPendingAmount);
			$("#r_advance_amount_mxn").on("input", setPendingAmount);
		});

		function disableForm() {
			$("#new_invoice input, #new_invoice select, #new_invoice textarea, #new_invoice button").prop('disabled', true);
		}

		function createInvoice() {
			const form = $("#new_invoice")[0];
			let method = $(form).attr('method');
			let url = $(form).attr('action');
			let formData = $(form).serialize();

			console.log(`formData ${formData}`);

			$.ajax({
					data: formData,
					cache: false,
					method: method,
					url: url,
					dataType: 'json'
				})
				.done(function(response) {
					$("#i_type").prop('disabled', true);
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
						timer: 1700,
						timerProgressBar: true,
						showConfirmButton: false,
					});
				});
		}

		function loadInvoicePDF(url) {
			document.getElementById('pdfInvoiceViewer').src = url;
			document.getElementById('pdfInvoiceDownload').href = url;
		}
	});
</script>