<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require '../connection_db.php';

$user_id = $_POST['user_id'];


$user_data_array = array();
// SQL para obtener los datos
$sql_row = "SELECT id, nombre, usuario, DATE_FORMAT(ultimo_acceso, '%d-%m-%Y %H:%i:%s') AS ultimo_acceso FROM usuarios WHERE id = ?;";

// Ejeuctar el SQL
$query = $conn->prepare($sql_row);
$query->bind_param("i", $user_id);
if ($query->execute()) {
    $result = $query->get_result();
    $user_data = $result->fetch_assoc();

    $user_permissions = array();

    $sql_row_permissions = "SELECT permission_id FROM u_permission_assignment WHERE user_id = ?";

    $query_permissions = $conn->prepare($sql_row_permissions);
    $query_permissions->bind_param("i", $user_id);
    if ($query_permissions->execute()) {
        $result_permissions = $query_permissions->get_result();
        while ($row = $result_permissions->fetch_assoc()) {
            $user_permissions[] = $row['permission_id'];
        }
    }

    $user_data_array = array_merge(
        $user_data,
        array("user_permissions" => $user_permissions)
    );

    echo json_encode($user_data_array);
}
