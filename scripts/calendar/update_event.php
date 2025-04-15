<?php
//1
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$success = false;
$message = "";

try {
  $event_id = $_POST['event_id'];
  $revision_time = null;
  $last_edit = date('Y-m-d H:i:s'); // Formato estándar de DATETIME
  $last_edit_by = $_POST['user_id'];

  $num_med_record = isset($_POST['num_med_record']) ? $_POST['num_med_record'] : 0;
  $patient_name = isset($_POST['patient_name']) ? $_POST['patient_name'] : '';

  $event_type = $_POST['event_type'];
  if ($_POST['event_type'] === "revision") {
    $title = "{$patient_name} - {$num_med_record}";
    $revision_time = $_POST['revision_time'];
  } else if ($_POST['event_type'] === "valoracion") {
    $title = $patient_name;
    $seller = $_POST['seller'];
  } else if ($_POST['event_type'] === "tratamiento") {
    $title = "{$patient_name} - {$num_med_record}";
    $tx_type = (isset($_POST['tx_type'])) ? $_POST['tx_type'] : '';
  } else {
    throw new Exception("Error. No se pudo determinar el tipo de evento.");
  }

  $attendance_type = $_POST['attendance_type'];

  $date = $_POST['event_date'];
  $start = $_POST['start_date'];
  $start_format = (strpos($start, 'AM') !== false || strpos($start, 'PM') !== false) ? 'h:i A' : 'H:i';
  $end = $_POST['end_date'];
  $end_format = (strpos($end, 'AM') !== false || strpos($end, 'PM') !== false) ? 'h:i A' : 'H:i';


  $start_datetime = $date . " " . $start;
  $parse_start_datetime = DateTime::createFromFormat("Y-m-d {$start_format}", $start_datetime);
  $mysql_start_datetime = $parse_start_datetime->format('Y-m-d H:i:s');

  $end_datetime = $date . " " . $end;
  $parse_end_datetime = DateTime::createFromFormat("Y-m-d {$end_format}", $end_datetime);
  $mysql_end_datetime = $parse_end_datetime->format('Y-m-d H:i:s');

  $description = $_POST['notes'];
  $clinic = $_POST['clinic'];

  $status = $_POST['status'];
  $qualy = $_POST['qualy'];


  $sql_row = "UPDATE sa_events SET event_type = ?, attendance_type = ?, title = ?, start = ?, end = ?, description = ?, clinic = ?, status = ?, qualy = ?, review_time = ?, last_edit = ? , last_edit_by = ? WHERE id = ?;";
  $sql = $conn->prepare($sql_row);

  $sql->bind_param("sisssssssssii", $event_type, $attendance_type, $title, $mysql_start_datetime, $mysql_end_datetime, $description, $clinic, $status, $qualy, $revision_time, $last_edit,$last_edit_by, $event_id);

  if (!$sql->execute()) throw new Exception("Error al actualizar." . $sql->error);

  if ($_POST['event_type'] === "valoracion") {

    $sql_row = "UPDATE sa_assessment_events SET seller = ? WHERE event_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("si", $seller, $event_id);

    if (!$sql->execute())
      throw new Exception('Error al actualizar: ' . $sql->error);
  }

  $success = true;
  $message = "Evento actualizado correctamente";
} catch (Exception $e) {
  $success = false;
  $message = $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message]);
