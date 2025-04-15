<?php
// Incluir la conexión a la base de datos
require '../common/connection_db.php';

// Obtener la categoría y clínica desde los parámetros GET
$category = isset($_GET['category']) ? $_GET['category'] : null;
$clinic = isset($_GET['clinic']) ? $_GET['clinic'] : null; // Obtener la clínica

try {
    // Validar los parámetros
    if (is_null($category)) {
        throw new Exception('La categoría es requerida.');
    }
    if (is_null($clinic)) {
        throw new Exception('La clínica es requerida.');
    }

    // Preparar la consulta filtrando por categoría y clínica
    if ($category === 'All' && $clinic === 'All') {
        $stmt = $conn->prepare("
            SELECT 
                i.id, 
                i.name, 
                i.description, 
                i.minimum_required, 
                i.stock, 
                i.quantity_package, 
                i.unit, 
                i.clinic, 
                COALESCE(SUM(m.quantity), 0) AS total_movement 
            FROM 
                ad_inventory_items i
            LEFT JOIN 
                ad_inventory_movements m ON i.id = m.item_id
            GROUP BY 
                i.id
        ");
    } elseif ($category === 'All') {
        $stmt = $conn->prepare("
            SELECT 
                i.id, 
                i.name, 
                i.description, 
                i.minimum_required, 
                i.stock, 
                i.quantity_package, 
                i.unit, 
                i.clinic, 
                COALESCE(SUM(m.quantity), 0) AS total_movement 
            FROM 
                ad_inventory_items i
            LEFT JOIN 
                ad_inventory_movements m ON i.id = m.item_id
            WHERE 
                i.clinic = ?
            GROUP BY 
                i.id
        ");
        $stmt->bind_param("s", $clinic);
    } elseif ($clinic === 'All') {
        $stmt = $conn->prepare("
            SELECT 
                i.id, 
                i.name, 
                i.description, 
                i.minimum_required, 
                i.stock, 
                i.quantity_package, 
                i.unit, 
                i.clinic, 
                COALESCE(SUM(m.quantity), 0) AS total_movement 
            FROM 
                ad_inventory_items i
            LEFT JOIN 
                ad_inventory_movements m ON i.id = m.item_id
            WHERE 
                i.category = ?
            GROUP BY 
                i.id
        ");
        $stmt->bind_param("s", $category);
    } else {
        $stmt = $conn->prepare("
            SELECT 
                i.id, 
                i.name, 
                i.description, 
                i.minimum_required, 
                i.stock, 
                i.quantity_package, 
                i.unit, 
                i.clinic, 
                COALESCE(SUM(m.quantity), 0) AS total_movement 
            FROM 
                ad_inventory_items i
            LEFT JOIN 
                ad_inventory_movements m ON i.id = m.item_id
            WHERE 
                i.category = ? AND i.clinic = ?
            GROUP BY 
                i.id
        ");
        $stmt->bind_param("ss", $category, $clinic);
    }

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception('Error en la ejecución de la consulta: ' . $stmt->error);
    }

    // Obtener resultados
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);

    // Calcular pendiente por comprar
    foreach ($items as &$item) {
        $stock = $item['stock'];
        $minimumRequired = $item['minimum_required'];
        $item['pending_to_buy'] = ($minimumRequired > $stock) ? ($minimumRequired - $stock) : 0;
    }

    // Retornar los ítems en formato JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'data' => $items]);
} catch (Exception $e) {
    // Manejo de errores
    http_response_code(500); // Código de error interno del servidor
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    error_log($e->getMessage()); // Registrar el error en el log del servidor
} finally {
    // Cerrar la declaración
    if (isset($stmt)) {
        $stmt->close();
    }

    // Cerrar la conexión
    if (isset($conn)) {
        $conn->close();
    }
}
