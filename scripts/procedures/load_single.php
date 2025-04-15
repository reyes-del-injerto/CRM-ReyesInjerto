<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../common/utilities.php";
require_once __DIR__ . "/../common/connection_db.php";

$procedure_id = $_POST['procedure_id'];
$procedure_info = [];

try {
    // Consulta principal para obtener la información del procedimiento
    $sql_row = "SELECT CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
                        sla.procedure_date, 
                        sla.procedure_type, 
                        ep.num_med_record, 
                        sla.status,
                        ep.touchup, 
                        ep.room, 
                        ep.specialist, 
                        ep.notes
                FROM enf_procedures ep 
                INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id 
                WHERE 
                ep.lead_id = ? AND sla.status=1;";



   /*  $sql_row = "SELECT CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
                        sla.procedure_date, 
                        sla.procedure_type, 
                        ep.num_med_record, 
                        ep.touchup, 
                        ep.room, 
                        ep.specialist, 
                        ep.notes,
                        eta.inv_type
                FROM enf_procedures ep 
                INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id 
                LEFT JOIN enf_treatments_appointments eta ON ep.num_med_record = eta.num_med_record
                WHERE sla.status = 1 AND ep.id = ?"; */

    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $procedure_id);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener la info. del procedimiento: " . $sql->error);
    }
    $result = $sql->get_result();

    if ($result->num_rows != 1) {
        throw new Exception("Error de duplicidad. Contacta al administrador.");
    }

    $procedure_info = $result->fetch_assoc();

    $message = "Done.";
    $success = true;
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "procedure_info" => $procedure_info, "message" => $message]);

$conn->close();
