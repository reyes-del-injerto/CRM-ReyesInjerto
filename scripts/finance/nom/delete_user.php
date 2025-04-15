<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../../common/connection_db.php';

// Array para la respuesta
$response = array();

// Verificar si se recibieron los datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir el ID del usuario desde la solicitud
    $usuario_id = isset($_POST['id']) ? $_POST['id'] : null;

    // Validar que el ID no esté vacío
    if ($usuario_id) {
        // SQL para eliminar el usuario
        $sql_delete = "DELETE FROM ad_nomina WHERE id = ?";
        
        // Preparar la sentencia
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("i", $usuario_id); // Suponiendo que el ID es un entero

        // Ejecutar la eliminación
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = ["success" => true, "message" => "Usuario eliminado correctamente."];
            } else {
                $response = ["success" => false, "message" => "No se encontró un usuario con ese ID."];
            }
        } else {
            $response = ["success" => false, "message" => "Error al eliminar el usuario."];
        }

        $stmt->close();
    } else {
        $response = ["success" => false, "message" => "El ID del usuario es requerido."];
    }
} else {
    $response = ["success" => false, "message" => "Método de solicitud no válido."];
}

// Enviar la respuesta como JSON
echo json_encode($response);

$conn->close();
?>
