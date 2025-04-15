<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require '../connection_db.php';
$lead_id = $_POST['lead_id'];

try {
   /*  $sql = "SELECT 
                sla.first_name, 
                sla.last_name, 
                sl.clinic, 
                sl.phone, 
                sla.date AS assessment_date,  
                sla.first_meet_type,
                sla.type AS assessment_type,
                sla.closer,
                sla.procedure_date,
                sla.procedure_type, 
                sla.notes,
                scp.quoted_cash_amount,
                scp.quoted_cc_amount,
                scp.installments
            FROM sa_leads sl
            LEFT JOIN sa_leads_assessment sla ON sl.id = sla.lead_id
            LEFT JOIN sa_closed_px scp ON sl.id = scp.lead_id
            WHERE sl.id = ?"; */
     $sql = "SELECT 
    sla.first_name, 
    sla.last_name, 
    sl.clinic, 
    sl.phone, 
    sla.date AS assessment_date,  
    sla.first_meet_type,
    sla.type AS assessment_type,
    sla.closer,
    sla.procedure_date,
    sla.procedure_type, 
    sla.notes,
    scp.quoted_cash_amount,
    scp.quoted_cc_amount,
    scp.installments,
    ep.num_med_record  
FROM sa_leads sl
LEFT JOIN sa_leads_assessment sla ON sl.id = sla.lead_id
LEFT JOIN sa_closed_px scp ON sl.id = scp.lead_id
LEFT JOIN enf_procedures ep ON sla.lead_id = ep.lead_id
WHERE sl.id = ?;"; 

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lead_id);

    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
    }

    // Obtiene los resultados
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(["success" => true, "profile" => $profile , "enf_procedures"=> "enf_procedures"]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontraron registros", "status"=> "yo"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    // Cierra la conexión
    $conn->close();
}
