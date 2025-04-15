<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../common/connection_db.php";

try {
    $fullname = null;
    $clinics = [];
    $message = null;

    // Verificación de parámetros
    if (!isset($_POST['num_med_record']) || empty($_POST['num_med_record'])) {
        throw new Exception("El parámetro num_med_record es requerido.");
    }

    // Obtener y normalizar los parámetros
    $num_med_record = $_POST['num_med_record'];

    // Validar que num_med_record sea un número
    if (!is_numeric($num_med_record)) {
        throw new Exception("El parámetro num_med_record debe ser un número.");
    }

    // Inicializar la variable de clínica
    $clinic = null;

    // Comprobar si la clínica se proporciona
    if (isset($_POST['clinic']) && !empty($_POST['clinic'])) {
        $clinic = trim($_POST['clinic']); // Eliminar espacios en blanco
    }

    // Consulta para obtener el fullname
    $sql_fullname = "SELECT 
        CONCAT(sig.first_name, ' ', sig.last_name) AS fullname
    FROM 
        sa_leads_assessment sig
    INNER JOIN 
        enf_procedures ep ON sig.lead_id = ep.lead_id
    WHERE 
        ep.num_med_record = ?";
    
    // Agregar el filtro de clínica solo si es "Queretaro"
    if ($clinic === "Queretaro") {
        $sql_fullname .= " AND sig.clinic = 'Queretaro'";
    } else {
        $sql_fullname .= " AND sig.clinic != 'Queretaro'";
    }
    

    

    $sql_fullname .= " ORDER BY sig.created_at DESC LIMIT 1;";

    $stmt_fullname = $conn->prepare($sql_fullname);

    if (!$stmt_fullname) {
        throw new Exception("Error en la preparación de la consulta fullname: " . $conn->error);
    }

    // Vincular parámetros
    $stmt_fullname->bind_param("i", $num_med_record); // Solo el número médico

    if (!$stmt_fullname->execute()) {
        throw new Exception("Error en la ejecución de la consulta fullname: " . $stmt_fullname->error);
    }

    $stmt_fullname->bind_result($fullname);

    if (!$stmt_fullname->fetch()) {
        throw new Exception("No se encontraron resultados para el num_med_record proporcionado.");
    }

    // Finalizar el procesamiento de resultados de la primera consulta
    $stmt_fullname->free_result();

    // Consulta para obtener las clínicas asociadas al num_med_record
    $sql_clinics = "SELECT DISTINCT sa_leads_assessment.clinic
                    FROM sa_leads_assessment
                    INNER JOIN enf_procedures ON sa_leads_assessment.lead_id = enf_procedures.lead_id
                    WHERE enf_procedures.num_med_record = ?";
    
    // Agregar filtro de clínica solo si es "Queretaro"
    if ($clinic === "Queretaro") {
        $sql_clinics .= " AND sa_leads_assessment.clinic = 'Santa fe'";
    }

    $stmt_clinics = $conn->prepare($sql_clinics);

    if (!$stmt_clinics) {
        throw new Exception("Error en la preparación de la consulta de clínicas: " . $conn->error);
    }

    // Vincular parámetros para clínicas
    $stmt_clinics->bind_param("i", $num_med_record);

    if (!$stmt_clinics->execute()) {
        throw new Exception("Error en la ejecución de la consulta de clínicas: " . $stmt_clinics->error);
    }

    $result_clinics = $stmt_clinics->get_result();
    while ($row = $result_clinics->fetch_assoc()) {
        $clinics[] = $row['clinic'];
    }

    $success = true;
    $message = "Consulta exitosa";
} catch (Exception $e) {
    $success = false;
    $message = $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message, "fullname" => $fullname, "clinics" => $clinics]);
$conn->close();
?>
