<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../common/utilities.php";
require_once __DIR__ . "/../common/connection_db.php";

$lead_id = $_POST['lead_id'];
$lead_info = [];
$lead_tasks = [];
$num_med_record = null; // Inicializar la variable con un valor por defecto

try {
  // lead info
  $sql_row = "SELECT id, created_at, first_name, last_name, clinic, origin, phone, interested_in, stage, quali, notes, link, seller,evaluator FROM sa_leads WHERE id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $lead_id);

  if (!$sql->execute()) {
    throw new Exception("Error al obtener la info. del lead: " . $sql->error);
  }
  $result = $sql->get_result();

  if ($result->num_rows != 1) {
    throw new Exception("No encuentro al lead o hay dos con el mismo ID. Contacta al administrador");
  }

  $lead_info = $result->fetch_assoc();

  // lead num med record
  $sql_row = "SELECT `num_med_record` FROM `enf_procedures` WHERE lead_id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $lead_id);

  if (!$sql->execute()) {
    throw new Exception("Error al obtener la info. del lead: " . $sql->error);
  }
  $result = $sql->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $num_med_record = $row['num_med_record'];
  }

  // lead tasks
  $sql_tasks = "SELECT id, subject, comments, assigned_to, DATE_FORMAT(end_date, '%d. %b %h:%i %p') end_date, created_by, status FROM sa_lead_tasks WHERE lead_id = ? ORDER BY end_date DESC;";
  $sql = $conn->prepare($sql_tasks);
  $sql->bind_param("i", $lead_id);

  if (!$sql->execute()) {
    throw new Exception("Error al obtener las tareas del lead: " . $sql->error);
  }
  $result = $sql->get_result();

  // Guardar cada tarea en el arreglo $tasks
  while ($task = $result->fetch_assoc()) {
    $lead_tasks[] = $task;
  }

  $message = "Done";
  $success = true;
} catch (Exception $e) {
  $success = false;
  $message = "Error: " . $e->getMessage();
}

echo json_encode([
  "success" => $success,
  "lead_info" => $lead_info,
  "lead_tasks" => $lead_tasks,
  "lead_num_med_record" => $num_med_record,
  "message" => $message
]);

$conn->close();
?>
