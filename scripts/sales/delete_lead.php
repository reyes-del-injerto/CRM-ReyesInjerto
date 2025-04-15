<?php
// Configuración para mostrar errores y ajustar la zona horaria
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

// Configurar el header para JSON
header('Content-Type: application/json');

// Iniciar sesión y cargar la conexión a la base de datos
session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
    // Obtener el ID del lead desde la solicitud POST
    $lead_id = $_POST['lead_id'];

    // Verificar si se recibió el ID del lead
    if (!$lead_id) {
        throw new Exception('ID de lead no proporcionado');
    }

    // Preparar la consulta SQL para eliminar el lead
    $stmt = $conn->prepare("DELETE FROM sa_leads WHERE id = ?");
    $stmt->bind_param("i", $lead_id); // Asignar el ID del lead a la consulta

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si se ejecutó correctamente, preparar la respuesta
        $status = 'success';
        $message = "Lead eliminado correctamente";
    } else {
        // Si hay un error en la ejecución
        throw new Exception("No se pudo eliminar el lead.");
    }

    // Cerrar la declaración preparada
    $stmt->close();

} catch (Exception $e) {
    // En caso de cualquier excepción, capturar el error y enviar la respuesta
    $status = 'error';
    $message = "Error: " . $e->getMessage();
}

// Cerrar la conexión a la base de datos
$conn->close();

// Devolver la respuesta en formato JSON
echo json_encode(['status' => $status, 'message' => $message]);
