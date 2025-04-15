<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once '../common/connection_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*  num_med_record=1&
   name=Eliezer%20Solano%20Martinez
    */



    // Recolectar los datos del POST
    $num_med_record = $_POST['num_med_record'];
    $name = $_POST['name'];
    $sex = $_POST['sex'];

    // Prepara la consulta
    $sql = $conn->prepare("INSERT INTO enf_protocols (num_med_record, name,sex ) VALUES (?, ?, ? )");
    // Vincula los parámetros
    $sql->bind_param("iss", $num_med_record, $name, $sex);

    // Ejecuta la consulta
    if ($sql->execute()) {
        echo json_encode(['success' => true, 'message' => 'Tratamiento añadido']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error']);
    }
    // Cierra la conexión
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
}