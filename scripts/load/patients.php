<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$data_array = array();
// SQL para obtener los datos
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) full_name, phone_1, DATE_FORMAT(procedure_date, '%d/%m/%Y') procedure_date, temp_num_med_record FROM px_sales WHERE status = 0;";

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
	$tdOptions =
		'<div class="dropdown dropdown-action">
		<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
		<div class="dropdown-menu dropdown-menu-end">
			<a class="dropdown-item" href="edit_appointment.php?px_id=' . $data->id . '"><i class="fa-solid fa-pen-to-square m-r-5"></i>Ver o Editar</a>
			<a class="dropdown-item" href="fotos_valoracion.php?px_id=' . $data->id . '"><i class="fa-solid fa-image m-r-5"></i>Cargar Fotos Valoración</a>
		</div>
	</div>';

	$data_array[] = array(
		$data->temp_num_med_record,
		$data->full_name,
		$data->phone_1,
		$data->procedure_date,
		$tdOptions
	);
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
