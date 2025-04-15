<!-- TAB: Valoración -->
<div id="tab-assessment" class="tab">
    <form id="assessment_document" action="scripts/sales/add_assessment.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="eval_lead_id" name="lead_id" value="<?= $_GET['id']; ?>">
        <input type="hidden" id="user_id" name="user_id" value="">
        <div class="row">
            <div class="col-12">
                <div class="form-heading">
                    <strong>Información de valoración </strong>
                    <button id="editAssessmentForm" style="margin-bottom: 25px;" type="button" class="btn btn-light position-relative">
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            Editar
                        </span>
                        <i style="cursor:pointer;" class="fa fa-pencil"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <label>Fecha de la valoración <span class="login-danger">*</span></label>
                    <input class="form-control" type="date" name="assessment_date" id="e_assessment_date" required>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <label>Nombre (s)<span class="login-danger">*</span></label>
                    <input class="form-control" type="text" name="client_firstname" id="e_client_firstname" required>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <label>Apellido (s)<span class="login-danger">*</span></label>
                    <input class="form-control" type="text" name="client_lastname" id="e_client_lastname" required>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="input-block local-forms">
                    <label>Fecha probable de injerto:</label>
                    <input type="date" id="e_procedure_date" name="e_procedure_date" class="form-control" required>
                    <label class="no-style">
                        <input type="checkbox" name="open_date" id="open_date" value="1">Fecha abierta</label>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Foto del diseño (JPG) <span class="login-danger">*</span></label>
                    <input class="form-control" type="file" id="photo" name="photo" accept="image/jpeg" required>
                    <input type="checkbox" id="no_photo" name="no_photo"> Sin fotografía
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Tipo de injerto:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="procedure_type" id="e_procedure_type" required>
                        <option value="" selected disabled>Selecciona</option>
                        <option value="Capilar">Capilar</option>
                        <option value="Barba">Barba</option>
                        <option value="Ambos">Ambos</option>
                        <option value="Tratamientos">Tratamientos</option>
                        <option value="Micro">Micro</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>¿Quién realizó la valoración?<span class="login-danger">*</span></label>
                    <select class="form-control select" name="assessment_employee" id="e_closer" required>
                        <option value="" selected disabled>Selecciona</option>
                        <option value="Dr. Alejandro Santana">Dr. Alejandro Santana</option>
                        <option value="Dra. Amairani Romero">Dra. Amairani Romero</option>
                        <option value="Dra. Oriana Aguilar">Dra. Oriana Aguilar</option>
                        <option value="Dra. Monserrat Mata">Dra. Monserrat Mata</option>
                        <option value="Marisol Olmos">Marisol Olmos</option>
                        <option value="Adriana Silva">Adriana Silva</option>
                        <option value="Janeth Ruiz">Janeth Ruiz</option>
                        <option value="Dra. Samanta Soto">Dra. Samanta Soto</option>
                        <option value="Dr. Luis Andres Peña Melendez">Dr. Luis Andres Peña Melendez</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>¿Por qué medio nos conoció? </label>
                    <div class="time-icon">
                        <label>Origen:<span class="login-danger">*</span></label>
                        <select class="form-control select" name="first_meet_type" id="e_first_meet_type" required>
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
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>Fue valorado en:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="clinic" id="e_clinic" required>
                        <option value="" selected disabled>Selecciona</option>
                        <option value="Queretaro">Queretaro</option>
                        <option value="Pedregal">Pedregal</option>
                        <option value="Santa Fe">Santa Fe</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-block local-forms">
                    <label>La valoración fue:<span class="login-danger">*</span></label>
                    <select class="form-control select" name="assessment_type" id="e_assessment_type" required>
                        <option value="" selected disabled>Selecciona</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Virtual">Virtual</option>
                        <option value="Virtual y después presencial">Virtual y después presencial</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-12">
                <div class="input-block local-forms">
                    <label>¿Qué se le ofreció al cliente?<span class="login-danger">*</span></label>
                    <textarea class="form-control" name="description" id="e_description" rows="3" cols="30" required></textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="doctor-submit text-end">
                    <button type="submit" class="btn btn-primary submit-form me-2">Generar Hoja</button>
                </div>
            </div>
        </div>
    </form>
    <div class="row" id="divPdfAssessment" style="display:none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <iframe id="pdfViewer" src="" width="100%" height="1000px" style="border: none;"></iframe>
                </div>
                <a target="_blank" href="" id="pdfInvoiceDownloadAssesment" download>Descargar</a>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('no_photo').checked = false;
    var photoInput = document.getElementById('photo');
    photoInput.disabled = false;
   
    


    document.getElementById('no_photo').addEventListener('change', function() {
        photoInput.removeAttribute('required');
     });



   
</script>
