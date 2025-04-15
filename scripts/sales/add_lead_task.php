<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
  /* if (!isset($_SESSION['user_id'])) {
    throw new Exception("Error al obtener datos de tu usuario. Por favor inicia sesión de nuevo.");
  } */

  $user_id = $_POST['seller_id'];

  $lead_id = $_POST['lead_id'];
  $subject = $_POST['subject'];
  $comments = $_POST['comments'];
  $seller_id = $_POST['seller_id'];
  $end_datetime = str_replace("T", " ", $_POST['end_datetime']);

  $sql = $conn->prepare("INSERT INTO sa_lead_tasks (lead_id, subject, comments, assigned_to, end_date, created_by, created_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)");

  $sql->bind_param("issisi", $lead_id, $subject, $comments, $seller_id, $end_datetime, $user_id);

  if (!$sql->execute()) {
    throw new Exception("Error al insertar los datos de la tarea.");
  }

  $task_id = $conn->insert_id;

  $reminder_at_the_moment = set_reminder($conn, $end_datetime, $task_id, "Pendiente");
  if (!$reminder_at_the_moment) {
    throw new Exception("Error al configurar recordatorio.");
  }

  $message = "Tarea añadida correctamente";

  if (isset($_POST['reminder'])) {
    $reminders = $_POST['reminder'];
    $time = isset($_POST['time']) ? $_POST['time'] : '09:00';

    schedule_reminders($conn, $task_id, $end_datetime, $reminders, $time);

    $message = "Tarea y recordatorios añadidos correctamente";
  }

  $sql->close();
  $response = ["success" => true, "message" => $message];

} catch (Exception $e) {
  $response = ["success" => false, "message" => "Error: " . $e->getMessage()];
}

function set_reminder($conn, $datetime, $task_id, $status)
{
  $sql = $conn->prepare("INSERT INTO sa_lead_tasks_notif (task_id, datetime, status) VALUES (?, ?, ?)");
  if (!$sql) {
    return false;
  }
  $sql->bind_param("iss", $task_id, $datetime, $status);
  return $sql->execute();
}

function schedule_reminders($conn, $task_id, $end_datetime, $reminders, $time)
{
  foreach ($reminders as $reminder) {
    if ($reminder === "in_the_morning") {
      $scheduled_time = explode(" ", $end_datetime);
      $scheduled_datetime = $scheduled_time[0] . " " . $time . ":00";
    } elseif ($reminder === "one_day_before") {
      $scheduled_timestamp = strtotime($end_datetime);
      $scheduled_datetime = date("Y-m-d H:i", strtotime("-1 day", $scheduled_timestamp));
    } elseif ($reminder === "one_week_before") {
      $scheduled_date = explode(" ", $end_datetime);
      $scheduled_date = $scheduled_date[0];
      $scheduled_timestamp = strtotime($scheduled_date);
      $scheduled_datetime = date("Y-m-d", strtotime("-1 week", $scheduled_timestamp)) . " " . date("H:i:s", strtotime($end_datetime));
    }

    set_reminder($conn, $scheduled_datetime, $task_id, "Pendiente");
  }
}

echo json_encode($response);
