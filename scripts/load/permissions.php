<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../common/connection_db.php';

$data_array = array();
$user_id = 1;
// SQL para obtener los datos
$sql = "SELECT id, cat, name, description, clinic FROM u_permissions WHERE clinic = 1;";

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
    $tdOptions =
        '<div class="dropdown dropdown-action">
		<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
		<div class="dropdown-menu dropdown-menu-end">
			<a class="dropdown-item btn-edit" data-permissionid="' . $data->id . '" href="#"><i class="fa-solid fa-pen-to-square m-r-5"></i>Ver o Editar</a>
			<a class="dropdown-item btn-delete" data-permissionid="' . $data->id . '" href="#"><i class="fa-solid fa-times m-r-5"></i>Eliminar</a>
		</div>
	</div>';

    switch ($data->clinic) {
        case 1:
            $clinic = "CDMX";
            break;
        case 2:
            $clinic = "Culiacán";
            break;
        case 3:
            $clinic = "Mazatlán";
            break;
        case 4:
            $clinic = "Tijuana";
            break;
    }

    $data_array[] = array(
        $data->id,
        $data->cat,
        $data->name,
        $data->description,
        $clinic,
        $tdOptions
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
