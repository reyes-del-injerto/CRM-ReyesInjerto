<?php
// Mostrar todos los errores (útil para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$clinic = isset($_GET['clinic']) ? $_GET['clinic'] : null;

// Incluye la conexión a la base de datos
require_once "../../scripts/common/connection_db.php";
?>

<div class="container mt-2">
    <h2 class="mb-4 text-center">Vista General del Inventario</h2>

    <!-- Select para la categoría -->
    <div class="mb-4">
        <label for="categorySelect" class="form-label">Mostrando categoría:</label>
        <select id="categorySelect" class="form-select">
            <option value="All">Todo</option>
            <option value="Farmacia">Farmacia</option>
            <option value="Lanceta">Lanceta</option>
            <option value="La paz">La paz</option>
            <option value="Imprenta">Imprenta</option>
            <option value="Sams">Sams</option>
            <option value="Amazon">Amazon</option>
            <option value="Office">Office</option>
            <option value="Instituto de tricologia">Instituto de tricologia</option>
            <option value="Turquia">Turquia</option>
            <option value="TIM ">TIM</option>

            <!--  <option value="Medica">Médica</option>
            <option value="Papeleria">Papelería</option>
            <option value="Limpieza">Limpieza</option>
            <option value="Amazon">Amazon</option>
            <option value="Consumibles">Consumibles</option> -->
        </select>
    </div>

    <!-- Select para la ubicación -->
    <!--    <div class="mb-4">
        <label for="locationSelect" class="form-label">Filtrar por ubicación:</label>
        <select id="locationSelect" class="form-select">
            <option value="All">Todas las ubicaciones</option>
            <option value="Bodega">Bodega</option>
            <option value="Casa Dra.">Casa Dra.</option>
        </select>
    </div> -->

    <div class="table-responsive">
        <img src="assets/img/excel.svg" alt="Descargar Excel" class=" descargar-btn" id="excel_cat" style="width: 35px; height: 35px;" data-category="All">

        <table id="itemsTable" class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad Mínima Requerida</th>
                    <th>Stock Actual</th>
                    <th>Pendiente por Comprar</th>
                    <th>Cantidad por Paquete</th> <!-- Nueva columna -->
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se llenarán aquí -->
            </tbody>
        </table>
    </div>
</div>


<script>
    $(document).ready(function() {

        var url_clinic = "<?php echo $clinic; ?>";
        console.log("clinica en overview", url_clinic)


        var table = $('#itemsTable').DataTable({
            "pageLength": 100, // Mostrar 100 filas por defecto
            "order": [
                [5, 'desc']
            ] // Ordenar por la columna "Pendiente por Comprar" en orden descendente
        });

        // Cargar todos los ítems al inicio
        fetchItems('All', 'All', table, url_clinic);

        // Manejar el cambio del dropdown de categoría
        $('#categorySelect').change(function() {
            var category = $(this).val();
            console.log("Categoría seleccionada: ", category)
            var location = $('#locationSelect').val(); // Obtener la ubicación seleccionada
            table.clear().draw();
            fetchItems(category, location, table,url_clinic);
            $('#excel_cat').attr('data-category', category);
        });

        // Manejar el cambio del dropdown de ubicación
        $('#locationSelect').change(function() {
            var category = $('#categorySelect').val(); // Obtener la categoría seleccionada
            var location = $(this).val();
            table.clear().draw();
            fetchItems(category, location, table,url_clinic);
        });

        // Función para realizar la petición AJAX
        function fetchItems(category, location, table,clinica) {
            $.ajax({
                url: 'scripts/inventary/get_items_by_category.php',
                type: 'GET',
                data: {
                    category: category,
                   // location: location, // Incluir ubicación en la petición
                    clinic: clinica
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        response.data.forEach(function(item) {
                            // Comprobar si el campo 'unit' está presente
                            var stockDisplay;
                            if (item.unit && item.unit.trim() !== '') {
                                stockDisplay = item.stock + ' ' + item.unit; // Mostrar con la unidad específica
                            } else {
                                stockDisplay = item.stock + ' piezas'; // Mostrar 'piezas' por defecto
                            }

                            // Agregar nueva columna para 'Cantidad por Paquete'
                            var quantityPackage = item.quantity_package ? item.quantity_package + ' por paquete' : 'No especificado';

                            table.row.add([
                                item.id, // ID
                                item.name, // Nombre
                                item.description, // Descripción
                                item.minimum_required + ' piezas', // Cantidad Mínima Requerida
                                stockDisplay, // Stock Actual con unidad
                                item.pending_to_buy, // Pendiente por Comprar
                                quantityPackage // Cantidad por Paquete
                            ]).draw();
                        });
                    } else {
                        alert(response.message); // Manejar error
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error en la petición:', textStatus, errorThrown);
                    alert('Ocurrió un error al obtener los ítems.');
                }
            });
        }

        $('.descargar-btn').click(function() {
            var category_btn = $('#categorySelect').val();
            console.log("Enviando categoría:", category_btn);
            // Hacer la petición AJAX para obtener el enlace de descarga
            $.ajax({
                url: 'scripts/inventary/get_download_link.php', // Cambia esto al script que genera el enlace de descarga
                method: 'POST',
                data: {
                    category: category_btn,
                },
                success: function(response) {
                    // Abre una nueva pestaña con el enlace obtenido
                    if (response.url) {
                        window.open(response.url, '_blank');
                    } else {
                        console.error('No se pudo obtener el enlace de descarga.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener el enlace de descarga:', error);
                }
            });
        });
    });
</script>