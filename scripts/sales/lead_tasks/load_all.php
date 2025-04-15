<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');
require_once __DIR__ . "/../../common/connection_db.php";

function formatDateToSpanish($date)
{
    // Convierte la fecha a formato en español
    $months = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre'
    ];
    $date = date('d F Y', strtotime($date));
    return strtr($date, $months);
}

try {
    $notifications = "";
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;

    if ($user_id === null) {
        echo "No se proporcionó el ID del usuario.";
        exit;
    }

    // Consulta SQL modificada para seleccionar la fecha del dia actual
    $sql_seller_notif = "
    SELECT slt.id AS task_id, slt.lead_id, slt.subject, slt.comments, 
           slt.end_date, 
           CONCAT(sl.first_name, ' ', sl.last_name) AS name, slt.status 
    FROM sa_lead_tasks slt 
    LEFT JOIN sa_leads sl ON slt.lead_id = sl.id 
    WHERE slt.assigned_to = ? 
      AND slt.status = 0 
      AND DATE(slt.end_date) = CURDATE() 
    ORDER BY slt.end_date DESC;
";

    $sql = $conn->prepare($sql_seller_notif);
    $sql->bind_param("i", $user_id);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener tus notificaciones: " . $sql->error);
    }

    $result = $sql->get_result();

    if ($result) {
        while ($data = $result->fetch_object()) {
            $notifications .= loadNotification($data);
        }
    }

    $message = "Done";
    $success = true;
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo $notifications;
$conn->close();

function loadNotification($data)
{
    // Verificar si 'end_date' existe en el objeto $data
    $formatted_end_date = formatDateToSpanish($data->end_date);
    $hour = strtotime($data->end_date);
    $hour = $hour !== false ? date("g:i A", $hour) : 'Hora no disponible';

    $date_f = $formatted_end_date;  // Utilizar la fecha formateada en español

    $end_datetime = strtotime($data->end_date);
    $end_datetime = $end_datetime !== false ? date("d.M g:i A", $end_datetime) : 'Fecha y hora no disponible';

    $is_reminder = "";

    $type = "task";
    $icon = "check";
    $bg_color = ($data->status === "Pendiente") ? "#d9534f" : "#5cb85c";

    $notification = "<li>
                        <div >
                            <div style='display:flex; gap: 1 rem;' class='list-item'>
                                <div class='list-left'>
                                     <span class='avatar status-notif' style='background:{$bg_color};' data-notif-id={$data->task_id} data-type='{$type}'>
                                        <i class='complete-task fa fa-{$icon}'></i>
                                </div>
                                <a style='  width: 100%;' href='view_lead.php?id={$data->lead_id}' class='list-body'>
                                    <span class='message-author'>Lead: {$data->name}</span>
                                    <span class='message-time'>{$date_f}</span>
                                    <br>
                                    
                                       <span class='message-time'>{$hour}</span>
                                    <div class='clearfix'></div>
                                    <span class='message-content'>Asunto: {$data->subject}</span>
                                    <span class='message-content'>Mensaje: {$data->comments}</span>
                                </a>
                            </div>
                        </a>
                    </li>";

    return $notification;
}
