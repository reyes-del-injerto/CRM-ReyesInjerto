<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');
require_once __DIR__ . "/../../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si task_id está definido y no está vacío
    if (isset($_POST['task_id']) && !empty($_POST['task_id'])) {
        $task_id = $_POST['task_id'];

        // Cambia el estado a 1
        $sql = "UPDATE sa_lead_tasks SET status = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $task_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'success' => 'true']);

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el estado']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta']);
        }
    } else {
        // task_id no está definido o está vacío
        echo json_encode(['status' => 'error', 'message' => 'ID de tarea no proporcionado o vacío']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
