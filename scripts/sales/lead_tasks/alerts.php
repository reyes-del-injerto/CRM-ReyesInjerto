<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');
require_once __DIR__ . "/../../common/connection_db.php";

// Verificar si se recibió un user_id por POST
if (isset($_POST['user_id']) && isset($_POST['current_datetime'])) {
    $user_id = $_POST['user_id'];
    $currentDateTime = $_POST['current_datetime'];

    try {
        $notifications = array();
        $seller_id = $user_id; // Utilizar el ID recibido por POST

        // Modificar la consulta SQL para obtener las notificaciones que coincidan con la hora actual exacta
        $sql_seller_notif = "SELECT slt.id task_id, slt.lead_id, slt.subject, slt.comments, slt.end_date, CONCAT(sl.first_name, ' ', sl.last_name) AS name, slt.status AS 'state' FROM sa_lead_tasks slt LEFT JOIN sa_leads sl ON slt.lead_id = sl.id WHERE assigned_to = ? AND DATE(slt.end_date) = CURDATE() AND slt.status = 0 AND end_date = ? ";

        $sql = $conn->prepare($sql_seller_notif);
        $sql->bind_param("is", $seller_id, $currentDateTime); // Bind parameters

        if (!$sql->execute()) {
            throw new Exception("Error al obtener tus notificaciones: " . $sql->error);
        }
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            // Si hay resultado, obtener los detalles de la tarea
            $data = $result->fetch_object();

            $notifications[] = array(
                'task_id' => $data->task_id,
                
                'lead_id' => $data->lead_id,
                'name' => $data->name,
                'subject' => $data->subject,
                'comments' => $data->comments,
                'end_date' => $data->end_date,
                
                'state' => $data->state,
                
            );
        } else {
            // Si no hay resultado para la hora actual
            $notifications[] = array(
                'no_task' => true,
                'moment' => $currentDateTime,
                'message' => 'Sin tarea para este momento'
            );
        }

        $response = array(
            'success' => true,
            'message' => 'Done',
            'notifications' => $notifications
        );
    } catch (Exception $e) {
        $response = array(
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        );
    }

    // Devolver respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Si no se recibió un user_id o current_datetime válido por POST
    $response = array(
        'success' => false,
        'message' => 'No se recibió un user_id o current_datetime válido'
    );

    // Devolver respuesta JSON con mensaje de error
    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();
?>
