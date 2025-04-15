<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../../common/connection_db.php';

$data_array = [];

// Verificar si se recibieron los datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los parámetros desde la solicitud
    $num_progresivo = isset($_POST['num_progresivo']) ? $_POST['num_progresivo'] : null;
    $cuenta = isset($_POST['cuenta']) ? $_POST['cuenta'] : null;
    $importe = isset($_POST['importe']) ? $_POST['importe'] : null;
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $clinic = isset($_POST['clinic']) ? $_POST['clinic'] : null;

    // Validar que el campo clinic no esté vacío antes de eliminar
    if ($clinic) {
        deletePreviousData($conn, $clinic);
    }

    function deletePreviousData($conn, $clinic)
    {
        $sql_row = "DELETE FROM ad_nomina WHERE clinic = ?"; // Eliminar solo donde el campo 'clinic' coincide
        $stmt = $conn->prepare($sql_row);
        $stmt->bind_param("s", $clinic);
        
        if ($stmt->execute()) {
            return true;
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar los datos previos."]);
            exit;
        }

        $stmt->close();
    }

    // Validar que los datos no estén vacíos antes de insertar
    if ($num_progresivo && $cuenta && $importe && $nombre && $clinic) {
        // SQL para insertar los datos en la tabla ad_nomina
        $sql_insert = "INSERT INTO ad_nomina (num_progresivo, cuenta, importe, nombre, clinic) VALUES (?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("isdss", $num_progresivo, $cuenta, $importe, $nombre, $clinic);

        // Ejecutar la inserción
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Datos insertados correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al insertar los datos."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Todos los campos son requeridos."]);
    }
}

$conn->close();
