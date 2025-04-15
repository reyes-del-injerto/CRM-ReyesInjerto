<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$lead_id = $_POST['lead_id'];
$info_payment = [];
$payments = [];

/* Obtener cotización */
$sql = "SELECT quoted_cash_amount, quoted_cc_amount, installments FROM sa_closed_px WHERE lead_id = {$lead_id};";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $data = $result->fetch_object();
    $info_payment = [
        "quoted_cash_amount" => $data->quoted_cash_amount,
        "quoted_cc_amount"  => $data->quoted_cc_amount,
        "installments" => $data->installments
    ];
}


$sql = "SET lc_time_names = 'es_ES'";
$conn->query($sql);

$sql = "SELECT type, amount, DATE_FORMAT(payment_date, '%d %b %Y') AS date FROM sa_info_payment_px WHERE lead_id = {$lead_id} AND status = 1 AND (type = 'abono' OR type = 'anticipo' OR type = 'liquidacion') ORDER BY date DESC;";


$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($data = $result->fetch_object()) {
        $date = strtoupper($data->date);
        $payments[] = [
            'type' => $data->type,
            'amount' => $data->amount,
            'date' => $date
        ];
    }
}

echo json_encode(["success" => true, "info_payment" => $info_payment, "payments" => $payments]);
