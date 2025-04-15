<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once "../connection_db.php";
$search = $_POST['search'];
$clinic = $_POST['clinic'];

$sql = "SELECT e.id, e.event_type, e.attendance_type, e.title, e.start, DATE_FORMAT(e.start, '%d/%m/%y') AS date, e.description, e.clinic, e.status, e.qualy, e.uploaded_by, e.review_time, eva.seller, eva.closer, u.nombre AS uploaded_by FROM sa_events e LEFT JOIN sa_evaluation_events eva ON e.id = eva.event_id LEFT JOIN usuarios u ON e.uploaded_by = u.id  WHERE title LIKE '%{$search}%' AND e.clinic = '{$clinic}' ORDER BY e.start DESC;";

$result = $conn->query($sql);
$events_qty = false;
// Verifica si hay resultados
if ($result->num_rows > 0) {
    // Inicializa un array para almacenar los eventos
    // Itera sobre los resultados y agrega cada evento al array
    while ($data = $result->fetch_object()) {

        $attendance_type = ($data->attendance_type) ? 'Virtual' : 'Presencial';

        switch ($data->event_type) {
            case 'revision':
                $pre = "Revisión $attendance_type $data->review_time";
                $backgroundColor = '#e67c73';

                $title = "[$pre] $data->title";
                break;
            case 'valoracion':
                $pre = "Valoración $attendance_type";
                $backgroundColor = '#039be5';
                $title = "[$pre] $data->title";
                break;
            case 'tratamiento':
                $pre = "Tratamiento";
                $backgroundColor = '#f6c026';

                $title = "[{$pre}] $data->title";
                break;
            default:
                $pre = "ERROR";
                $backgroundColor = 'red';
        }

        $events[] = array(
            'id' => $data->id,
            'title' => $title,
            'start' => $data->start,
            'date' => $data->date,
            'clinic' => $data->clinic,
            'status' => $data->status,
            'qualy' => $data->qualy
        );
    }
    $events_qty = true;
}

echo ($events_qty) ? json_encode($events) : 0;

// Cierra la conexión
$conn->close();
