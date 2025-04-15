<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once "../common/utilities.php";
require_once "../common/connection_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $clinic = $_POST['clinic'] ?? 'Santa Fe';
        $date = date("Y-m-d");

        // Construye la consulta SQL condicionalmente
        $sql = "SELECT DISTINCT
                    leads.id AS lead_id, 
                    CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
                    sla.procedure_date, 
                    sla.procedure_type, 
                    ep.id AS procedure_id,
                    ep.num_med_record, 
                    ep.room, 
                    ep.specialist,
                    sla.status,
                    sla.enfermedades
                FROM sa_leads AS leads 
                INNER JOIN sa_leads_assessment sla ON leads.id = sla.lead_id 
                LEFT JOIN enf_procedures AS ep ON leads.id = ep.lead_id 
                WHERE sla.procedure_date = ? 
                  AND sla.status = 1";

        // Agrega el filtro de clínica según corresponda
        $sql .= ($clinic === 'Queretaro') ? " AND sla.clinic = ?" : " AND sla.clinic != 'Queretaro'";

        // Prepara la consulta
        $stmt = $conn->prepare($sql);

        // Asigna parámetros según corresponda
        if ($clinic === 'Queretaro') {
            $stmt->bind_param("ss", $date, $clinic);
        } else {
            $stmt->bind_param("s", $date);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            throw new Exception("No hay procedimientos programados para hoy en la clínica seleccionada.");
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(["success" => true, "data" => $data]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
