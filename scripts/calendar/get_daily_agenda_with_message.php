<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$agenda = "";
$success = false;

$clinic = trim($_POST['clinic']);

// Calcular la fecha de mañana o pasado mañana si mañana es domingo
$timestamp_tomorrow = (date("l", strtotime("+1 day")) !== "Sunday") ? strtotime("+1 day") : strtotime("+2 day");
$date = date('Y-m-d', $timestamp_tomorrow);

$start = $date . " 00:00:00";
$end = $date . " 23:59:59";

// Construir la consulta SQL con condiciones de filtrado por clínica
$sql = "
    SELECT 
        e.event_type, 
        e.attendance_type, 
        e.title, 
        e.start, 
        DATE_FORMAT(e.start, '%l:%i%p') AS start_hour, 
        e.review_time, 
        eva.seller AS seller 
    FROM 
        sa_events e 
    LEFT JOIN 
        sa_assessment_events eva 
    ON 
        e.id = eva.event_id 
    WHERE 
        e.clinic = ? 
        AND e.start BETWEEN ? AND ? 
";

// Agregar la parte de los procedimientos sólo si la clínica no es "Pedregal"
if ($clinic !== "Pedregal") {
    $sql .= "
    UNION ALL 
    SELECT 
        'Procedimiento' AS event_type, 
        '0' AS attendance_type, 
        CONCAT(l.first_name, ' ', l.last_name) AS title, 
        CONCAT(l.procedure_date, ' 07:00:00') AS start, 
        '7:00AM' AS start_hour, 
        l.procedure_type AS review_time, 
        l.closer AS seller 
    FROM 
        sa_leads_assessment l 
    WHERE 
        l.procedure_date BETWEEN ? AND ? 
    ";
    
    // Aplicar filtro según la clínica recibida
    if ($clinic === "Queretaro") {
        $sql .= " AND l.clinic = 'Queretaro'";
    } elseif ($clinic === "Santafe") {
        $sql .= " AND l.clinic IN ('Santa Fe', 'Pedregal')";
    }
}

$sql .= " ORDER BY start ASC;";

// Preparar la consulta
$sql = $conn->prepare($sql);

// Enlazar parámetros dependiendo del caso
if ($clinic !== "Pedregal") {
    // Si no es Pedregal, enlazar los parámetros para eventos y procedimientos
    $sql->bind_param("sssss", $clinic, $start, $end, $start, $end);
} else {
    // Si es Pedregal, solo enlazar los parámetros para eventos
    $sql->bind_param("sss", $clinic, $start, $end);
}

$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    while ($data = $result->fetch_object()) {
        $start_hour = str_replace(':00', '', $data->start_hour);
        $start_hour = trim($start_hour);

        $attendance_type = ($data->attendance_type == '1') ? 'VIRTUAL' : '';

        switch ($data->event_type) {
            case 'revision':
                $review_time = str_replace("D", "DIAS", $data->review_time);
                $review_time = str_replace("M", "MESES", $review_time);
                $review_time = ($review_time == "1 MESES") ? "1 MES" : $review_time;
                $pre = "REV $review_time $attendance_type";
                break;
            case 'valoracion':
                if (!is_null($data->seller)) {
                    $seller = explode(" ", $data->seller);
                    $seller = trim($seller[0]);
                } else {
                    $seller = '';
                }
                $pre = "VALORACION $attendance_type $seller";
                break;
            case 'tratamiento':
                $pre = "TRATAMIENTO";
                break;
            case 'Procedimiento':
                $pre = "$data->review_time";
                break;
            default:
                $pre = "ERROR";
        }

        $name = explode("-", $data->title);
        $name = trim($name[0]);

        $agenda .= "{$start_hour} {$name} - {$pre}\n\n";
    }
    $success = true;
} else {
    $agenda = "No se encontraron eventos para la clínica y fecha especificadas.";
}

echo json_encode(["success" => $success, "agenda" => $agenda]);

$conn->close();
