<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once "../common/utilities.php";
require_once '../common/connection_db.php';

try {
    $treatments = [];

    // Convertimos a minúsculas el tipo de identificador, para evitar problemas con mayúsculas/minúsculas
    $px_identifier_type = isset($_POST['px_identifier_type']) ? strtolower(trim($_POST['px_identifier_type'])) : null;
    $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : null;
    $clinic = isset($_POST['clinic']) ? trim($_POST['clinic']) : ""; // Asignar "Santa Fe" si no se recibe clínica

    // Verificar si los valores son válidos
    if (empty($px_identifier_type)) {
        throw new Exception("El tipo de identificador (px_identifier_type) no está definido o es inválido.");
    }

    if (empty($identifier)) {
        throw new Exception("El identificador (identifier) no está definido o es inválido.");
    }

    // Definimos la consulta en base al tipo de identificador (id o num_med_record)
    if ($px_identifier_type === 'id') {
        // Si el identificador es 'id', buscar en base al ID del paciente
        $sql_row = "SELECT t_a.id, t_a.px_id, t_a.date, t_a.clinic, t_a.doctor, t_a.type, t_a.notes 
                    FROM enf_treatments_appointments_ext t_a 
                    WHERE t_a.px_id = ?  
                    ORDER BY t_a.date ASC";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("i", $identifier);  // 'i' para enteros, 's' para cadenas
    } elseif ($px_identifier_type === 'exp') {
        // Si el identificador es 'exp', buscar en base al número de expediente
        $sql_row = "SELECT t_a.id, t_a.date, t_a.clinic, t_a.doctor, t_a.type, t_a.notes 
                    FROM enf_treatments_appointments t_a 
                    WHERE t_a.num_med_record = ? AND t_a.origin = ? 
                    ORDER BY t_a.date ASC";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("is", $identifier, $clinic);  // 'i' para enteros, 's' para cadenas
    } else {
        // Si el tipo de identificador no es válido
        throw new Exception("Tipo de identificador inválido. Debe ser 'id' o 'exp'.");
    }

    // Ejecutar la consulta SQL
    if (!$sql->execute()) {
        throw new Exception("Error al obtener los tratamientos: " . $sql->error);
    }

    // Procesar el resultado
    $result = $sql->get_result();
    while ($data = $result->fetch_object()) {
        $treatments[] = [
            'id' => $data->id,
            'date' => $data->date,
            'clinic' => $data->clinic,
            'doctor' => $data->doctor,
            'notes' => $data->notes,
            'type' => $data->type,
            'px_identifier_type' => $px_identifier_type,
            'px_identifier' => $identifier,
        ];
    }

    $success = true;
} catch (Exception $e) {
    // Capturar cualquier excepción y preparar un mensaje de error
    $success = false;
    $message = "Error: " . $e->getMessage();
}

// Devolver la respuesta en formato JSON
echo json_encode(["success" => $success, "treatments" => $treatments, "message" => $message ?? null, "clinic" => $clinic]);
