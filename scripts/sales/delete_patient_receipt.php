<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {

  // Verificación de solicitud POST y existencia del parámetro 'id'
  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['invoice_id'])) {
    throw new Exception("Solicitud incorrecta.");
  }

  $invoice_id = $_POST['invoice_id'];
  $sql_row = "UPDATE sa_info_payment_px SET status = 0 WHERE id = ?";
  $sql = $conn->prepare($sql_row);

  $sql->bind_param("i", $invoice_id);

  if (!$sql->execute()) {
    throw new Exception("Error" . $sql->error);
  }

  $success = true;
  $message = 'Transacción eliminada correctamente';
} catch (Exception $e) {
  $success = false;
  $message = "Error:" . $e->getMessage();
}
$data  = ["success" => $success, "message" => $message];
echo json_encode($data);

$conn->close();
