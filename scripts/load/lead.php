<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../connection_db.php';
$lead_id = $_POST['lead_id'];

$tasks = [];

$sql = "SELECT id,created_at, first_name, last_name, clinic, origin, phone, interested_in, stage, quali, notes, link, seller FROM sa_leads WHERE id = ?;";

if ($stmt = $conn->prepare($sql)) {
	$stmt->bind_param("i", $lead_id);
	$stmt->execute();

	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$sql_tasks = "SELECT id,subject, comments, assigned_to,  DATE_FORMAT(end_date, '%d. %b %h:%i %p') end_date, created_by, status FROM sa_lead_tasks WHERE lead_id = ? ORDER BY end_date DESC;";
		if ($stmt_tasks = $conn->prepare($sql_tasks)) {
			$stmt_tasks->bind_param("i", $lead_id);
			$stmt_tasks->execute();
			$result_tasks = $stmt_tasks->get_result();

			// Guardar cada tarea en el arreglo $tasks
			while ($task = $result_tasks->fetch_assoc()) {
				$tasks[] = $task;
			}
			$stmt_tasks->close();
		}

		// Añadir las tareas al arreglo $row antes de codificarlo a JSON
		$row['tasks'] = $tasks;

		$jsonResult = json_encode($row);
		echo $jsonResult;
	} else {
		echo "No se encontraron resultados.";
	}

	// Cerrar statement
	$stmt->close();
} else {
	echo "Error preparando la consulta: " . $conn->error;
}

// Cerrar conexión
$conn->close();
