<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/utilities.php";
require_once __DIR__ . "/../../common/connection_db.php";

$message = "No se obtuvieron transacciones";
$transactions = [];
$total = 0;

$dates = $_POST['dates'];
[$start_date, $end_date] = parseDates($dates);

switch ($_POST['clinic']) {
	case 'Santafe':
		$where_clinic = "AND clinic = 'Santafe' ";
		break;
	case 'Pedregal':
		$where_clinic = "AND clinic = 'Pedregal' ";
		break;
	case 'Queretaro': // Agregando la clínica de Querétaro
		$where_clinic = "AND clinic = 'Queretaro' ";
		break;
	case 'Ambas':
		$where_clinic = "";
		break;
}

$sql_transactions = "SELECT 
						t.id, 
						IF(LENGTH(t.description) > 40, CONCAT(LEFT(t.description, 40), '...'), t.description) AS description, 
						t.payment_method_id, 
						t.amount, 
						DATE_FORMAT(t.date, '%d/%m') AS date, 
						t.store, 
						t.cat_id, 
						t.clinic, 
						cat.name cat_name 
						FROM ad_transactions t 
						LEFT JOIN ad_categories cat ON t.cat_id = cat.id 
						WHERE t.date BETWEEN ? AND ?  ";

if (!empty($where_clinic)) {
	$sql_transactions .= $where_clinic;
}

$sql_transactions .= " ORDER BY t.date DESC";

$stmt = $conn->prepare($sql_transactions);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
	while ($data = $result->fetch_object()) {
		$transactions[] = [
			$data->id,
			$data->description,
			formatMXN($data->amount),
			$data->cat_name,
			getPaymentMethod($data->payment_method_id),
			$data->date,
			$data->clinic,
			transactionOptions($data->id)
		];
		$total += $data->amount;
	}
	$message = "Done";
}

echo json_encode([
	"success" => true, "data" => $transactions, "total" => $total, "message" => $message
]);

$stmt->close();


function parseDates($dates)
{
	$dates = explode("-", $dates);

	//Start Date
	$raw_start_date = ltrim(rtrim($dates[0]));
	$object_start_date = DateTime::createFromFormat('d/m/Y', $raw_start_date);
	$start_date = $object_start_date->format('Y-m-d');

	// End Date
	$raw_end_date = ltrim(rtrim($dates[1]));
	$object_end_date = DateTime::createFromFormat('d/m/Y', $raw_end_date);
	$end_date = $object_end_date->format('Y-m-d');
	$end_date = $end_date . " 23:59:59";

	return [$start_date, $end_date];
}

function getPaymentMethod($payment_method)
{	
	

	if ($payment_method == 1) {
		$color = "bg-primary-subtle";
		$icon = "fa fa-money-bill";
		$payment_method = "Efectivo";
	} else if ($payment_method == 2) {
		$color = "bg-danger";
		$icon = "fa fa-credit-card";
		$payment_method = "Tarjeta";
	} else if ($payment_method == 3) {
		$color = "bg-warning-subtle";
		$icon = "fa fa-university";
		$payment_method = "Transferencia";
	} else if ($payment_method == 4) {
		$color = "bg-success-subtle";
		$icon = "fa fa-download";
		$payment_method = "Depósito";
	}

	return "<span class='badge text-dark {$color}'>
					{$payment_method} <span class='{$icon}'></span>
					</span>";
}

function transactionOptions($transaction_id)
{
	return "<button type='button' class='btn btn-rounded btn-outline-success edit' data-transaction-id={$transaction_id}><i class='fa fa-pencil'></i></button>
	<button type='button' class='btn btn-rounded btn-outline-danger delete' data-transaction-id={$transaction_id}><i class='fa fa-times'></i></button>";
	// <a class="dropdown-item" data-type="view" href="#"><i class="fa-solid fa-eye m-r-5"></i>Ver más info</a>
}
function formatMXN($amount)
{
	return '$' . number_format($amount * -1, 2, '.', ',');
}
