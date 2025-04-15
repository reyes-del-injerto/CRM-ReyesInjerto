<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/utilities.php";
require_once __DIR__ . "/../../common/connection_db.php";

try {
    $lead_id = $_POST['lead_id'];
    $procedure_type = $_POST['procedure_type'];

    $enable_upload_photo = false;
    $notifications = "<div class='col-12 mt-4'>
            <button data-process=0 class='btn-notif-select btn btn-primary'>Px firmó Documentos</button>
    </div>";

    $available_process = ["Px firmó documentos", "Inicio de infiltración", "Término de infiltración", "Inicio de extracción", "Término de extracción", "Inicio de infiltración", "Término de infiltración", "Inicio de incisiones", "Término de incisiones", "Inicio de implantación", "Término de procedimiento", ""];

    // Modificación para filtrar por lead_id y procedure_type
    $sql_current_process = "SELECT process, datetime FROM notifications 
                            WHERE lead_id = ? AND procedure_type = ? 
                            AND process = (
                                SELECT MAX(process) 
                                FROM notifications 
                                WHERE lead_id = ? AND procedure_type = ?
                            );";

    $sql = $conn->prepare($sql_current_process);
    $sql->bind_param("iiii", $lead_id, $procedure_type, $lead_id, $procedure_type);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener la notificación más reciente: " . $sql->error);
    }

    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $procedure_info = $result->fetch_object();
        $current_process_number = $procedure_info->process;

        // Obtener siguiente proceso
        $next_process_number = $current_process_number + 1;
        $next_process_number = floor($next_process_number);

        $notifications = "<div class='col-12 mb-4'>
                            <button data-process={$next_process_number} class='btn-notif-select btn btn-primary'>{$available_process[$next_process_number]}</button>
                        </div>";

        // Habilitar opciones de envío de fotos según el proceso actual
        if ($current_process_number == 3 || $current_process_number == 3.1) {
            $notifications .= "<hr><hr>
                                <div class='col-12 mb-4'>
                                    <button data-process=3.1 class='btn-notif-select btn btn-danger'>Enviar Hora de Extracción</button>
                                </div>";
        } else if ($current_process_number >= 7) {
            // Buscar si ya han marcado Inicio de Implantación
            $process = 9;
            $sql_find_process = "SELECT id FROM notifications WHERE lead_id = ? AND procedure_type = ? AND process = ?;";
            $sql = $conn->prepare($sql_find_process);
            $sql->bind_param("iii", $lead_id, $procedure_type, $process);

            if (!$sql->execute()) {
                throw new Exception("Error al buscar la notificación de implantación: " . $sql->error);
            }

            $result = $sql->get_result();
            if ($result->num_rows == 0) {
                $notifications .= "<div class='col-12 mb-4'>
                                    <button data-process=9 class='btn-notif-select btn btn-primary'>Inicio de implantación</button>
                                </div>";
            } else {
                // Buscar si ya han marcado Término de Incisiones
                $process = 8;
                $sql_find_process = "SELECT id FROM notifications WHERE lead_id = ? AND procedure_type = ? AND process = ?;";
                $sql = $conn->prepare($sql_find_process);
                $sql->bind_param("iii", $lead_id, $procedure_type, $process);

                if (!$sql->execute()) {
                    throw new Exception("Error al buscar la notificación de incisiones: " . $sql->error);
                }

                if ($result->num_rows == 0) {
                    $notifications .= "<div class='col-12 mb-4'>
                                        <button data-process=8 class='btn-notif-select btn btn-primary'>Término de incisiones</button>
                                    </div>";
                }

                // Habilitar envío de fotos de implantación
                $notifications .= "<hr>
                                    <div class='col-12 mb-4'>
                                        <button data-process=9.1 class='btn-notif-select btn btn-danger'>Enviar Hora de Implantación</button>
                                        <br><br>
                                        <button data-process=8 class='btn-notif-select btn btn-primary'>Término de incisiones</button>
                                    </div>";
            }
        }
    }

    $success = true;
    $message = "Done";
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "next_notif" => $notifications, "message" => $message, "procedure_type_recibido"=> $procedure_type]);

$conn->close();
?>
