<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$lead_id = $_POST['lead_id'];
$message = "";
$status = 1;
$success = false;
$assessment = false;

try {

  $sql_row = "SELECT lead_id, date, first_name, last_name, procedure_date, procedure_type, closer, first_meet_type, clinic, type, notes, created_at, timestamp FROM sa_leads_assessment WHERE lead_id = ? AND status = 1 ;";


  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $lead_id);
  if (!$sql->execute()) {
    throw new Exception("Error al consultar datos de valoración. Contacta al administrador");
  }

  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $assessment = json_encode($row);
  }

  $success = true;
} catch (Exception $e) {
  $success = false;
  $message = "Error:" . $e->getMessage();
}

$conn->close();

$response =  json_encode(["success" => $success, "assessment" => $assessment, "message" => $message]);
echo $response;
