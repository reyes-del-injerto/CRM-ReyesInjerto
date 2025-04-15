<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

/*
name=Kathhia%20Sharbely&num_med_record=18


 */

try {
    $name = $_POST['name'];
    $num_med_record = $_POST['num_med_record'];
    


    $sql_row = "UPDATE enf_treatments SET name = ?  WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("si", $name, $num_med_record);

    if (!$sql->execute()) {
        throw new Exception("Error al actualizar la información. Contacta al administrador");
    }

    $success = true;
    $message = "Transaccion Actulizada . Gracias! 🦁";
} catch (Exception $e) {
    $success = false;
    $message = "Error:" . $e->getMessage();
}

$conn->close();

$response = ["success" => $success, "message" => $message];
echo json_encode($response);
