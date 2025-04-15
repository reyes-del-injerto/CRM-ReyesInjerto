<?php

//! Permisos: 3 / Finanzas / Ver TODOS LOS EGRESOS
//! Permisos: 4 / Finanzas / Ver TODOS LOS INGRESOS
//! Permisos: 5 / Finanzas / Ver solo los EGRESOS subidos por el usuario
//! Permisos: 6 / Finanzas / Ver solo los INGRESOS subidos por el usuario
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();

require_once "../connection_db.php";

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
	default:
		$where_clinic = "";
		break;
}

$sql_transactions = "SELECT 
                        t.id,     
                        IF(LENGTH(t.description) > 40, CONCAT(LEFT(t.description, 40), '...'), t.description) AS description,
                        t.payment_method_id, t.amount, t.date, t.store, t.cat_id, t.clinic, cat.name cat_name 
                    FROM ad_transactions t 
                    LEFT JOIN ad_categories cat ON t.cat_id = cat.id 
                    WHERE t.date BETWEEN ? AND ? ";

if (!empty($where_clinic)) {
	$sql_transactions .= $where_clinic;
}

$sql_transactions .= " ORDER BY t.date DESC";

$stmt = $conn->prepare($sql_transactions);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$message = "No se mostraron transacciones";
		$transactions[] = $row;
		$total += $row['amount'];
	}
}

//! Array
echo json_encode([
	"success" => true,
	"transactions" => $transactions,
	"total" => $total,
	"message" => $message
]);
// Cierra la conexión
$stmt->close();

function parseDates($dates)
{
	// Dividir las fechas por el guion
	$dates = explode("-", $dates);

	// Obtener la fecha de inicio
	$raw_start_date = ltrim(rtrim($dates[0]));
	$object_start_date = DateTime::createFromFormat('d/m/Y', $raw_start_date);
	$start_date = $object_start_date->format('Y-m-d');

	// Obtener la fecha de fin
	$raw_end_date = ltrim(rtrim($dates[1]));
	$object_end_date = DateTime::createFromFormat('d/m/Y', $raw_end_date);
	$end_date = $object_end_date->format('Y-m-d');
	$end_date = $end_date . " 23:59:59";

	// Retornar las fechas
	return [$start_date, $end_date];
}
