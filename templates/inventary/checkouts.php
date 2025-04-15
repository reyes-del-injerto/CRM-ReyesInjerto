<?php
// Mostrar todos los errores (útil para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$clinic = isset($_GET['clinic']) ? $_GET['clinic'] : null;

?>
<div class="contenedor_gral_esp mt-2">
    <h2 class="text-center">Movimientos de Inventario</h2>

    <select id="movementCategorySelect" class="form-select mb-4">
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
            <option value="TIM">TIM</option>


    </select>

    <div class="table-responsive">
        <table id="movementsTable" class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo de Movimiento</th>
                    <th>Cantidad</th> <!-- Nueva columna -->
                    <th>Fecha de Movimiento</th>
                    <th>Entregado a</th>
                    <th>Fecha de Caducidad</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se llenarán aquí mediante DataTables -->
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {

        var url_clinic = "<?php echo $clinic; ?>";
        console.log("clinica getit", url_clinic)


        var movementsTable = $('#movementsTable').DataTable({
            "pageLength": 100, // Mostrar 50 filas por defecto
            "order": [[0, 'desc']] // Ordenar por la primera columna (ID) en orden ascendente
        });

        // Cargar todos los movimientos al inicio
        fetchMovementsByCategory('All', movementsTable,url_clinic);

        // Manejar el cambio del dropdown
        $('#movementCategorySelect').change(function() {
            var category = $(this).val();

            // Limpiar la tabla
            movementsTable.clear().draw();

            // Hacer la petición AJAX solo si hay una categoría seleccionada
            fetchMovementsByCategory(category, movementsTable,url_clinic);
        });

        // Función para realizar la petición AJAX
        function fetchMovementsByCategory(category, movementsTable,clinic) {
            $.ajax({
                url: 'scripts/inventary/get_movements_by_category.php',
                type: 'GET',
                data: {
                    category: category,
                    clinic: clinic
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Llenar la DataTable con los datos recibidos
                        response.data.forEach(function(movement) {
                            movementsTable.row.add([
                                movement.id, // ID
                                movement.name, // Nombre
                                movement.description, // Descripción
                                movement.movement_type, // Tipo de Movimiento
                                movement.quantity + ' piezas', // Cantidad
                                movement.movement_date, // Fecha de Movimiento
                                movement.delivered_to || 'No aplica', // A quién se entregó
                                movement.expiry_date || 'No aplica' // Fecha de Caducidad (si aplica)
                            ]).draw();
                        });
                    } else {
                        // Manejar error
                        alert(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error en la petición:', textStatus, errorThrown);
                    alert('Ocurrió un error al obtener los movimientos.');
                }
            });
        }
    });
</script>
