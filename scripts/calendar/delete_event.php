<?php
//1
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  try {
    $event_id = $_POST['event_id'];

    $sql = "DELETE FROM sa_events WHERE id = ?;";

    $sql = $conn->prepare($sql);
    $sql->bind_param("i", $event_id);

    if ($sql->execute()) throw new Exception("Error al eliminar el evento: " . $sql->error);

    if ($sql->affected_rows === 0) throw new Exception("El evento ya no existe.");

    $sql = "DELETE FROM sa_evaluation_events WHERE event_id = ?;";
    $sql = $conn->prepare($sql);
    $sql->bind_param("i", $event_id);

    if ($sql->execute()) throw new Exception("Error al eliminar el evento: " . $sql->error);

    $success = true;
    $message = "Evento eliminado correctamente";
  } catch (Exception $e) {
    $success = false;
    $message = $e->getMessage();
  }

  echo json_encode(["success" => $success, "message" => $message]);
  $conn->close();
}
