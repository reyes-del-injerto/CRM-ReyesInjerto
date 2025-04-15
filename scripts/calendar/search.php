<?php
//1
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$search = "%" . trim($_POST['search']) . "%";
$clinic = $_POST['clinic'];
$events = [];
$success = false;

$sql = "SELECT e.id, e.event_type, e.attendance_type, e.title, e.start, DATE_FORMAT(e.start, '%d/%m/%y') AS date, e.clinic, e.qualy, e.review_time FROM sa_events e WHERE title LIKE ? AND e.clinic = ? ORDER BY e.start DESC;";

$sql = $conn->prepare($sql);
$sql->bind_param("ss", $search, $clinic);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
  while ($data = $result->fetch_object()) {

    $attendance_type = ($data->attendance_type) ? 'Virtual' : 'Presencial';

    switch ($data->event_type) {
      case 'revision':
        $pre = "Revisión $attendance_type $data->review_time";
        $title = "[$pre] $data->title";
        break;
      case 'valoracion':
        $pre = "Valoración $attendance_type";
        $title = "[$pre] $data->title";
        break;
      case 'tratamiento':
        $pre = "Tratamiento";
        $title = "[{$pre}] $data->title";
        break;
      default:
        $pre = "ERROR";
        break;
    }

    $events[] = [
      'id' => $data->id,
      'title' => $title,
      'start' => $data->start,
      'date' => $data->date,
      'clinic' => $data->clinic,
      'qualy' => $data->qualy
    ];
  }
  $success = true;
}


echo json_encode(["success" => $success, "coincidences" => $events]);

$conn->close();
