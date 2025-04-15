<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$data_array = array();
$user_id = 1;
// SQL para obtener los datos

if ($_SESSION['user_department'] == "Gerencia" || $_SESSION['user_department'] == "Recepcion Pedregal") {
    $sql = "SELECT id, total, public_id, DATE_FORMAT(date, '%d/%m/%Y') date, uploaded_by, notes, clinic, approved FROM sa_corte_caja;";
} else if ($_SESSION['user_department'] == "Recepcion Santafe") {
    $sql = "SELECT id, total, public_id, DATE_FORMAT(date, '%d/%m/%Y') date, uploaded_by, notes, clinic, approved FROM sa_corte_caja WHERE clinic = 'Santa Fe';";
}

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {

    switch ($data->approved) {
        case 0:
            $approved = 'En revisión';
            break;
        case 1:
            $approved = 'Aprobado';
            break;
        case 2:
            $approved = 'Rechazado';
            break;
        default:
            $approved = 'Desconocido';
            break;
    }

    $total = "$" . number_format($data->total, 2, ".", ",");

    $tdOptions = "";
    if ($_SESSION['user_department'] == "Gerencia" && $data->approved == 0) {
        $tdOptions .= '<a type="button" href="#" class="btn btn-success approve" data-public-id=' . $data->public_id . '><i class="fa fa-check"></i></a> ';
    }
    $tdOptions .=
        '<a type="button" href="#" class="btn btn-primary load_files" data-public-id=' . $data->public_id . '><i class="fa fa-upload"></i></a>
        <a type="button" href="scripts/download/corte_caja.php?id=' . $data->id . '&filename=' . $data->public_id . '" target="_blank" class="btn btn-success"><i class="fa fa-download"></i> </a>';

    $data_array[] = array(
        $data->public_id,
        $total,
        $data->date,
        $data->notes,
        $approved,
        $data->clinic,
        $tdOptions
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
