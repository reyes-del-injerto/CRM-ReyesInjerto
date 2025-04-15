<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/connection_db.php";

// Inicializa variables
$data_procedures = [];
$types = "sss"; // Modificado para incluir un tercer parámetro
$total_values = [];

// Verifica si se ha recibido el mes a través de POST
if (isset($_POST['month'])) {
    $month = $_POST['month'];

    // Asigna el valor por defecto a la clínica si no se recibe
    $clinic = isset($_POST['clinica']) ? $_POST['clinica'] : 'Santa Fe';

    // Define las fechas de inicio y fin del mes
    $currentYear = date('Y');
    $startDate = "$currentYear-$month-01 00:00:00";
    $endDate = date("Y-m-t 23:59:59", strtotime($startDate));

    $total_values = [$startDate, $endDate, $clinic]; // Agrega la clínica al array de valores

    // Define la consulta SQL
    $sql = "SELECT DISTINCT sla.lead_id, 
    CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
    DATE_FORMAT(sla.procedure_date, '%d/%m/%y') AS procedure_date, 
    sla.procedure_date AS original_procedure_date, 
    sla.procedure_type, 
    ep.specialist, 
    ep.num_med_record
FROM enf_procedures ep 
INNER JOIN (
    SELECT lead_id, MAX(procedure_date) AS procedure_date
    FROM sa_leads_assessment
    WHERE status = 1
    GROUP BY lead_id
) AS latest_sla ON ep.lead_id = latest_sla.lead_id
INNER JOIN sa_leads_assessment sla ON sla.lead_id = latest_sla.lead_id 
AND sla.procedure_date = latest_sla.procedure_date
WHERE sla.procedure_date BETWEEN ? AND ?
AND sla.clinic = ?  -- Filtra por la clínica
ORDER BY sla.procedure_date ASC";

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$total_values);
    $stmt->execute();
    $result = $stmt->get_result();

    // Procesa los resultados
    while ($data = $result->fetch_object()) {
        $data_procedures[] = array(
            "name" => $data->name,
            "procedure_date" => $data->procedure_date,
            "procedure_type" => $data->procedure_type,
            "specialist" => $data->specialist,
            "clinic" => $clinic,
            "num_med" => $data->num_med_record
        );
    }

    // Devuelve los datos en formato JSON
    echo json_encode(["data" => $data_procedures]);
} else {
    echo json_encode(["error" => "No se recibió el mes."]);
}
