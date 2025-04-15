<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {

  $task_id = $_POST['task_id'];

  $sql_row = "DELETE FROM sa_lead_tasks WHERE id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $task_id);

  if (!$sql->execute()) {
    throw new Exception("Error al eliminar la tarea. Contacta al administrador");
  }

  $sql_row = "DELETE FROM sa_lead_tasks_notif WHERE task_id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $task_id);

  if (!$sql->execute()) {
    throw new Exception("Error al eliminar los recordatorios. Contacta al administrador");
  }

  $success = true;
  $message = "Tarea eliminada correctamente";
} catch (Exception $e) {
  $success = false;
  $message = "Error:" . $e->getMessage();
}

$conn->close();

echo json_encode(['success' => $success, 'message' => $message]);
