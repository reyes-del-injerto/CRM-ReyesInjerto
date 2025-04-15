<?php
require '../common/connection_db.php'; // Incluir la conexión a la base de datos

header('Content-Type: application/json');

// Inicializamos un array para el resultado
$response = [
    'status' => 'error',
    'message' => '',
    'details' => []
];

// Verificar si todos los parámetros están presentes
if (!isset($_POST['product'], $_POST['current_location'], $_POST['current_quantity'], $_POST['destination'], $_POST['quantity_to_move'])) {
    $response['message'] = 'Error: Faltan parámetros obligatorios.';
    echo json_encode($response);
    exit();
}

// Obtener los parámetros del formulario
$product_id = $_POST['product'];
$current_location = $_POST['current_location'];
$current_quantity = (int)$_POST['current_quantity'];
$destination = $_POST['destination'];
$quantity_to_move = (int)$_POST['quantity_to_move'];

// Obtener el nombre del producto a partir de su ID
$stmt_product = $conn->prepare("SELECT name FROM ad_inventory_items WHERE id = ?");
$stmt_product->bind_param('i', $product_id);
$stmt_product->execute();
$product_result = $stmt_product->get_result();
$product_data = $product_result->fetch_assoc();
$product_name = $product_data['name'];

// Agregar detalles al response
$response['details'] = [
    'product_id' => $product_id,
    'product_name' => $product_name,
    'current_location' => $current_location,
    'current_quantity' => $current_quantity,
    'destination' => $destination,
    'quantity_to_move' => $quantity_to_move
];

// Validar si la cantidad a mover no excede la cantidad actual
if ($quantity_to_move > $current_quantity) {
    $response['message'] = 'Error: La cantidad a mover excede la cantidad disponible en la ubicación actual.';
    echo json_encode($response);
    exit();
}

try {
    // Iniciar la transacción
    $conn->begin_transaction();

    // Restar la cantidad de la ubicación actual
    $stmt = $conn->prepare("UPDATE ad_inventory_items SET stock = stock - ? WHERE id = ? AND location = ? AND stock >= ?");
    $stmt->bind_param('iisi', $quantity_to_move, $product_id, $current_location, $quantity_to_move);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Comprobar si ya existe un producto con el mismo nombre en la nueva ubicación (excluyendo el ID del producto actual)
            $stmt_check = $conn->prepare("SELECT id, stock FROM ad_inventory_items WHERE name = ? AND location = ? AND id != ?");
            $stmt_check->bind_param('ssi', $product_name, $destination, $product_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $existing_product = $result->fetch_assoc();

            if ($existing_product) {
                // Si existe un producto con el mismo nombre en la nueva ubicación, sumamos la cantidad a esa ubicación
                $stmt_update = $conn->prepare("UPDATE ad_inventory_items SET stock = stock + ? WHERE id = ?");
                $stmt_update->bind_param('ii', $quantity_to_move, $existing_product['id']);
                $stmt_update->execute();
            } else {
                // Si no existe un producto con ese nombre, creamos un nuevo registro con la nueva ubicación
                $stmt_insert = $conn->prepare("
                    INSERT INTO ad_inventory_items (name, description, category, stock, minimum_required, location, has_expiry) 
                    SELECT name, description, category, ?, minimum_required, ?, has_expiry 
                    FROM ad_inventory_items WHERE id = ? AND location = ?");
                $stmt_insert->bind_param('isis', $quantity_to_move, $destination, $product_id, $current_location);
                $stmt_insert->execute();
            }

            // Registro de movimiento de "Salida" en la ubicación actual
            $stmt_movement_out = $conn->prepare("
                INSERT INTO ad_inventory_movements (item_id, movement_type, quantity, movement_date, delivered_by, location) 
                VALUES (?, 'Salida', ?, NOW(), 'Sistema', ?)");
            $stmt_movement_out->bind_param('iis', $product_id, $quantity_to_move, $current_location);
            $stmt_movement_out->execute();

            // Registro de movimiento de "Ingreso" en la nueva ubicación
            $stmt_movement_in = $conn->prepare("
                INSERT INTO ad_inventory_movements (item_id, movement_type, quantity, movement_date, delivered_by, received_by, location) 
                VALUES (?, 'Cambio de ubicaion', ?, NOW(), 'Sistema', '', ?)");
            $stmt_movement_in->bind_param('iis', $product_id, $quantity_to_move, $destination);
            $stmt_movement_in->execute();

            // Confirmar la transacción
            $conn->commit();
            $response['status'] = 'success';
            $response['message'] = 'Ubicación del producto actualizada exitosamente y movimiento registrado.';
        } else {
            throw new Exception('No se pudo restar la cantidad de la ubicación actual. Es posible que no haya suficiente stock.');
        }
    } else {
        throw new Exception('Error al actualizar la ubicación actual.');
    }
} catch (Exception $e) {
    // Revertir cambios si hay error
    $conn->rollback();
    $response['message'] = 'Error: ' . $e->getMessage();
}


// Cerrar los prepared statements
$stmt->close();
$stmt_check->close();

// Enviar la respuesta como JSON
echo json_encode($response);
