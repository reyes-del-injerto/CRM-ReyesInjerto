<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../connection_db.php';

$lead_id = $_POST['lead_id'];
$message = "";
$status = 1;
$success = false;
$assessment = false;

$sql = "SELECT lead_id, date, first_name, last_name, procedure_date, procedure_type, closer, first_meet_type, clinic, type, notes, created_at, timestamp FROM sa_leads_assessment WHERE lead_id = ? AND status = ?;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $lead_id, $status);
if ($stmt->execute()) {
    $success = true;

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $assessment = json_encode($row);
    }
    $stmt->close();
} else {
    $message = "Error preparando la consulta" . $conn->error;
}

$response =  json_encode(["success" => $success, "assessment" => $assessment, "message" => $message]);

$conn->close();

echo $response;
