<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require '../connection_db.php';

$px_id = $_POST['px_id'];
$type = $_POST['type'];
$data = $_POST['data'];

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
    $status = 0;

    $sql_row = "UPDATE sa_info_general_px SET status = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $status, $px_id);

    if ($sql->execute()) {
        $sql_row = "UPDATE sa_info_procedure_px SET purpose = CONCAT(purpose, ' ', ?) WHERE px_general_id = ?;";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("si", $data, $px_id);
        if ($sql->execute()) {
            echo json_encode(['success' => true, 'message' => 'Procedimiento cancelado correctamente']);
            exit;
        }
    }
} else if ($type == "reactivate") {
    $status = 1;
    $sql_row = "UPDATE sa_info_general_px SET status = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $status, $px_id);

    if ($sql->execute()) {
        $sql_row = "UPDATE sa_info_procedure_px SET procedure_date = ? WHERE px_general_id = ?;";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("si", $data, $px_id);
        if ($sql->execute()) {
            echo json_encode(['success' => true, 'message' => 'Procedimiento cancelado correctamente']);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Ocurrió un error. Contacta a administración']);
