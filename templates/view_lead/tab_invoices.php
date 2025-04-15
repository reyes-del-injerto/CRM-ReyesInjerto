<!-- TAB: Generar Recibo -->
<div id="tab-invoices" class="tab" style="display:none;">
    <div class="row">
        <div class="col-12 col-md-2">
            <div class="widget settings-menu setting-list-menu">
                <ul>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="anticipo">
                            <i class="fa fa-dollar-sign me-2"></i> <span> Anticipo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="abono">
                            <i class="fa fa-dollar-sign me-2"></i> <span>Abono</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="liquidacion">
                            <i class="fa fa-dollar-sign me-2"></i> <span>Liquidación</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="producto">
                            <i class="fa fa-bottle-water me-2"></i> <span>Productos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-tab="tratamiento">
                            <i class="fas fa-syringe me-2"></i> <span>Tratamiento</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="card" id="formContainer">
            </div>
        </div>
    </div>
    <div class="row" id="divInvoicePdf" style="display:none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <iframe id="pdfInvoiceViewer" src="" width="100%" height="1000px" style="border: none;"></iframe>
                   
                </div>
                <a target="_blank" href="" id="pdfInvoiceDownload" download>Descargar</a>
            </div>
        </div>
    </div>
</div>