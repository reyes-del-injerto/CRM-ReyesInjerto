<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos
require_once "../../scripts/common/connection_db.php";

// Obtener los valores del GET
$category = $_GET['category'] ?? 'All';
$clinic = $_GET['clinic'] ?? 'All'; // Obtener el valor de la clínica

try {
    $data = [];
    $added_ids = [];

    // Consulta SQL
    if ($category === 'All' && $clinic === 'All') {
        // No se filtra por categoría ni por clínica
        $stmt = $conn->prepare("
            SELECT 
                m.id, 
                i.name, 
                i.description, 
                m.movement_date, 
                m.movement_type, 
                m.received_by AS delivered_to, 
                i.clinic, -- Cambiado a 'clinic'
                b.expiry_date,
                m.quantity -- Agregar cantidad
            FROM 
                ad_inventory_movements m
            JOIN 
                ad_inventory_items i ON m.item_id = i.id
            LEFT JOIN 
                ad_medicine_batches b ON m.batch_id = b.id
            ORDER BY 
                m.movement_date DESC -- Ordenar por fecha más reciente
            LIMIT 150
        ");
    } elseif ($category === 'All') {
        // Filtrar solo por clínica
        $stmt = $conn->prepare("
            SELECT 
                m.id, 
                i.name, 
                i.description, 
                m.movement_date, 
                m.movement_type, 
                m.received_by AS delivered_to, 
                i.clinic, -- Cambiado a 'clinic'
                b.expiry_date,
                m.quantity -- Agregar cantidad
            FROM 
                ad_inventory_movements m
            JOIN 
                ad_inventory_items i ON m.item_id = i.id
            LEFT JOIN 
                ad_medicine_batches b ON m.batch_id = b.id
            WHERE 
                i.clinic = ? -- Cambiado a 'clinic'
            ORDER BY 
                m.movement_date DESC -- Ordenar por fecha más reciente
            LIMIT 150
        ");
        $stmt->bind_param("s", $clinic);
    } elseif ($clinic === 'All') {
        // Filtrar solo por categoría
        $stmt = $conn->prepare("
            SELECT 
                m.id, 
                i.name, 
                i.description, 
                m.movement_date, 
                m.movement_type, 
                m.received_by AS delivered_to, 
                i.clinic, -- Cambiado a 'clinic'
                b.expiry_date,
                m.quantity -- Agregar cantidad
            FROM 
                ad_inventory_movements m
            JOIN 
                ad_inventory_items i ON m.item_id = i.id
            LEFT JOIN 
                ad_medicine_batches b ON m.batch_id = b.id
            WHERE 
                i.category = ?
            ORDER BY 
                m.movement_date DESC -- Ordenar por fecha más reciente
            LIMIT 150
        ");
        $stmt->bind_param("s", $category);
    } else {
        // Filtrar por categoría y clínica
        $stmt = $conn->prepare("
            SELECT 
                m.id, 
                i.name, 
                i.description, 
                m.movement_date, 
                m.movement_type, 
                m.received_by AS delivered_to, 
                i.clinic, -- Cambiado a 'clinic'
                b.expiry_date,
                m.quantity -- Agregar cantidad
            FROM 
                ad_inventory_movements m
            JOIN 
                ad_inventory_items i ON m.item_id = i.id
            LEFT JOIN 
                ad_medicine_batches b ON m.batch_id = b.id
            WHERE 
                i.category = ? AND i.clinic = ? -- Cambiado a 'clinic'
            ORDER BY 
                m.movement_date DESC -- Ordenar por fecha más reciente
            LIMIT 150
        ");
        $stmt->bind_param("ss", $category, $clinic);
    }

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception('Error en la ejecución de la consulta: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Solo agregar filas únicas basadas en 'id'
        if (!in_array($row['id'], $added_ids)) {
            $data[] = $row;
            $added_ids[] = $row['id'];
        }
    }

    // Devolver el resultado en formato JSON
    echo json_encode(['status' => 'success', 'data' => array_values($data)]);

} catch (Exception $e) {
    // Manejo de excepciones
    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error: ' . $e->getMessage()]);
} finally {
    // Cerrar la declaración y conexión
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
