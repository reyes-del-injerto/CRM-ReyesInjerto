<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); //
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . "/../common/connection_db.php";
try {
    $clinic = $_POST['clinic'];
    $date = date("Y-m-d H:i");

    $sql = "SELECT event_type, attendance_type, title, DATE_FORMAT(start, '%H:%i') date FROM sa_events WHERE clinic = '$clinic' AND (start = '$date' OR start = DATE_ADD('$date', INTERVAL 10 MINUTE));";

    $appointments = [];
    // Ejeuctar el SQL
    $query = $conn->query($sql);
    if ($query->num_rows > 0) {
        while ($data = $query->fetch_object()) {
            $title = "{$data->title}";
            $attendance_type = ($data->attendance_type) ? 'Virtual' : 'Presencial';
            $body = "{$data->event_type} {$attendance_type} {$data->date} hrs";
            $body = strtoupper($body);

            $appointments[] = array(
                "title" => $title,
                "body" => $body
            );
        }
    }

    echo json_encode(["success" => true, "appointments" => $appointments, "date" => $date]);

    // Cierra la conexión
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["success" => false]);
}
