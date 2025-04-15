<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';
// SQL para obtener los datos
$sql = "SELECT id, name, department, allowed_days, used_days FROM ad_employees WHERE status = 1 ORDER BY department ASC;";

$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
    $data_array[] = array(
        $data->id,
        $data->name,
        $data->department,
        $data->allowed_days,
        $data->used_days
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = ["success" => true, "employees" => $data_array];
// crear el JSON apartir de los arrays
echo json_encode($new_array);
