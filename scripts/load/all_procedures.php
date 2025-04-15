<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$data_patients = [];
$clinic = "CDMX";

$sql = "SELECT 
		ep.lead_id, ep.num_med_record, ep.room, ep.specialist, ep.notes,
		CONCAT(sla.first_name, ' ', sla.last_name) AS name,
		sla.procedure_date,
		sla.procedure_type
	FROM enf_procedures ep
	INNER JOIN sa_leads_assessment sla";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
	while ($data = $result->fetch_object()) {
		$link_name = "<a data-procedureid='{$data->lead_id}' href='#' type='button' class='single_procedure'>{$data->name}</a>";

		$notes = ($data->notes != null && strlen($data->notes) > 30) ? $data->notes . "..." : $data->notes;

		$data_patients[] = [
			$data->procedure_date,
			$data->num_med_record,
			$link_name,
			$data->procedure_type,
			$data->room,
			$data->specialist,
			$notes
		];
	}
}
$response  = ["data" => $data_patients];

echo json_encode($response);
