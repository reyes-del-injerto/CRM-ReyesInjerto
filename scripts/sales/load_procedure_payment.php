<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$message = "";
$status = 1;

try {
  $lead_id = $_POST['lead_id'];
  $info_payment = [];
  $payments = [];

  /* Obtener cotización */
  $sql_row = "SELECT quoted_cash_amount, quoted_cc_amount, installments FROM sa_closed_px WHERE lead_id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $lead_id);

  if (!$sql->execute()) {
    throw new Exception($sql->error);
  }

  $result = $sql->get_result();


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

  $sql_row = "SELECT type, amount, DATE_FORMAT(payment_date, '%d %b %Y') AS date FROM sa_info_payment_px WHERE lead_id = ? AND status = ? AND (type = 'abono' OR type = 'anticipo' OR type = 'liquidacion') ORDER BY date DESC;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("ii", $lead_id, $status);

  if (!$sql->execute()) {
    throw new Exception($sql->error);
  }

  $result = $sql->get_result();

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
  $success = true;
} catch (Exception $e) {
  $success = false;
  $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message, "info_payment" => $info_payment, "payments" => $payments]);
