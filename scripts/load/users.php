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
$sql = "SELECT id, nombre, usuario, DATE_FORMAT(ultimo_acceso, '%d-%m-%Y %H:%i:%s') AS ultimo_acceso FROM usuarios;";

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while ($data = $query->fetch_object()) {
    $tdOptions =
        '<div class="dropdown dropdown-action">
		<a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
		<div class="dropdown-menu dropdown-menu-end">
			<a class="dropdown-item" href="edit_user.php?user_id=' . $data->id . '"><i class="fa-solid fa-pen-to-square m-r-5"></i>Ver o Editar</a>
			<a class="dropdown-item" href="delete_user.php?user_id=' . $data->id . '"><i class="fa-solid fa-times m-r-5"></i>Eliminar</a>
		</div>
	</div>';

    $data_array[] = array(
        $data->id,
        $data->nombre,
        $data->usuario,
        $data->ultimo_acceso,
        $tdOptions
    );
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data" => $data_array);
// crear el JSON apartir de los arrays
echo json_encode($new_array);
