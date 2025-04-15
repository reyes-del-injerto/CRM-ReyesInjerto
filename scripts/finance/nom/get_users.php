<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../../common/connection_db.php';

// Array para almacenar los usuarios
$users_array = array();

// SQL para obtener los usuarios (asumo que "ad_nomina" contiene la lista de usuarios con sus respectivos nombres)
$sql = "SELECT  id, nombre FROM ad_nomina ORDER BY nombre ASC";

// Ejecutar la consulta
$query = $conn->query($sql);

// Recorrer los resultados
while ($user = $query->fetch_object()) {
    // Agregar cada usuario al array como un objeto con id y nombre
    $users_array[] = array(
        "id" => $user->id,
        "nombre" => $user->nombre
    );
}

// Devolver el JSON con la lista de usuarios
echo json_encode($users_array);

?>
