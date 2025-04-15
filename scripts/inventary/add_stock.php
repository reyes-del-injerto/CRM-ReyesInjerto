<?php
require '../common/connection_db.php'; // Incluir la conexión

$response = ['status' => 'error', 'message' => '', 'debug' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movementType = $_POST['movement_type'] ?? null;

    // Validar que el tipo de movimiento esté presente
    if ($movementType === null) {
        $response['message'] = 'Tipo de movimiento no proporcionado.';
        echo json_encode($response);
        exit;
    }

    if ($movementType === 'entrada') {
        // Procesar entrada
        $itemName = $_POST['item_name'] ?? null;
        $itemDescription = $_POST['item_description'] ?? null;
        $stock = $_POST['stock'] ?? null;
        $itemLocation = $_POST['item_location'] ?? null;
        $itemCategory = $_POST['category'] ?? null;
        $expiryDate = $_POST['expiry_date'] ?? null;
        $minimumValue = $_POST['minimum_value'] ?? null;
        $itemUnit = $_POST['unit'] ?? null; // Campo opcional
        $quantityPackage = $_POST['quantity_package'] ?? null; // Campo opcional
        $clinic = $_POST['clinic'] ?? null; // Campo opcional

        // Validación de campos obligatorios
        if ($itemName === null || $stock === null || $itemLocation === null || $itemCategory === null  || $clinic === null) {
            $response['message'] = 'Faltan datos obligatorios.';
            $response['debug'] = "Campos recibidos: itemName=$itemName, clinic=$clinic, stock=$stock,  itemLocation=$itemLocation, itemCategory=$itemCategory";
            echo json_encode($response);
            exit;
        }

    

        // Verificar si el producto ya existe
        $stmt = $conn->prepare("SELECT id, stock FROM ad_inventory_items WHERE name = ?");
        $stmt->bind_param("s", $itemName);
        if (!$stmt->execute()) {
            $response['message'] = 'Error al consultar el producto existente.';
            $response['debug'] = $stmt->error;
            echo json_encode($response);
            exit;
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Producto existe, actualizar stock
            $row = $result->fetch_assoc();
            $itemId = $row['id'];
            $newStock = $row['stock'] + $stock;

            // Actualizar stock
            $stmt = $conn->prepare("UPDATE ad_inventory_items SET description = ?, location = ?, category = ?, stock = ?, quantity_package = ?, unit = ? WHERE id = ?");
            $stmt->bind_param("ssssisi", $itemDescription, $itemLocation, $itemCategory, $newStock, $quantityPackage, $itemUnit, $itemId);

            if (!$stmt->execute()) {
                $response['message'] = 'Error al actualizar el producto en el inventario.';
                $response['debug'] = $stmt->error;
            }
        } else {
            // Producto no existe, insertar nuevo producto
            $stmt = $conn->prepare("INSERT INTO ad_inventory_items (name, description, location, category, stock,quantity_package, unit, minimum_required,clinic) VALUES (?, ?, ?, ?, ?, ?,?,?,?)");
            $stmt->bind_param("ssssdisis", $itemName, $itemDescription, $itemLocation, $itemCategory, $stock, $quantityPackage, $itemUnit, $minimumValue,$clinic);

            if (!$stmt->execute()) {
                $response['message'] = 'Error al agregar el producto al inventario.';
                $response['debug'] = $stmt->error;
                echo json_encode($response);
                exit;
            }
            $itemId = $conn->insert_id;
        }

        // Insertar en ad_medicine_batches
        $stmt = $conn->prepare("INSERT INTO ad_medicine_batches (item_id, batch_number, expiry_date, quantity) VALUES (?, ?, ?, ?)");
        $batchNumber = 'Batch-001'; // Ajusta según sea necesario
        $expiryDate = $expiryDate ?: null; // Si no hay fecha, se guarda como NULL
        $stmt->bind_param("issi", $itemId, $batchNumber, $expiryDate, $stock);

        if (!$stmt->execute()) {
            $response['message'] = 'Error al registrar la fecha de caducidad.';
            $response['debug'] = $stmt->error;
        } else {
            $batchId = $conn->insert_id;

            // Insertar en ad_inventory_movements
            $deliveredBy = 'Sistema';
            $stmt = $conn->prepare("INSERT INTO ad_inventory_movements (item_id, batch_id, movement_type, quantity, movement_date, delivered_by, location) VALUES (?, ?, 'Ingreso', ?, NOW(), ?, ?)");
            $stmt->bind_param("iidss", $itemId, $batchId, $stock, $deliveredBy, $itemLocation);

            if (!$stmt->execute()) {
                $response['message'] = 'Error al registrar movimiento de entrada.';
                $response['debug'] = $stmt->error;
            } else {
                $response['status'] = 'success';
            }
        }
    } elseif ($movementType === 'salida') {
        // Procesar salida
        $productId = $_POST['product_id'] ?? null;
        $outputQuantity = $_POST['output_quantity'] ?? null;
        $outputDate = $_POST['output_date'] ?? null;

        // Validación de campos obligatorios
        if ($productId === null || $outputQuantity === null || $outputDate === null) {
            $response['message'] = 'Faltan datos obligatorios.';
            $response['debug'] = "Campos recibidos: productId=$productId, outputQuantity=$outputQuantity, outputDate=$outputDate";
            echo json_encode($response);
            exit;
        }

        // Insertar en ad_inventory_movements
        $deliveredBy = 'Sistema';
        $stmt = $conn->prepare("INSERT INTO ad_inventory_movements (item_id, movement_type, quantity, movement_date, delivered_by, location) VALUES (?, 'Salida', ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $productId, $outputQuantity, $outputDate, $deliveredBy, $_POST['item_location']);

        if ($stmt->execute()) {
            $response['status'] = 'success';
        } else {
            $response['message'] = 'Error al registrar movimiento de salida.';
            $response['debug'] = $stmt->error;
        }
    } else {
        $response['message'] = 'Tipo de movimiento no válido.';
        $response['debug'] = "Tipo recibido: $movementType";
    }
} else {
    $response['message'] = 'Método no permitido.';
    $response['debug'] = "Método: {$_SERVER['REQUEST_METHOD']}";
}

// Cerrar la conexión
$conn->close();

// Retornar respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
