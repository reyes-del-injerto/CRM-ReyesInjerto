<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once '../../common/connection_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $holiday_id = $_POST['holiday_id'];

    $sql = "DELETE FROM ad_holidays WHERE id = $holiday_id;";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Vacaciones eliminadas correctamente']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Error:' . $conn->error]);
    }


    // Cierra la conexión
    $conn->close();
}
