<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); //
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . "/../common/connection_db.php";
try {
    $clinic = $_POST['clinic'];
    $userid = $_POST['user_id'];
    $date = date("Y-m-d H:i");

    $sqlConsul = "SELECT `lead_id`, `subject`, `comments`, `assigned_to`, `end_date`, `status` FROM `sa_lead_tasks` WHERE DATE(`end_date`) = CURDATE() AND created_by = ?; ";
    $sql = $conn->prepare($userid);
    $sql->bind_param("i", $userid);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener tus notificaciones: " . $sql->error);
    }
    $result = $sql->get_result();
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

    echo json_encode(["success" => true, "tasks" => $appointments, "date" => $date]);

    // Cierra la conexión
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["success" => false]);
}
