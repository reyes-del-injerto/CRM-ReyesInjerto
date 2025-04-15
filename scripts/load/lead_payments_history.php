<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$lead_id = $_POST['lead_id'];
$sql = "SELECT id, lead_id, type, amount, method, DATE_FORMAT(payment_date, '%d/%m/%Y') AS date, public_notes FROM sa_info_payment_px WHERE lead_id = $lead_id AND status = 1;";

$data_array = [];
// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
    $options = "
    <a href='storage/leads/{$data->lead_id}/invoices/{$data->type}_{$data->id}.pdf' target='_blank' class='btn btn-dark btn-sm btn-square rounded-pill'><i class='btn-icon fa fa-external-link'></i> </a>
    <a href='#' data-id='{$data->id}' class='btn btn-danger btn-sm btn-square rounded-pill delete_invoice'><i class='btn-icon fa fa-trash'></i> </a>";

    $amount = "$" . number_format($data->amount, 2, '.', ',');

    $data_array[] = array(
        $data->id,
        ucfirst($data->type),
        $data->date,
        $amount,
        $data->method,
        $data->public_notes,
        $options
    );
}

$data  = ["data" => $data_array];
echo json_encode($data);
