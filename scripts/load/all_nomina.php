<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../common/connection_db.php';

$data_array = array();
$user_clinic = 5;
// SQL para obtener los datos
$sql = "SELECT num_progresivo, cuenta, importe, nombre FROM ad_nomina ORDER BY num_progresivo ASC;";

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {

    $data_array[] = array(
        $data->num_progresivo,
        $data->cuenta,
        $data->importe,
        $data->nombre
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
