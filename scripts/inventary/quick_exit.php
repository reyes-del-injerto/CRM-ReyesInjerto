<?php
require '../common/connection_db.php'; // Incluir la conexión

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receivedBy = $_POST['received_by'] ?? null;
    $outputDate = $_POST['output_date'] ?? null;
    $movementType = $_POST['type'] ?? null; // 'capilar' o 'barba'
    $clinic = $_POST['clinic'] ?? null; // Parámetro clinic

    $missingFields = [];

    if ($receivedBy === null) $missingFields[] = 'Recibido por';
    if ($outputDate === null) $missingFields[] = 'Fecha';
    if ($movementType === null) $missingFields[] = 'Tipo de movimiento';
    if ($clinic === null) $missingFields[] = 'Clínica';

    if (!empty($missingFields)) {
        $response['message'] = 'Faltan datos obligatorios: ' . implode(', ', $missingFields) . '.';
        echo json_encode($response);
        exit;
    }

    // Definir productos y cantidades a descontar según el tipo y clínica
    if ($clinic === 'Santa fe') {
        if ($movementType === 'capilar') {
            $productsToDiscount = [1698, 1503, 1493, 1494, 1495, 1476, 1475, 1477, 1506, 1505, 1508, 1519, 1529, 1528, 1535, 1509, 1732, 1717, 1715, 1735, 1727, 1733, 1751, 1740];
            $quantitiesToDiscount = [2, 1, 16, 6, 6, 4, 1, 1, 4, 2, 1, 1, 1, 2, 6, 1, 2, 1, 1, 1, 1, 1, 1, 1]; 
        } else if ($movementType === 'barba') {
            $productsToDiscount = [1698, 1503, 1493, 1494, 1495, 1476, 1475, 1477, 1506, 1505, 1508, 1519, 1529, 1528, 1535, 1509, 1732, 1717, 1715, 1735, 1727, 1733, 1751, 1740];
            $quantitiesToDiscount = [2, 1, 18, 6, 6, 5, 1, 1, 4, 2, 1, 2, 1, 3, 8, 2, 2, 1, 1, 1, 1, 1, 1, 1]; 
        } else {
            $response['message'] = 'Tipo de movimiento no válido.';
            echo json_encode($response);
            exit;
        }
    } elseif ($clinic === 'Querétaro') {
        // En Querétaro, los productos se descuentan desde el ID 1
        $productsToDiscount = range(1, 24); // IDs del 1 al 24 AJUSTAR CON LOS ID DE NUEVOS PRODUCTOS
        $quantitiesToDiscount = array_fill(0, 24, 1); // Cantidades a descontar para cada producto
    } else {
        $response['message'] = 'Clínica no válida.';
        echo json_encode($response);
        exit;
    }

    // Array para almacenar la información de los productos antes de descontar
    $productsInfo = [];

    foreach ($productsToDiscount as $index => $productId) {
        $quantityToDiscount = $quantitiesToDiscount[$index];

        // Obtener el stock actual del producto
        $stmt = $conn->prepare("SELECT stock FROM ad_inventory_items WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentStock = $row['stock'];

            // Guardar la información del producto antes de descontar
            $productsInfo[] = [
                'id' => $productId,
                'quantity_to_discount' => $quantityToDiscount,
                'current_stock' => $currentStock
            ];

            // Verificar si hay suficiente stock para descontar la cantidad deseada
            if ($currentStock < $quantityToDiscount) {
                $response['message'] = 'No hay suficiente stock para el producto ID ' . $productId;
                echo json_encode($response);
                exit;
            }
        } else {
            $response['message'] = 'Producto no encontrado con ID ' . $productId;
            echo json_encode($response);
            exit;
        }
    }

    // Descontar el stock y registrar movimientos
    foreach ($productsToDiscount as $index => $productId) {
        $quantityToDiscount = $quantitiesToDiscount[$index];
        $currentStock = $productsInfo[$index]['current_stock'];

        $newStock = $currentStock - $quantityToDiscount;

        // Actualizar stock
        $updateStmt = $conn->prepare("UPDATE ad_inventory_items SET stock = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newStock, $productId);
        if (!$updateStmt->execute()) {
            $response['message'] = 'Error al actualizar el stock del producto ID ' . $productId;
            echo json_encode($response);
            exit;
        }

        // Registrar movimiento en la tabla de movimientos
        $deliveredBy = 'Sistema';
        $movementStmt = $conn->prepare("INSERT INTO ad_inventory_movements (item_id, movement_type, quantity, movement_date, delivered_by, received_by) VALUES (?, 'Salida', ?, ?, ?, ?)");
        $movementStmt->bind_param("iisss", $productId, $quantityToDiscount, $outputDate, $deliveredBy, $receivedBy);

        if (!$movementStmt->execute()) {
            $response['message'] = 'Error al registrar el movimiento para el producto ID ' . $productId;
            echo json_encode($response);
            exit;
        }
    }

    $response['status'] = 'success';
} else {
    $response['message'] = 'Método no permitido.';
}

// Cerrar la conexión
$conn->close();

// Retornar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
