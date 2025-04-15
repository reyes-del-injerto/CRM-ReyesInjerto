<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once  "../common/utilities.php";
require_once '../common/connection_db.php';

try {
    $treatments = [];
    $num_med_record = $_POST['num_med_record'];

    $sql_row = "SELECT t_a.id, t_a.date, t_a.clinic, t_a.doctor, t_a.type, t_a.notes FROM enf_treatments_appointments t_a WHERE t_a.num_med_record = ? ORDER BY t_a.date ASC";

    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $num_med_record);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener los tratamientos: " . $sql->error);
    }
    $result = $sql->get_result();

    while ($data = $result->fetch_object()) {
        $treatments[] = [
            'id' => $data->id,
            'date' => $data->date,
            'clinic' => $data->clinic,
            'doctor' => $data->doctor,
            'type' => $data->type,
            'notes' => $data->notes
        ];
    }

    $success = true;
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "treatments" => $treatments]);
