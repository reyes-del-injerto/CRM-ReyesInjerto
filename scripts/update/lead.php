<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require '../connection_db.php';

$lead_id = $_POST['id'];
$stage = $_POST['stage'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$clinic = $_POST['clinic'];
$origin = $_POST['origin'];
$phone = $_POST['phone'];
$interested_in = isset($_POST['interested_in']) ? $_POST['interested_in'] : '';
$quali = $_POST['qualif'];
$seller = $_POST['seller'];
$notes = $_POST['notes'];

$gender = "Hombre";
$status = 0;

try {
	// Inicia la transacción
	$conn->begin_transaction();

	$sql_row = "UPDATE sa_leads SET first_name = ?, last_name = ?, clinic = ?, origin = ?, phone = ?, interested_in = ?, stage = ?, quali = ?, seller = ?, notes = ? WHERE id = ?;";
	$sql = $conn->prepare($sql_row);
	$sql->bind_param("ssssssssssi", $first_name, $last_name, $clinic, $origin, $phone, $interested_in, $stage, $quali, $seller, $notes, $lead_id);

	/* Update Lead Stage and Info */
	if (!$sql->execute()) {
		throw new Exception("Contacta al Administrador");
	}

	/* Validate if assessment exists */
	if ($stage === "Dio anticipo") {
		$sql_row = "SELECT first_name, last_name, procedure_date, procedure_type, notes FROM sa_leads_assessment WHERE lead_id = ? AND status = 1";
		$stmt = $conn->prepare($sql_row);
		$stmt->bind_param("i", $lead_id);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows <= 0) {
			throw new Exception("Para crear el Cierre, realiza la Hoja de Valoración.");
		}
	}

	/* Create Closed Px Row */
	if ($stage === "Dio anticipo") {
		$status = 1;

		$sql_row = "INSERT INTO sa_closed_px (lead_id, status) VALUES (?, ?);";
		$sql = $conn->prepare($sql_row);
		$sql->bind_param("ii", $lead_id, $status);
		if (!$sql->execute()) {
			throw new Exception("Error al crear el cierre. Contacta al Administrador.");
		}
	}

	/* Successful Result */
	$conn->commit();

	if ($stage === "Dio anticipo") {
		echo json_encode(["success" => true, "message" => "Lead convertido a Cierre correctamente"]);
	} else {
		echo json_encode(["success" => true, "message" => "Datos actualizados correctamente."]);
	}
} catch (Exception $e) {
	$conn->rollback();
	echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
	$conn->close();
}
