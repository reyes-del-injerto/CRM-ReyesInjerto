<?php

error_reporting(E_ALL);
ini_set('error_reporting', '-1');
ini_set('display_errors', '1');
session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {

  $clinic = (isset($_POST['clinic'])) ? $_POST['clinic'] : '';

  $events = [];
  $consul = "";

  if (isset($_POST['filters']) && $_POST['filters'] != 0) {
    $filters = $_POST['filters'];
    $filterString = implode("', '", $filters);
    $whereClause = '';

    if (!empty($filterString)) {
      $whereClause = "event_type IN ('$filterString')";
    }
    $consul = 1;
    $sql_row = "SELECT e.id, e.event_type, e.attendance_type, e.title, e.start, e.end, e.description, e.clinic, e.status, e.qualy, e.uploaded_by, e.review_time, sae.seller, u.nombre AS uploaded_by FROM sa_events e LEFT JOIN sa_assessment_events sae ON e.id = sae.event_id LEFT JOIN usuarios u ON e.uploaded_by = u.id  WHERE e.clinic = ? ";

    if (!empty($whereClause)) {
      $consul = 1.1;
      $sql_row .= " AND $whereClause";
    }
  } else {
    $consul = 2;
    $sql_row = "SELECT e.id, e.event_type, e.attendance_type, e.title, e.start, e.end, e.description, e.clinic, e.status, e.qualy, e.uploaded_by, e.review_time, sae.seller, u.nombre AS uploaded_by FROM sa_events e LEFT JOIN sa_assessment_events sae ON e.id = sae.event_id LEFT JOIN usuarios u ON e.uploaded_by = u.id  WHERE e.clinic = ?;";
  }
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("s", $clinic);

  $sql->execute();
  $result = $sql->get_result();

  if ($result->num_rows > 0) {
    while ($data = $result->fetch_object()) {
      $events[] = addEvent($data);
    }
  }

  $success = true;
} catch (Exception $e) {
  $success = false;
  $message = "Error:" . $e->getMessage() . " Code:" . $e->getCode();
}

if ($clinic == "Santafe") {
  // original $sql = "SELECT sig.id, CONCAT(sig.first_name, ' ', sig.last_name) AS title,  sig.procedure_type, sig.purpose AS description,  DATE_FORMAT(sig.procedure_date, '%Y-%m-%dT07:00:00') as start,  DATE_FORMAT(sig.procedure_date, '%Y-%m-%dT08:00:00') as end FROM sa_info_general_px sig;";
  $sql = "SELECT sls.id, CONCAT(sls.first_name, ' ', sls.last_name) AS title, sls.procedure_type, sls.notes AS description, DATE_FORMAT(sls.procedure_date, '%Y-%m-%dT07:00:00') as start, DATE_FORMAT(sls.procedure_date, '%Y-%m-%dT08:00:00') as end FROM sa_leads_assessment sls WHERE status = 1 AND clinic IN ('Santa Fe', 'Pedregal') ";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $backgroundColor = "green";
    while ($data = $result->fetch_object()) {

      $events[] = array(
        'id' => $data->id,
        'title' => $data->title . " [$data->procedure_type]",
        'start' => $data->start,  // Asegúrate de que $start esté en el formato correcto
        'end' => $data->end,      // Asegúrate de que $end esté en el formato correcto
        'backgroundColor' => $backgroundColor, // Opcional, para personalizar el color de fondo del evento
        // Este campo no es estándar de FullCalendar, pero puedes usarlo en el evento render callback
        'extendedProps' => array(              // Usa extendedProps para propiedades personalizadas como 'clinic'
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
        )
      );
    }
    $events_qty = true;
  }
}

if ($clinic == "Queretaro") {
  // original $sql = "SELECT sig.id, CONCAT(sig.first_name, ' ', sig.last_name) AS title,  sig.procedure_type, sig.purpose AS description,  DATE_FORMAT(sig.procedure_date, '%Y-%m-%dT07:00:00') as start,  DATE_FORMAT(sig.procedure_date, '%Y-%m-%dT08:00:00') as end FROM sa_info_general_px sig;";
  $sql = "SELECT sls.id, CONCAT(sls.first_name, ' ', sls.last_name) AS title, sls.procedure_type, sls.notes AS description, DATE_FORMAT(sls.procedure_date, '%Y-%m-%dT07:00:00') as start, DATE_FORMAT(sls.procedure_date, '%Y-%m-%dT08:00:00') as end FROM sa_leads_assessment sls  WHERE status = 1 AND clinic = 'Queretaro'";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $backgroundColor = "green";
    while ($data = $result->fetch_object()) {

      $events[] = array(
        'id' => $data->id,
        'title' => $data->title . " [$data->procedure_type]",
        'start' => $data->start,  // Asegúrate de que $start esté en el formato correcto
        'end' => $data->end,      // Asegúrate de que $end esté en el formato correcto
        'backgroundColor' => $backgroundColor, // Opcional, para personalizar el color de fondo del evento
        // Este campo no es estándar de FullCalendar, pero puedes usarlo en el evento render callback
        'extendedProps' => array(              // Usa extendedProps para propiedades personalizadas como 'clinic'
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
        )
      );
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
}

echo json_encode(["success" => $success, "events" => $events, "sql" => $sql_row, "consul" => $consul]);

//Tratamiento, Revisión o Valoración
function addEvent($data)
{
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
      $title = "$data->title [$pre]";
      break;

    case 'evento':
      $pre = "EV";
      $backgroundColor = '#Ff8000';
      $title = "$data->title [$pre]";
      break;

    default:
      $pre = "ERROR";
      $backgroundColor = 'red';
      $title = "$data->title [$pre]";
      break;
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
      'uploaded_by' => $data->uploaded_by,
      'revision_time' => $data->review_time,
      'status' => $data->status,
      'qualy' => $data->qualy
    ]
  ];

  return $event;
}
