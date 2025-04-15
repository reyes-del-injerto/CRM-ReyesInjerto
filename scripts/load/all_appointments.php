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
$sql = "SELECT sig.id, CONCAT(sig.first_name, ' ',sig.last_name) fullname, sig.clinic, sig.status, sipa.advance_amount, sipa.full_amount, DATE_FORMAT(sipr.procedure_date, '%d/%m/%Y') procedure_date, sipr.procedure_type, sipr.seller, sipr.purpose FROM sa_info_general_px AS sig INNER JOIN sa_info_procedure_px AS sipr ON sig.id = sipr.px_general_id INNER JOIN sa_info_payment_px sipa ON sig.id = sipa.px_sales_id WHERE sig.clinic = 'CDMX';";

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
    switch ($data->status) {
        case 0:
            $status = '<a type="button" class="custom-badge bg-danger">Canceló</a>';
            break;
        case 1:
            $status = '<a class="custom-badge rounded-pill bg-warning a-status" href="#" data-status=' . $data->status . ' data-pxid="' . $data->id . '" data-bs-toggle="modal" data-bs-target="#statusModal">Próximo</a>';
            break;
        case 2:
            $status = '<a class="custom-badge rounded-pill bg-info a-status" href="#" data-status=' . $data->status . ' data-pxid="' . $data->id . '" data-bs-toggle="modal" data-bs-target="#statusModal">Exp. Asignado</a>';
            break;
        case 3:
            $status = '<button class="custom-badge bg-info">En Quirófano</button>';
            break;
        default:
            $status = '<button class="custom-badge bg-dark">Desconocido</button>';
            break;
    }

    $advance_amount = "$" . number_format($data->advance_amount, 2, '.', ',');
    $full_amount = "$" . number_format($data->full_amount, 2, '.', ',');
    $fullname = '<a href="view_appointment.php?px_id=' . $data->id . '">' . $data->fullname . '</a>';


    $data_array[] = array(
        $fullname,
        $data->procedure_type,
        $advance_amount,
        $full_amount,
        $data->procedure_date,
        $data->seller,
        //$data->purpose,
        $status
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
