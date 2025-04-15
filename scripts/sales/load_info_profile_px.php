<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$lead_id = $_POST['lead_id'];
$status = 1;
$profile = [];
try {
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
    sla.status,
    scp.quoted_cash_amount,
    scp.quoted_cc_amount,
    scp.installments
FROM sa_leads sl
LEFT JOIN (
    SELECT *
    FROM sa_leads_assessment
    WHERE status IN (1, 2)
    ORDER BY FIELD(status, 1, 2), date DESC
) sla ON sl.id = sla.lead_id
LEFT JOIN sa_closed_px scp ON sl.id = scp.lead_id
WHERE sl.id = ?
  AND sla.status IS NOT NULL

            
             ";
  // AND sla.status = ?

  $stmt = $conn->prepare($sql);
  //$stmt->bind_param("ii", $lead_id, $status);
  $stmt->bind_param("i", $lead_id);

  if (!$stmt->execute()) {
    throw new Exception("Error al obtener el perfil del px. " . $stmt->error);
  }

  // Obtiene los resultados
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $profile = $result->fetch_all(MYSQLI_ASSOC);
  }
  $success = true;
} catch (Exception $e) {
  $success = false;
  $message = $e->getMessage();
}
echo json_encode(["success" => $success, "profile" => $profile]);
$conn->close();
