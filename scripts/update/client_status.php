<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require '../connection_db.php';

$lead_id = $_POST['lead_id'];
$status = $_POST['status'];

$success = false;
$exists = false;
$message = "Contacta a administración";

if ($status == "Cancelado") {
    $status = 0;

    $sql_row = "UPDATE sa_closed_px SET status = ? WHERE lead_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $status, $lead_id);

    if ($sql->execute()) {
        $message = "Procedimiento cancelado correctamente";
        $success = true;
    }
} else if ($status == "Asignar Exped.") {
    $num_med_record = $_POST['num_med_record'];

    $status = 2;

    $sql_row = "SELECT num_med_record FROM enf_procedures WHERE lead_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $lead_id);
    $sql->execute();
    $result = $sql->get_result();

    $exists = ($result->num_rows > 0);

    $sql_row = "UPDATE sa_closed_px SET status = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $status, $lead_id);

    if ($sql->execute()) {
        $sql_row = "INSERT INTO enf_procedures (lead_id, num_med_record) VALUES (?, ?);";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("ii", $lead_id, $num_med_record);
        if ($sql->execute()) {
            $message = 'Datos actualizados correctamente';
        }
    } else {
        $message = "av";
    }
    $success = true;
} else if ($status == "Próximo") {
    $status = 1;


    $sql_row = "UPDATE sa_closed_px SET status = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $status, $lead_id);

    if ($sql->execute()) {
        $success = true;
        $message = 'Cliente actualizado correctamente';
    }
} else if ($status == "Retoque") {
}


echo json_encode(['success' => $success, 'message' => $message, 'exist' => $exists]);





























/*





if ($type == "assign") {
    $sql = "SELECT enf_procedures.id FROM enf_procedures INNER JOIN sa_info_general_px ON enf_procedures.px_sales_id = sa_info_general_px.id WHERE enf_procedures.num_med_record = $data AND sa_info_general_px.clinic = 'CDMX';";
    $query = $conn->query($sql);

    if ($query->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El número de expediente ya existe']);
        exit;
    } else {
        $status = 2;
        $room = $specialist = $notes = '';

        $sql_row = "UPDATE sa_info_general_px SET status = ? WHERE id = ?;";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("ii", $status, $px_id);

        if ($sql->execute() && $sql->affected_rows > 0) {
            $sql_row = "INSERT INTO enf_procedures (px_sales_id, num_med_record, room, specialist, notes) VALUES (?, ?, ?, ?, ?);";
            $sql = $conn->prepare($sql_row);
            $sql->bind_param("iiiss", $px_id, $data, $room, $specialist, $notes);
            if ($sql->execute() && $sql->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente']);
                exit;
            }
        }
    }
} else if ($type == "cancel") {
}

echo json_encode(['success' => false, 'message' => 'Ocurrió un error. Contacta a administración']);
*/