<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
  $lead_id = $_POST['lead_id'];
  $type = $_POST['type'];
  $status = 1;

  $sql_row = "SELECT id FROM sa_info_payment_px WHERE type = ? AND lead_id = ? AND status = ?;";

  $sql = $conn->prepare($sql_row);
  $sql->bind_param("sii", $type, $lead_id, $status);

  if (!$sql->execute()) {
    throw new Exception($sql->error);
  }

  $success = true;
  $exist = ($sql->num_rows > 0) ? true : false;
  $message = ($exist) ? "Ya existe un anticipo. Revísalo en el Historial de Transacciones" : '';
} catch (Exception $e) {
  $success = false;
  $exist = false;
  $message = $e->getMessage();
}

echo json_encode([
  "success" => $success,
  "exist" => $exist,
  "message" => $message
]);
