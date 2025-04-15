<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
header('Content-Type: application/json');

session_start();
require_once '../../common/connection_db.php';

/* 
employee=6&
start=2024-06-12&
end=2024-06-16&
notes=end%20to%20date&
approved_by=El%20de%20arriba
*/

$employee_id = $_POST['employee'];
$start = $_POST['start'];
$end = $_POST['end'];
$notes = $_POST['notes'];
$approved_by = $_POST['approved_by'];

$modified_end = fixEndDate($end);

// Obtener el nombre del empleado (si no se encuentra, seguirá sin marcar error)
$employee_name = getEmployeeName($employee_id);

$requested_days = getRequestedDays($start, $end);
$available_days = getAvailableDays($employee_id);

$sub = $available_days - $requested_days;

$allowed = ($sub >= 0);
$allowed = true;

if ($allowed) {
    insertHolidays($employee_id, $start, $modified_end, $notes, $approved_by);
    updateAvailableDays($employee_id, $requested_days);

    $message = "Vacaciones agregadas";
    if ($employee_name) {
        $message .= " para trabajador $employee_name";
    }
    $message .= " en las fechas $start a $modified_end.";

    echo json_encode(['success' => true, 'message' => $message]);
} else {
    echo json_encode(['success' => false, 'message' => 'Los días solicitados exceden los días disponibles del empleado.']);
}

function updateAvailableDays($employee_id, $requested_days)
{
    global $conn;

    $sql_row = "UPDATE ad_employees SET used_days = used_days + ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);

    try {
        $sql->bind_param("ii", $requested_days, $employee_id);
        $sql->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error al actualizar las vacaciones: " . $e->getMessage());
        return false; // La consulta no se ejecutó correctamente
    }
}

function insertHolidays($employee_id, $start, $end, $notes, $approved_by)
{
    global $conn;

    $sql_row = "INSERT INTO ad_holidays (employee_id, start, end, notes, approved_by) VALUES (?, ?, ?, ?, ?)";
    $sql = $conn->prepare($sql_row);

    try {
        $sql->bind_param("issss", $employee_id, $start, $end, $notes, $approved_by);
        $sql->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error al insertar vacaciones: " . $e->getMessage());
        return false; // La consulta no se ejecutó correctamente
    }
}

function getRequestedDays($start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);

    $interval = $start->diff($end);

    return $interval->days;
}

function getAvailableDays($employee_id)
{
    global $conn;

    $sql = "SELECT (allowed_days - used_days) AS available_days FROM ad_employees WHERE id = $employee_id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["available_days"];
    } else {
        return 0;
    }
}

function getEmployeeName($employee_id)
{
    global $conn;

    $sql = "SELECT name FROM ad_employees WHERE id = ?";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['name'];
        } else {
            return null; // Si no se encuentra el nombre
        }
    } catch (Exception $e) {
        error_log("Error al obtener el nombre del empleado: " . $e->getMessage());
        return null;
    }
}

function fixEndDate($end)
{
    // Add 1 day to the end date due to fullcalendar configuration
    $object_date = new DateTime($end);
    $object_date->modify('+1 day');
    return $object_date->format('Y-m-d');
}
