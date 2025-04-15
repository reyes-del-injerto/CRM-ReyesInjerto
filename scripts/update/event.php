<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once "../connection_db.php";

$revision_time = "";

$event_id = $_POST['event_id'];

$event_type = $_POST['event_type'];
$attendance_type = $_POST['attendance_type'];
$num_med_record = isset($_POST['num_med_record']) ? $_POST['num_med_record'] : 0;
$patient_name = isset($_POST['patient_name']) ? $_POST['patient_name'] : '';

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

//! Status 0 = "Pendiente sin confirmar"
//! Status 1 = "Pendiente, confirmado"
//! Status 2 = "Acudió a cita" 
//! Status 3 = "No acudió a cita"

$description = $_POST['notes'];

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
    echo json_encode(['success' => false, 'message' => 'No se pudo determinar el tipo de evento. Contacta a Admin.']);
    exit();
}

$clinic = $_POST['clinic'];
$status = $_POST['status'];
$qualy = $_POST['qualy'];

$sql_row = "UPDATE sa_events SET event_type = ?, attendance_type = ?, title = ?, start = ?, end = ?, description = ?, clinic = ?, status = ?, qualy = ?, review_time = ? WHERE id = ?;";
$sql = $conn->prepare($sql_row);

$sql->bind_param("sissssssssi", $event_type, $attendance_type, $title, $mysql_start_datetime, $mysql_end_datetime, $description, $clinic, $status, $qualy, $revision_time, $event_id);
if ($sql->execute() === TRUE) {
    if ($_POST['event_type'] === "valoracion") {

        $sql_row = "UPDATE sa_evaluation_events SET seller = ? WHERE event_id = ?;";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("si", $seller, $event_id);

        if ($sql->execute() === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Evento actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $sql->error]);
        }
    } else {
        echo json_encode(['success' => true, 'message' => 'Evento actualizado correctamente']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $sql->error]);
}
