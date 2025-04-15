<?php
require '../common/connection_db.php'; // Incluir la conexión

$response = ['status' => 'error', 'data' => []];

// Obtener la clínica del parámetro GET, o establecer 'All' por defecto
$clinic = $_GET['clinic'] ?? 'All';

try {
    // Modificar la consulta para filtrar por clínica
    if ($clinic === 'All') {
        $query = "SELECT id, name, description, location, category, stock, unit, quantity_package FROM ad_inventory_items ORDER BY `ad_inventory_items`.`name` ASC";
    } else {
        $query = "SELECT id, name, description, location, category, stock, unit, quantity_package FROM ad_inventory_items WHERE clinic = ? ORDER BY `ad_inventory_items`.`name` ASC";
    }

    // Preparar la consulta
    $stmt = $conn->prepare($query);

    // Si no es 'All', enlazar el parámetro de clínica
    if ($clinic !== 'All') {
        $stmt->bind_param("s", $clinic);
    }

    // Ejecutar la consulta
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $response['status'] = 'success';
        $response['data'] = $products;
    } else {
        $response['message'] = 'Error al consultar los productos.';
    }

    // Cerrar la declaración
    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Cerrar la conexión
$conn->close();

// Retornar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
