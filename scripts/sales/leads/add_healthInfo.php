<?php
// Mostrar todos los errores para depuración
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

// Conexión a la base de datos
require_once __DIR__ . "/../../common/connection_db.php";

// Inicializar la respuesta
$response = [
    'success' => false,
    'message' => ''
];

// Verificar si se ha enviado el formulario con los datos requeridos
if (isset($_POST['lead_id']) && isset($_POST['health_conditions'])) {
    // Recibir los datos del formulario
    $lead_id = $_POST['lead_id'];
    $enfermedades = $_POST['health_conditions'];

    // Preparar la consulta para actualizar los datos en la tabla sa_leads_assessment
    $stmt = $conn->prepare("UPDATE sa_leads_assessment SET enfermedades = ? WHERE lead_id = ?");

    // Verificar si la consulta se preparó correctamente
    if ($stmt) {
        // Enlazar los parámetros con los valores recibidos
        $stmt->bind_param("si", $enfermedades, $lead_id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = "Datos actualizados correctamente.";
            } else {
                $response['message'] = "No se encontró el lead_id especificado o no hubo cambios.";
            }
        } else {
            $response['message'] = "Error al ejecutar la consulta: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        $response['message'] = "Error al preparar la consulta: " . $conn->error;
    }
} else {
    $response['message'] = "Los datos no fueron enviados correctamente.";
}

// Establecer el encabezado de tipo de contenido a JSON
header('Content-Type: application/json');

// Retornar la respuesta en formato JSON
echo json_encode($response);

// Cerrar la conexión
$conn->close();
?>
