<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

$lead_id = $_POST['id'] ?? null;
$stage = $_POST['stage'] ?? '';
$first_name = $_POST['first_name'] ?? null;
$last_name = $_POST['last_name'] ?? null;
$clinic = $_POST['clinic'] ?? null;
$origin = $_POST['origin'] ?? null;
$phone = $_POST['phone'] ?? null;
$interested_in = $_POST['interested_in'] ?? '';
$seller = $_POST['seller'] ?? null;
$evaluator = $_POST['evaluator'] ?? '';
$notes = $_POST['notes'] ?? null;

try {
    if (!$lead_id) {
        throw new Exception("ID de lead no proporcionado.");
    }

    $conn->begin_transaction();

    /* Verificar si el lead existe antes de actualizar */
    $sql_check = "SELECT id FROM sa_leads WHERE id = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $lead_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("El lead no existe.");
    }

    /* Actualizar datos del lead */
    $sql_update = "UPDATE sa_leads SET first_name = ?, last_name = ?, clinic = ?, origin = ?, phone = ?, interested_in = ?, stage = ?, seller = ?, notes = ?, evaluator = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssssssssi", $first_name, $last_name, $clinic, $origin, $phone, $interested_in, $stage, $seller, $notes, $evaluator, $lead_id);

    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar los datos. Contacta al Administrador.");
    }

    /* Validar existencia de hoja de valoración si el lead avanza a "Dio anticipo" */
    if ($stage === "Dio anticipo") {
        $sql_check_assessment = "SELECT first_name FROM sa_leads_assessment WHERE lead_id = ? AND status = 1";
        $stmt = $conn->prepare($sql_check_assessment);
        $stmt->bind_param("i", $lead_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            throw new Exception("Para crear el Cierre, realiza la Hoja de Valoración.");
        }

        /* Verificar si el lead ya tiene un cierre registrado */
        $sql_check_closed = "SELECT id FROM sa_closed_px WHERE lead_id = ?";
        $stmt = $conn->prepare($sql_check_closed);
        $stmt->bind_param("i", $lead_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            /* Insertar el cierre solo si no existe */
            $status = 1;
            $sql_insert_closed = "INSERT INTO sa_closed_px (lead_id, status) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_insert_closed);
            $stmt->bind_param("ii", $lead_id, $status);
            if (!$stmt->execute()) {
                throw new Exception("Error al crear el cierre. Contacta al Administrador.");
            }
        }
    }

    /* Confirmar la transacción */
    $conn->commit();

    $success = true;
    $message = ($stage === "Dio anticipo") ? "Lead convertido a Cierre correctamente" : "Datos actualizados correctamente.";
} catch (Exception $e) {
    if ($conn && $conn->errno) {
        $conn->rollback();
    }
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message]);
exit;

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
