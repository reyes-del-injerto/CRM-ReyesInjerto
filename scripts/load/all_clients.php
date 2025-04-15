<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$data_clients = [];
$clinic = "CDMX";

$sql = "SELECT
					sla.lead_id,
					CONCAT(sla.first_name, ' ', sla.last_name) AS name,
					sla.procedure_type,
					DATE_FORMAT(sla.procedure_date, '%d/%m/%Y') AS procedure_date, 
					sl.seller,
					scp.status
        FROM sa_leads_assessment sla
        INNER JOIN sa_leads sl
        	ON sla.lead_id = sla.lead_id
        INNER JOIN sa_closed_px scp
        	ON sla.lead_id = scp.lead_id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$available_status = [
	0 => "<a class='dropdown-item cancelado' data-color='danger' data-status=0 href='#' >Cancelado</a>",
	1 => "<a class='dropdown-item proximo' data-color='warning' data-status=1 href='#' >Próximo</a>",
	2 => "<a class='dropdown-item asignar' data-color='info' data-status=2 href='#' >Asignar Exped.</a>"
];

$status_options = [
	2 => ["color" => "info", "class" => "status-green", "label" => "Exp. Asignado", "dropdown" => $available_status[0] . $available_status[1]],
	1 => ["color" => "warning", "class" => "status-orange", "label" => "Próximo", "dropdown" => $available_status[0] . $available_status[2]],
	0 => ["color" => "danger", "class" => "status-pink", "label" => "Cancelado", "dropdown" => $available_status[1]]
];

while ($data = $result->fetch_object()) {
	$link_name = "<a data-lead-id='{$data->lead_id}' href='#' type='button' class='single_procedure'>{$data->name}</a>";

	// $notes = strlen($data->notes) > 30 ? $data->notes . "..." : $data->notes;
	$status_color = $status_options[$data->status]['color'];
	$status_class = $status_options[$data->status]['class'];
	$status_label = $status_options[$data->status]['label'];
	$status_dropdown = $status_options[$data->status]['dropdown'];

	$status = "<div class='dropdown action-label'>
                <a data-color='{$status_color}' class='custom-badge {$status_class} dropdown-toggle' href='#' data-bs-toggle='dropdown' aria-expanded='false'>
                {$status_label}
                </a>
                <div class='dropdown-menu dropdown-menu-end client-status status-staff' data-lead-id={$data->lead_id}>
                    {$status_dropdown}
                </div>
            </div>";

	$data_clients[] = [
		$link_name,
		$data->procedure_type,
		$data->procedure_date,
		$data->seller,
		$status
	];
}

$response  = ["data" => $data_clients];

echo json_encode($response);
