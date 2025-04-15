<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');
require_once __DIR__ . "/../../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lead_id = $_POST['lead_id'];
    $new_stage = $_POST['new_stage'];

    $sql = "UPDATE sa_leads SET quali = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $new_stage, $lead_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la etapa']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
