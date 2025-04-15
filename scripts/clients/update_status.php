<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
    $lead_id = $_POST['lead_id'];
    $current_status = $_POST['current_status_val'];
    $chosen_status = $_POST['chosen_status_lbl'];
    $success = false;
    $exists = false;

    // Obtener valor de 'clinic' desde la tabla 'sa_leads_assessment'
    $sql_row = "SELECT clinic FROM sa_leads_assessment WHERE lead_id = ?";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $lead_id);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clinic = $row['clinic'];
    } else {
        throw new Exception("Error: No se encontró el clinic para el lead_id proporcionado.");
    }

    // Casos de Exp. Asignado
    if ($current_status == 2) {
        if ($chosen_status == "Cancelado") {
            $status = 0;

            $sql_row = "DELETE FROM enf_procedures WHERE lead_id = ?";
            $sql = $conn->prepare($sql_row);
            $sql->bind_param("i", $lead_id);

            if (!$sql->execute()) {
                throw new Exception("Error al eliminar de Procedimientos. Contacta al Administrador");
            }

            $message = "Eliminado de Cliente y Procedimiento correctamente.";
        }

        if ($chosen_status == "Próximo") {
            $status = 1;
            $message = "Cliente actualizado a Próximo correctamente.";
        }

        $sql_row = "UPDATE sa_closed_px SET status = ? WHERE lead_id = ?";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("ii", $status, $lead_id);

        if (!$sql->execute()) {
            throw new Exception("Error al actualizar el status. Contacta al Administrador");
        }
    }

    // Casos de Próximo
    if ($current_status == 1) {
        if ($chosen_status == "Asignar Exped.") {
            $num_med_record = $_POST['num_med_record'];
            $status = 2;

            // Verificar si el expediente ya existe
            $sql_row = "SELECT lead_id FROM enf_procedures WHERE num_med_record = ?";
            $sql = $conn->prepare($sql_row);
            $sql->bind_param("i", $num_med_record);
            $sql->execute();
            $result = $sql->get_result();

            $touchup = ($result->num_rows > 0) ? 1 : 0;

            // Actualizar status en sa_closed_px
            $sql_row = "UPDATE sa_closed_px SET status = ? WHERE lead_id = ?";
            $sql = $conn->prepare($sql_row);
            $sql->bind_param("ii", $status, $lead_id);

            if (!$sql->execute()) {
                throw new Exception("Error al cambiar el status del cliente. Contacta al Administrador");
            }

            // Insertar en enf_procedures, incluyendo el campo clinic
            $sql_row = "INSERT INTO enf_procedures (lead_id, clinic, num_med_record, touchup, room, specialist, notes) 
                        VALUES (?, ?, ?, ?, 0, '', '');";
            $sql = $conn->prepare($sql_row);
            $sql->bind_param("isii", $lead_id, $clinic, $num_med_record, $touchup);

            if (!$sql->execute()) {
                throw new Exception("Error al asignar número de expediente. Contacta al Administrador");
            }
            $message = "Expediente asignado al cliente correctamente";
        }

        if ($chosen_status == "Próximo") {
            $status = 0;
            $sql_row = "UPDATE sa_closed_px SET status = ? WHERE lead_id = ?";
            $sql = $conn->prepare($sql_row);
            $sql->bind_param("ii", $status, $lead_id);

            if (!$sql->execute()) {
                throw new Exception("Error al cancelar el paciente. Contacta al Administrador");
            }

            $message = "Cliente cancelado correctamente.";
        }
        $success = true;
    }

    // Casos de Cancelado
    if ($current_status == 0) {
        $status = 1;

        $sql_row = "UPDATE sa_closed_px SET status = ? WHERE lead_id = ?";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("ii", $status, $lead_id);

        if (!$sql->execute()) {
            throw new Exception("Error al actualizar el status. Contacta al Administrador");
        }
        $message = "Cliente actualizado a Próximo correctamente.";
    }

    $success = true;
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

$conn->close();
echo json_encode(['success' => $success, 'message' => $message]);
