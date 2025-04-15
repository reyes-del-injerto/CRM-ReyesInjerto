<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$data_array = array();
// SQL para obtener los datos
$sql = "SELECT t.id, t.name, t.num_med_record FROM enf_treatments t ";

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
    $link_name = "<a href='view_treatment.php?id={$data->id}' type='button' class='single_procedure'>{$data->name}</a>";

    $data_array[] = array(
        $data->num_med_record,
        $link_name,
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
