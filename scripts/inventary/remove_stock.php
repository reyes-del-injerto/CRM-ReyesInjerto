<?php
require '../common/connection_db.php'; // Incluir la conexión

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movementType = $_POST['movement_type'];

    if ($movementType === 'entrada') {
        // Procesar entrada
        $itemName = $_POST['item_name'] ?? null;
        $itemDescription = $_POST['item_description'] ?? null;
        $stock = $_POST['stock'] ?? null;
        $itemLocation = $_POST['item_location'] ?? null;
        $itemCategory = $_POST['category'] ?? null;
        $expiryDate = $_POST['expiry_date'] ?? null;
        $minimumValue = $_POST['minimum_value'] ?? null; // Valor mínimo

        $missingFields = [];

        if ($itemName === null) $missingFields[] = 'Nombre del producto';
        if ($stock === null) $missingFields[] = 'Cantidad';
        if ($itemLocation === null) $missingFields[] = 'Ubicación';
        if ($itemCategory === null) $missingFields[] = 'Categoría';

        if (!empty($missingFields)) {
            $response['message'] = 'Faltan datos obligatorios: ' . implode(', ', $missingFields) . '.';
            echo json_encode($response);
            exit;
        }

        // Verificar si el producto ya existe
        $stmt = $conn->prepare("SELECT id, stock FROM ad_inventory_items WHERE name = ?");
        $stmt->bind_param("s", $itemName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Producto existe, actualizar stock
            $row = $result->fetch_assoc();
            $itemId = $row['id'];
            $newStock = $row['stock'] + $stock;

            $stmt = $conn->prepare("UPDATE ad_inventory_items SET description = ?, location = ?, category = ?, stock = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $itemDescription, $itemLocation, $itemCategory, $newStock, $itemId);

            if ($stmt->execute()) {
                $response['status'] = 'success';
            } else {
                $response['message'] = 'Error al actualizar el producto en el inventario.';
            }
        } else {
            // Producto no existe, insertar nuevo producto
            $stmt = $conn->prepare("INSERT INTO ad_inventory_items (name, description, location, category, stock, minimum_required) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $itemName, $itemDescription, $itemLocation, $itemCategory, $stock, $minimumValue); // Incluir minimum_value

            if ($stmt->execute()) {
                $itemId = $conn->insert_id;

                
                
            } else {
                $response['message'] = 'Error al agregar el producto al inventario.';
            }
        }
    } elseif ($movementType === 'salida') {
        // Procesar salida
        $productId = $_POST['product_id'] ?? null;
        $outputQuantity = $_POST['output_quantity'] ?? null;
        $outputDate = $_POST['output_date'] ?? null;
        $receivedBy = $_POST['received_by'] ?? null; // Obtener quien recibe el producto

        $missingFields = [];

        if ($productId === null) $missingFields[] = 'ID del producto';
        if ($outputQuantity === null) $missingFields[] = 'Cantidad a salir';
        if ($outputDate === null) $missingFields[] = 'Fecha de salida';
        if ($receivedBy === null) $missingFields[] = 'Recibido por';

        if (!empty($missingFields)) {
            $response['message'] = 'Faltan datos obligatorios: ' . implode(', ', $missingFields) . '.';
            echo json_encode($response);
            exit;
        }

        // Verificar si hay suficiente stock
        $stmt = $conn->prepare("SELECT stock FROM ad_inventory_items WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['stock'] >= $outputQuantity) {
                // Actualizar stock
                $newStock = $row['stock'] - $outputQuantity;

                $stmt = $conn->prepare("UPDATE ad_inventory_items SET stock = ? WHERE id = ?");
                $stmt->bind_param("di", $newStock, $productId);
                if ($stmt->execute()) {
                    // Insertar en ad_inventory_movements
                    $deliveredBy = 'Sistema'; // Definido como una variable
                    $stmt = $conn->prepare("INSERT INTO ad_inventory_movements (item_id, movement_type, quantity, movement_date, delivered_by, location, received_by) VALUES (?, 'Salida', ?, ?, ?, ?, ?)");
                    $stmt->bind_param("idssss", $productId, $outputQuantity, $outputDate, $deliveredBy, $_POST['item_location'], $receivedBy);

                    if ($stmt->execute()) {
                        $response['status'] = 'success';
                    } else {
                        $response['message'] = 'Error al registrar movimiento de salida.';
                    }
                } else {
                    $response['message'] = 'Error al actualizar el stock.';
                }
            } else {
                $response['message'] = 'No hay suficiente stock para realizar esta salida.';
            }
        } else {
            $response['message'] = 'Producto no encontrado.';
        }
    } else {
        $response['message'] = 'Tipo de movimiento no válido.';
    }
} else {
    $response['message'] = 'Método no permitido.';
}

// Cerrar la conexión
$conn->close();

// Retornar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
