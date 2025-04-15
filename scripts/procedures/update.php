<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
    $procedure_id = $_POST['procedure_id'];
    $room = $_POST['room'];
    $specialist = $_POST['specialist'];
    $notes = $_POST['notes'];

    $sql_row = "UPDATE enf_procedures SET room = ?, specialist = ?, notes = ? WHERE lead_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("issi", $room, $specialist, $notes, $procedure_id);

    if (!$sql->execute()) {
        throw new Exception("Error al actualizar la información. Contacta al administrador");
    }

    $success = true;
    $message = "Procedimiento actualizado. Gracias! 🦁";
} catch (Exception $e) {
    $success = false;
    $message = "Error:" . $e->getMessage();
}

$conn->close();

$response = ["success" => $success, "message" => $message];
echo json_encode($response);
