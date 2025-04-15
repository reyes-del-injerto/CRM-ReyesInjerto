<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once "../connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $action = "complete";
    $status = 1;

    $sql_row = "UPDATE sa_lead_tasks SET status = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $status, $task_id);

    $success = ($sql->execute()) ? true : false;

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Datos actualizados correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ocurrió un error. Conta a administración.'
        ]);
    }

    $sql->close();
}
