<?php
//1 
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
  $event_type = $_POST['event_type'];
  $attendance_type = $_POST['attendance_type'];
  $num_med_record = isset($_POST['num_med_record']) ? $_POST['num_med_record'] : 0;
  $patient_name = isset($_POST['patient_name']) ? $_POST['patient_name'] : '';
  $event_name = isset($_POST['event_name']) ? $_POST['event_name'] : '';

  $date = $_POST['event_date'];
  $start = $_POST['start_date'];
  $format = (strpos($start, 'AM') !== false || strpos($start, 'PM') !== false) ? 'h:i A' : 'H:i';
  $end = $_POST['end_date'];

  $start_datetime = $date . " " . $start;
  $parse_start_datetime = DateTime::createFromFormat("Y-m-d {$format}", $start_datetime);
  $mysql_start_datetime = $parse_start_datetime->format('Y-m-d H:i:s');

  $end_datetime = $date . " " . $end;
  $parse_end_datetime = DateTime::createFromFormat('Y-m-d h:i A', $end_datetime);
  $mysql_end_datetime = $parse_end_datetime->format('Y-m-d H:i:s');

  $review_time = "";

  $description = $_POST['notes'];

  $clinic = $_POST['clinic'];
  $uploaded_by = $_POST['user_id'];

  if ($_POST['event_type'] === "revision") {
    $title = "{$patient_name} - {$num_med_record}";
    $review_time = $_POST['revision_time'];
  } else if ($_POST['event_type'] === "valoracion") {
    $title = $patient_name;
    $seller = $_POST['seller'];
  } else if ($_POST['event_type'] === "tratamiento") {
    $title = "{$patient_name} - {$num_med_record}";
    $tx_type = (isset($_POST['tx_type'])) ? $_POST['tx_type'] : '';
  } else if ($_POST['event_type'] === "evento") {
    $title = "{$event_name} ";
    $tx_type = (isset($_POST['tx_type'])) ? $_POST['tx_type'] : '';
  }else {
    throw new Exception('No se pudo determinar el tipo de evento.');
  }

  $status = $_POST['status'];
  $qualy = $_POST['qualy'];

  $sql_row = "INSERT INTO sa_events (event_type, attendance_type, title, start, end, description, clinic, qualy, status, review_time, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
  $sql = $conn->prepare($sql_row);

  $sql->bind_param("sissssssssi", $event_type, $attendance_type, $title, $mysql_start_datetime, $mysql_end_datetime, $description, $clinic, $qualy, $status, $review_time, $uploaded_by);

  if (!$sql->execute() || !$sql->affected_rows > 0) throw new Exception('No se pudo añadir el tipo de evento. Contacta al administrador.');


  $insert_id = $conn->insert_id;

  if ($_POST['event_type'] === "valoracion") {
    $sql_evaluation = "INSERT INTO sa_assessment_events (event_id, seller) VALUES (?, ?);";
    $sql = $conn->prepare($sql_evaluation);
    $sql->bind_param('is', $insert_id, $seller);

    if (!$sql->execute() || !$sql->affected_rows > 0) throw new Exception('No se pudo añadir el evento. Contacta al administrador.');
  }

  $success = true;
  $message = "Evento actualizado correctamente.";
} catch (Exception $e) {
  $success = false;
  $message = "Error:" . $e->getMessage();
}

echo json_encode(['success' => $success, 'message' => $message . $sql->error]);
