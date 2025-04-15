<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../../common/connection_db.php";

$files_data = [];
$user_id = 1;
// SQL para obtener los datos

$sql = "SELECT id, total, public_id, DATE_FORMAT(date, '%d/%m/%Y') date, uploaded_by, notes, clinic, approved FROM sa_corte_caja;";

$query = $conn->query($sql);
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

    $tdOptions .= "<a type='button' href='#' class='btn btn-rounded btn-outline-success aprove' data-id={$data->id} data-public-id={$data->public_id}><i class='fa fa-check'></i> Aprobar</a>
    <a type='button' href='#' class='btn btn-rounded btn-outline-dark load_files' data-id={$data->id} data-public-id={$data->public_id}><i class='fa fa-file'></i> Ver Archivos</a>
    <a type='button' href='#' class='btn btn-rounded btn-outline-danger delete' data-id={$data->id} data-public-id={$data->public_id}><i class='fa fa-times'></i> Eliminar</a>";

    $files_data[] = [
        $data->clinic,
        $data->public_id,
        $total,
        $data->date,
        $approved,
        $tdOptions
    ];
}
echo json_encode(["data" => $files_data]);
