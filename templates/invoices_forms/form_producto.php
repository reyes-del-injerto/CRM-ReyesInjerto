<?php date_default_timezone_set('America/Mexico_City'); ?>
<div class="form-heading">
    <h4><strong>Generar recibo de Compra de Producto</strong></h4>
</div>
<div class="card-body">
    <form method="POST" action="scripts/sales/add_receipt.php" id="new_invoice">
        <input type="hidden" name="invoice_type" id="invoice_type" value="producto">
        <input type="hidden" name="lead_id" id="lead_id_pr">
        <input type="hidden" name="userid" id="userid">



        <div class="row">
            <div class="col-6 col-md-4 show-after">
                <div class="input-block local-forms">
                    <label>Fecha <span class="login-danger">*</span></label>
                    <input class="form-control" type="date" name="receipt_date" id="date" value="<?= date("Y-m-d"); ?>" required>
                </div>
            </div>
            <div class="col-6 col-md-4 show-after">
                <div class="input-block local-forms">
                    <label>Nombre del Paciente <span class="login-danger">*</span></label>
                    <input class="form-control" type="text" name="full_name" id="full_name" readonly>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="input-block local-forms">
                    <label>Producto (s):<span class="login-danger">*</span></label>
                    <input class="form-control" type="text" name="product" id="product" required>

                </div>
            </div>
            <div class="col-6 col-md-4 show-after">
                <div class="input-block local-forms">
                    <label>Forma de Pago:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="payment_method" id="payment_method" required>
                        <option value="" selected disabled>Selecciona</option>
                        <option value=1>Efectivo</option>
                        <option value=2>Transferencia</option>
                        <option value=3>Tarjeta de Débito</option>
                        <option value=4>Tarjeta de Credito</option>
                        <option value=5>Dólares</option>
                        <option value=6>Deposito</option>
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


            <div class="col-6 col-md-4 show-after">
                <div class="input-block local-forms">
                    <label>Monto total (MXN) <span class="login-danger">*</span></label>
                    <div class="time-icon">
                        <input type="number" class="form-control" name="amount" id="amount" min=0 step="0.1" required>
                    </div>
                </div>
            </div>


            <div class="col-6 col-lg-4 show-after">
                <div class="input-block local-forms">
                    <label>Sucursal:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="clinic" id="clinic" required>
                        <option value="" selected disabled>Selecciona</option>
                        <option value="pedregal">Pedregal</option>
                        <option value="Queretaro">Queretaro</option>
                        <option value="Santa Fe">Santa Fe</option>
                        <!--    <option value="Queretaro">Queretaro</option> -->
                    </select>
                </div>
            </div>

            <div class="col-6 mx-auto show-after">
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
    var urlParams_for_id = new URLSearchParams(window.location.search);
    var leadId_producto = urlParams_for_id.get('id');

    // Asegúrate de que el parámetro existe antes de asignarlo al input
    if (leadId_producto) {
        document.getElementById('lead_id_pr').value = leadId;
    }

    var userId_fp = localStorage.getItem('user_id');

    // Asigna el valor al input con id "userid"
    if (userId_fp) {
        document.getElementById('userid').value = userId_fp;
    }




    // Mostrar u ocultar los campos de dólares según el método de pago seleccionado
    $("#payment_method").change(function() {
        const paymentMethod = $(this).val();

        if (paymentMethod == 5) { // Dólares seleccionado
            $("#dollar_amount").show();
            $("#price_dls").show();
            // Requerir los campos de dólares
            $("#r_advance_amount_dls").attr("required", true);
            $("#r_price_dls").attr("required", true);
        } else {
            $("#dollar_amount").hide();
            $("#price_dls").hide();
            // Quitar el requerimiento si no es dólares
            $("#r_advance_amount_dls").removeAttr("required");
            $("#r_price_dls").removeAttr("required");
            // Restablecer el valor de amount a 0 si no es dólares
            $("#amount").val(0);
        }
    });

    // Realizar la multiplicación entre el monto en dólares y el precio del dólar
    $("#r_advance_amount_dls, #r_price_dls").on('input', function() {
        const advanceAmountDls = parseFloat($("#r_advance_amount_dls").val()) || 0;
        const priceDls = parseFloat($("#r_price_dls").val()) || 0;

        // Multiplicar el monto en dólares por el precio del dólar y asignarlo a 'amount'
        const totalAmount = advanceAmountDls * priceDls;
        $("#amount").val(totalAmount.toFixed(2)); // Limitar a 2 decimales
    });
</script>
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
            showSweetAlert();
        }
    })

    function loadInvoicePDF(url) {
        document.getElementById('pdfInvoiceViewer').src = url;
        document.getElementById('pdfInvoiceDownload').href = url;
    }

    function createInvoice() {
        const form = $("#new_invoice")[0];
        let method = $(form).attr('method');
        let url = $(form).attr('action');
        let formData = $(form).serialize();

        console.log("creando recibo con", formData);

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
                showSweetAlert();
            })
    }

    function disableForm() {
        //$("#new_invoice input, #new_invoice select, #new_invoice textarea, #new_invoice button").prop('disabled', true);
        //console.log('disable form');
    }
</script>