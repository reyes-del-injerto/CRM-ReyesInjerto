<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
  $created_at = date("Y-m-d H:i:s");
  $last_activity = date("Y-m-d H:i:s");

  /* Patient General Info */
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $clinic = $_POST['clinic'];
  $origin = $_POST['origin'];
  $phone = $_POST['phone'];
  $interested_in = $_POST['interested_in'];
  $stage = $_POST['stage'];
  $qualif = $_POST['qualif'];

  /* Procedure Info */
  $seller = $_POST['seller'];
  $notes = $_POST['notes'];
  $link = $_POST['link'];

  $sql_row = "INSERT INTO sa_leads (created_at, first_name, last_name, clinic, origin, phone, interested_in, stage, quali, link, notes, seller, last_activity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
  $sql = $conn->prepare($sql_row);

  $sql->bind_param("sssssssssssss", $created_at, $first_name, $last_name, $clinic, $origin, $phone, $interested_in, $stage, $qualif, $link, $notes, $seller, $last_activity);
  if (!$sql->execute() || !$sql->affected_rows > 0) throw new Exception("Al añadir el lead." . $sql->error);

  $success = true;
  $message = "Lead añadido correctamente. 🦁";
  $lead_id = $conn->insert_id;
} catch (Exception $e) {
  $success = false;
  $message = "Error: " . $e->getMessage();
  $lead_id = null;
}
echo json_encode(["success" => $success, "message" => $message, 'lead_id' => $lead_id]);
$conn->close();
