<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once "../connection_db.php";

$clinic = $_POST['clinic'];

$timestamp_tomorrow = strtotime('+2 day');
$date = date('Y-m-d', $timestamp_tomorrow);

$events = "";
$events_qty = false;

$sql = "SELECT e.event_type, e.attendance_type, e.title, e.start, DATE_FORMAT(e.start, '%l:%i%p') AS start_hour, e.review_time, eva.seller AS seller FROM sa_events e LEFT JOIN sa_evaluation_events eva ON e.id = eva.event_id  WHERE e.clinic = '{$clinic}' AND e.start BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59' ORDER BY start ASC;";

$result = $conn->query($sql);

// Verifica si hay resultados
if ($result->num_rows > 0) {
    while ($data = $result->fetch_object()) {
        $start_hour = str_replace(':00', '', $data->start_hour);
        $start_hour = trim($start_hour);

        $attendance_type = ($data->attendance_type) ? 'VIRTUAL' : '';

        switch ($data->event_type) {
            case 'revision':
                $review_time = str_replace("D", "DIAS", $data->review_time);
                $review_time = str_replace("M", "MESES", $review_time);
                if ($review_time == "1 MESES")
                    $review_time = "1 MES";

                $pre = "REV $review_time $attendance_type";
                break;
            case 'valoracion':
                $seller = explode(" ", $data->seller);
                $seller = trim($seller[0]);
                $pre = "VALORACION $attendance_type $seller";
                break;
            case 'tratamiento':
                $pre = "TRATAMIENTO";
                $title = "$data->title [{$pre}]";
                break;
            default:
                $pre = "ERROR";
                $backgroundColor = 'red';
        }

        $name = explode("-", $data->title);
        $name = trim($name[0]);

        $events .= "{$start_hour} {$name} - {$pre}\n\n";
    }
    $events_qty = true;
}

/*if ($clinic == "Santafe") {
	$sql = "SELECT sig.id, CONCAT(sig.first_name, ' ', sig.last_name) AS title,  sip.procedure_type, sip.purpose AS description,  DATE_FORMAT(sip.procedure_date, '%Y-%m-%dT07:00:00') as start,  DATE_FORMAT(sip.procedure_date, '%Y-%m-%dT08:00:00') as end FROM sa_info_general_px sig INNER JOIN sa_info_procedure_px sip ON sig.id = sip.px_general_id;";

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
}*/

if ($events_qty) {

    $type = "click";

    $user_id = $_SESSION['user_id'];
    $created_at = date("Y-m-d H:i:s");
    $description = "{$user_id} clicked on Get Daily Agenda at {$created_at}";
    $sql = $conn->prepare("INSERT INTO sys_logs (type,description,user_id,created_at) VALUES (?, ?, ?, ?)");

    $sql->bind_param("ssis", $type, $description, $user_id, $created_at);

    if ($sql->execute() && $sql->affected_rows > 0) {
        echo json_encode(["success" => true, "events" => $events]);
    }
} else {
    // http_response_code(204);
    echo 1;
}
// Cierra la conexión
$conn->close();
