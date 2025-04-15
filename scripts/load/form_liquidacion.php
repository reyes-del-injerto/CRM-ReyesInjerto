<div class="form-heading">
    <h4><strong>Generar recibo de Pago de Procedimiento</strong></h4>
</div>
<div class="card-body">
    <form method="POST" action="scripts/add/settlement.php" id="new_invoice">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Fecha en que se expide<span class="login-danger">*</span></label>
                    <input class="form-control" type="date" name="invoice_date" id="r_invoice_date" value="<?= date("Y-m-d"); ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Nombre del Paciente<span class="login-danger">*</span></label>
                    <input class="form-control" type="text" name="patient_name" id="r_patient_name" readonly>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Tipo de Injerto:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="procedure_type" id="r_procedure_type" readonly>
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
                    <input type="date" class="form-control" name="advance_date" id="r_advance_date" readonly>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Monto Abonado (MXN) <span class="login-danger">*</span></label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">$</span>
                        <input type="number" class="form-control" name="advance_amount" id="r_advance_amount" placeholder="0" readonly>
                        <span class="input-group-text" id="basic-addon1">.00</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Método de pago del anticipo:<span class="login-danger">*</span></label>
                    <select class="form-control" name="payment_method" id="r_payment_method" readonly>
                        <option selected disabled>Selecciona</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="TDC">Tarjeta de crédito</option>
                        <option value="TDD">Tarjeta de débito</option>
                        <option value="Enlace digital">Enlace digital</option>
                        <option value="Depósito">Depósito</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>
            <div class="mb-3"></div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Costo total del procedimiento (MXN) <span class="login-danger">*</span></label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">$</span>
                        <input type="number" class="form-control" name="total_amount" id="r_total_amount" placeholder="0">
                        <span class="input-group-text" id="basic-addon1">.00</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Monto que liquidó (MXN) <span class="login-danger">*</span></label>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">$</span>
                        <input type="number" class="form-control" name="final_amount" id="r_final_amount" placeholder="0">
                        <span class="input-group-text" id="basic-addon1">.00</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Método de pago de liquidación:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="payment_method" id="r_final_payment_method">
                        <option selected disabled>Selecciona</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="TDC">Tarjeta de crédito</option>
                        <option value="TDD">Tarjeta de débito</option>
                        <option value="Enlace digital">Enlace digital</option>
                        <option value="Depósito">Depósito</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>
            <div class="mb-3"></div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Fecha del procedimiento<span class="login-danger">*</span></label>
                    <input type="date" class="form-control" name="procedure_date" id="r_procedure_date" readonly>
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
    $()


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

    function disableForm() {
        $("#new_invoice input, #new_invoice select, #new_invoice textarea, #new_invoice button").prop('disabled', true);
        console.log('disable form');
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
</script>