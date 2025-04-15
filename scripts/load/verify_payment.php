<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
session_start();

require_once "../connection_db.php";

try {
    $lead_id = $_POST['lead_id'];
    $type = $_POST['type'];

    $sql = "SELECT id FROM sa_info_payment_px WHERE type = '$type' AND lead_id = $lead_id AND status = 1;";
    $query = mysqli_query($conn, $sql);

    if (!$query) {
        throw new Exception("Error: " . mysqli_error($conn));
    }

    $exist = (mysqli_num_rows($query) > 0) ? true : false;
    $message = ($exist) ? "Ya existe un anticipo. Revísalo en el Historial de Transacciones" : '';
    echo json_encode([
        "success" => true,
        "exist" => $exist,
        "message" => $message
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "exist" => false,
        "message" => false,
        "error" => $e->getMessage(),
    ]);
}
