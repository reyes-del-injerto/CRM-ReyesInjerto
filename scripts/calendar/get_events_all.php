<?php

//Falta eventos de procedimientos y vacaciones
error_reporting(E_ALL);
ini_set('error_reporting', '-1');
ini_set('display_errors', '1');
session_start();
require_once __DIR__ . "/../common/connection_db.php";

$clinic = (isset($_POST['clinic'])) ? $_POST['clinic'] : '';

$events = [];
$success = false;

if (isset($_POST['filters']) && $_POST['filters'] != 0) {
  $filters = $_POST['filters'];
  $filterString = implode("', '", $filters);
  $whereClause = '';

  if (!empty($filterString)) {
    $whereClause = "event_type IN ('$filterString')";
  }

  $sql = "SELECT e.id, e.event_type, e.attendance_type, e.title, e.start, e.end, e.description, e.clinic, e.status, e.qualy, e.uploaded_by, e.review_time, eva.seller, eva.closer, u.nombre AS uploaded_by FROM sa_events e LEFT JOIN sa_evaluation_events eva ON e.id = eva.event_id LEFT JOIN usuarios u ON e.uploaded_by = u.id  WHERE e.clinic = '{$clinic}' ";

  if (!empty($whereClause)) {
    $sql .= " AND $whereClause";
  }

  $result = $conn->query($sql);
} else {
  $sql = "SELECT e.id, e.event_type, e.attendance_type, e.title, e.start, e.end, e.description, e.clinic, e.status, e.qualy, e.uploaded_by, e.review_time, eva.seller, eva.closer, u.nombre AS uploaded_by FROM sa_events e LEFT JOIN sa_evaluation_events eva ON e.id = eva.event_id LEFT JOIN usuarios u ON e.uploaded_by = u.id  WHERE e.clinic = ?;";

  $sql = $conn->prepare($sql);
  $sql->bind_param("s", $clinic);

  $sql->execute();
  $result = $sql->get_result();
}


if ($result->num_rows > 0) {
  while ($data = $result->fetch_object()) {
    $events[] = addEvent($data);
  }
  $success = true;
}

/*
if ($clinic == "Santafe") {
	$sql = "SELECT sig.id, CONCAT(sig.first_name, ' ', sig.last_name) AS title,  sig.procedure_type, sig.purpose AS description,  DATE_FORMAT(sig.procedure_date, '%Y-%m-%dT07:00:00') as start,  DATE_FORMAT(sig.procedure_date, '%Y-%m-%dT08:00:00') as end FROM sa_info_general_px sig;";

	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$backgroundColor = "green";
		while ($data = $result->fetch_object()) {

			$events[] = array[
				'id' => $data->id,
				'title' => $data->title . " [$data->procedure_type]",
				'start' => $data->start,  
				'end' => $data->end,     
				'backgroundColor' => $backgroundColor, 
				
				'extendedProps' => [
					'clinic' => 'Santafe',
					'description' => '',
					'attendance_type' => '',
					'event_type' => 'PROC',
					'seller' => '',
					'closer' => '',
					'uploaded_by' => 1,
					'revision_time' => null,
					'status' => null,
					'qualy' => null
				]
			];
		}
		$events_qty = true;
	}
}

if (is_array($_POST['filters']) && in_array('holidays', $_POST['filters'])) {
	$sql = "SELECT ad_holidays.id, ad_employees.name, start, end, notes, approved_by 
            FROM ad_holidays 
            LEFT JOIN ad_employees ON ad_holidays.employee_id = ad_employees.id 
            WHERE status = 1";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$backgroundColor = '#ABAFD8';
		while ($data = $result->fetch_object()) {
			$events[] = [
				'id' => $data->id,
				'title' => $data->name,
				'start' => $data->start,
				'end' => $data->end,
				'allDay' => true,
				'backgroundColor' => $backgroundColor,
				'extendedProps' => [
					'clinic' => 'Santafe',
					'description' => $data->notes,
					'attendance_type' => '',
					'event_type' => 'HOL',
					'seller' => '',
					'closer' => '',
					'uploaded_by' => 1,
					'revision_time' => null,
					'status' => null,
					'qualy' => null
				]
			];
		}
	}
}*/


//Tratamiento, Revisión o Valoración
function addEvent($data) {
  $start = $data->start;
  $start = str_replace(" ", "T", $start);

  $end = $data->end;
  $end = str_replace(" ", "T", $end);

  $attendance_type = ($data->attendance_type) ? 'VIR' : 'PRE';

  switch ($data->event_type) {
    case 'revision':
      $pre = "REV $attendance_type ($data->review_time)";
      $backgroundColor = '#ff948e';

      $title = "$data->title [$pre]";
      break;
    case 'valoracion':
      $pre = "VAL $attendance_type ($data->seller)";
      $backgroundColor = '#7dc3ff';
      $title = "$data->title [$pre]";
      break;
    case 'tratamiento':
      $pre = "TRAT";
      $backgroundColor = '#f7d96c';

      $title = "$data->title [{$pre}]";
      break;
    default:
      $pre = "ERROR";
      $backgroundColor = 'red';
  }

  if ($data->status == "Confirmada" && $data->qualy == "Pendiente") {
    $backgroundColor = '#d9a6e2';
  }

  if ($data->qualy == "Asistió")
    $backgroundColor = '#87d778';
  else if ($data->qualy == "No asistió" || $data->qualy == "Reagendó")
    $backgroundColor = '#ff5555';

  $event = [
    'id' => $data->id,
    'title' => $title,
    'start' => $start,
    'end' => $end,
    'backgroundColor' => $backgroundColor,
    'extendedProps' => [
      'clinic' => $data->clinic,
      'description' => $data->description,
      'attendance_type' => $data->attendance_type,
      'event_type' => $data->event_type,
      'seller' => $data->seller,
      'closer' => $data->closer,
      'uploaded_by' => $data->uploaded_by,
      'revision_time' => $data->review_time,
      'status' => $data->status,
      'qualy' => $data->qualy
    ]
  ];

  return $event;
}

echo json_encode(["success" => $success, "events" => $events]);
