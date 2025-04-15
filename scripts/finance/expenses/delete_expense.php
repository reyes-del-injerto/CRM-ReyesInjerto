<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../../common/connection_db.php";

// Inicializa variables de respuesta
$success = false;
$message = "";

try {
    // Obtiene los datos JSON de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    $recordId = isset($data['id']) ? $data['id'] : null;
    $typePayment = isset($data['type_payment']) ? $data['type_payment'] : null;

    // Verifica que se haya proporcionado un ID y un tipo de pago válidos
    if ($recordId && $typePayment) {
        // Sanitiza el ID y el tipo de pago
        $recordId = $conn->real_escape_string($recordId);
        $typePayment = $conn->real_escape_string($typePayment);

        // Determina la tabla de la cual eliminar el registro según el tipo de pago
        if ($typePayment === 'payment_treatments') {
            $sql = "DELETE FROM sa_info_payment_treatments WHERE id = '$recordId'";
        } elseif ($typePayment === 'payment_px') {
            $sql = "DELETE FROM sa_info_payment_px WHERE id = '$recordId'";
        } else {
            $message = "Tipo de pago no válido."; // Mensaje si el tipo de pago no es válido
            echo json_encode(['success' => $success, 'message' => $message]);
            exit;
        }

        // Ejecuta la consulta de eliminación
        if ($conn->query($sql) === TRUE) {
            $success = true; // La eliminación fue exitosa
        } else {
            $message = "Error al eliminar el registro: " . $conn->error; // Mensaje de error en caso de fallo
        }
    } else {
        $message = "ID de registro o tipo de pago no proporcionados."; // Mensaje si faltan parámetros
    }
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage(); // Captura errores generales
}

// Envía la respuesta JSON
echo json_encode(['success' => $success, 'message' => $message]);

$conn->close(); // Cierra la conexión a la base de datos
?>
