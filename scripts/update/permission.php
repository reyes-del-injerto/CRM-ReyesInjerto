<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require '../connection_db.php';

$cat = $_POST['cat'];
$name = $_POST['name'];
$description = $_POST['description'];
$clinic = $_POST['clinic'];
$permission_id = $_POST['permission_id'];

switch ($clinic) {
    case "CDMX":
        $clinic = 1;
        break;
    case "Culiacán":
        $clinic = 2;
        break;
    case "Mazatlán":
        $clinic = 3;
        break;
    case "Tijuana":
        $clinic = 4;
        break;
}

$sql_row = "UPDATE u_permissions SET cat = ?, name = ?, description = ?, clinic = ? WHERE id = ?;";
$sql = $conn->prepare($sql_row);
$sql->bind_param("sssii", $cat, $name, $description, $clinic, $permission_id);

if ($sql->execute()) {

    if ($sql->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Datos actualizados correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "No rows were affected."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Query Fail. Contact Admin"]);
}
