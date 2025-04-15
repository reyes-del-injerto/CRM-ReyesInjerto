<div class="form-heading">
	<h4><strong>Generar recibo de Pago de Procedimiento</strong></h4>
</div>
<div class="card-body">
	<form method="POST" action="scripts/sales/add_settlement_receipt.php" id="new_invoice">
		<input type="hidden" name="lead_id" id="r_lead_id">
		<input type="hidden" name="userid" id="userid">
        <script>
            const userId_r = localStorage.getItem("user_id");
			console.log("el user id liquidacion: ", userId_r);
				console.log(document.getElementById("userid"))
				$("#userid").val(userId_r);
				console.log("post asig", document.getElementById("userid"))

			
        </script>

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
					<input class="form-control" type="text" name="patient_name" id="r_patient_name" readonly required>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Tipo de Injerto:<span class="login-danger">*</span></label>
					<select class="form-control select" name="procedure_type" id="r_procedure_type" readonly required>
						<option selected disabled>Selecciona</option>
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
					<input type="date" class="form-control" name="advance_date" id="r_advance_date" required>
				</div>
			</div>
			<div class=" col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Monto Abonado (MXN) <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="advance_amount" id="r_advance_amount" placeholder="0" min="1" required>
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Método de pago del anticipo:<span class="login-danger">*</span></label>
					<select class="form-control" name="advance_payment_method" id="r_advance_payment_method" required>
						<option selected disabled value="">Selecciona</option>
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
			<div class="mb-3"></div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Fecha de Liquidación<span class="login-danger">*</span></label>
					<input type="date" class="form-control" name="settlement_date" id="r_settlement_date" required>
				</div>
			</div>
			
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Método de pago de liquidación:<span class="login-danger">*</span></label>
					<select class="form-control select" name="settlement_payment_method" id="r_settlement_payment_method" required>
						<option selected disabled value="">Selecciona</option>
						<option value="Transferencia">Transferencia</option>
						<option value="TDC">Tarjeta de crédito</option>
						<option value="TDD">Tarjeta de débito</option>
						<option value="Enlace digital">Enlace digital</option>
						<option value="Depósito">Depósito</option>
						<option value="Efectivo">Efectivo</option>
						<option value="Dolarés">Dolarés</option>
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
					<label>Monto que liquidó (MXN) <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="settlement_amount" id="r_settlement_amount" placeholder="0" min="1" required step="0.1">
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Costo total del procedimiento (MXN) <span class="login-danger">*</span></label>
					<div class="input-group mb-3">
						<span class="input-group-text" id="basic-addon1">$</span>
						<input type="number" class="form-control" name="total_amount" id="r_total_amount" placeholder="0" min="1" required step="0.1">
						<span class="input-group-text" id="basic-addon1">.00</span>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-4">
				<div class="input-block local-forms">
					<label>Generado en:<span class="login-danger">*</span></label>
					<select class="form-control select" name="clinic" id="r_clinic" required>
						<option value="" selected disabled>Selecciona</option>
						<option value="Queretaro">Querétaro</option>
						<option value="Pedregal">Pedregal</option>
						<option value="Santa Fe">Santa Fe</option>
					</select>
				</div>
			</div>
			<div class="col-12 col-md-8">
				<div class="input-block local-forms">
					<label>Notas (aparecerán en el recibo)</label>
					<textarea class="form-control" rows="3" cols="30" name="public_notes" id="r_public__notes"></textarea>
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
<script>
    // Función para mostrar u ocultar los campos de dólares
    document.getElementById('r_settlement_payment_method').addEventListener('change', function() {
        var paymentMethod = this.value;
        var dollarAmount = document.getElementById('dollar_amount');
        var priceDls = document.getElementById('price_dls');
        var settlementAmount = document.getElementById('r_settlement_amount');
        var advanceAmountDls = document.getElementById('r_advance_amount_dls');
        var priceDlsInput = document.getElementById('r_price_dls');

        // Si el método de pago es "Dólares", muestra los campos
        if (paymentMethod === 'Dolarés') {
            dollarAmount.style.display = 'block';
            priceDls.style.display = 'block';

            // Realiza la multiplicación al ingresar valores en los campos
            function calculateTotalInMXN() {
                var dollars = parseFloat(advanceAmountDls.value) || 0;
                var dollarPrice = parseFloat(priceDlsInput.value) || 0;

                // Calcula el monto en pesos
                var totalInMXN = dollars * dollarPrice;
                settlementAmount.value = totalInMXN.toFixed(2); // Mostrar con 2 decimales
            }

            // Escuchar los cambios en los inputs de dólares y precio
            advanceAmountDls.addEventListener('input', calculateTotalInMXN);
            priceDlsInput.addEventListener('input', calculateTotalInMXN);

        } else {
            // Si no es dólares, oculta los campos y limpia los valores
            dollarAmount.style.display = 'none';
            priceDls.style.display = 'none';
            advanceAmountDls.value = '';
            priceDlsInput.value = '';
        }
    });
</script>

<script>
	console.log(profile);
	$("#r_lead_id").val(lead_id);
	$("#r_patient_name").val(`${profile.first_name} ${profile.last_name}`);
	$("#r_procedure_type").val(profile.procedure_type);
	$("#r_total_amount").val(profile.quoted_cash_amount);
	$("#r_procedure_type").prop('disabled', true);

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
		}
	})

	function loadInvoicePDF(url) {
		document.getElementById('pdfInvoiceViewer').src = url;
		document.getElementById('pdfInvoiceDownloadAssesment').href = url;
	}

	$("#r_total_amount,#r_advance_amount").change(function() {
		let costo_proced = 0;
		let monto_anticipo = 0;
		let importe_liquidar = 0;

		setPendingAmount();
	});

	function setPendingAmount() {
		let total_amount = isNaN($("#r_total_amount").val()) ? 0 : $("#r_total_amount").val();
		let advance_amount = isNaN($("#r_advance_amount").val()) ? 0 : $("#r_advance_amount").val();
		let pending_amount = total_amount - advance_amount;

		$("#r_final_amount").val(pending_amount);
	}

	function disableForm() {
		// $("#new_invoice input, #new_invoice select, #new_invoice textarea, #new_invoice button").prop('disabled', true);
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
				console.log(response.responseText);
				Swal.fire({
					title: "Ocurrió un error",
					text: "Por favor, contacta a administración",
					icon: "error",
					timer: 1700,
					timerProgressBar: true,
					showConfirmButton: false,
				});
			})
	}
</script>