<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
  $task_id = $_POST['task_id'];
  $status = 1;

  $sql_row = "UPDATE sa_lead_tasks SET status = ? WHERE id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("ii", $status, $task_id);

  if (!$sql->execute()) {
    throw new Exception("Error al marcar la tarea como completada. Contacta al administrador");
  }
  $status_reminder =  "Completada";

  $sql_row = "UPDATE sa_lead_tasks_notif SET status = ? WHERE task_id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("si", $status_reminder, $task_id);

  if (!$sql->execute()) {
    throw new Exception("Error al eliminar los recordatorios. Contacta al administrador");
  }

  $success = true;
  $message = "Tarea completada. Gracias! 🦁";
} catch (Exception $e) {
  $success = false;
  $message = "Error:" . $e->getMessage();
}

$conn->close();

$response = ["success" => $success, "message" => $message];
echo json_encode($response);
