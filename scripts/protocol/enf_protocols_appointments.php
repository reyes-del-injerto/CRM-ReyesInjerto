<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

/* 
num_med_record=190&
date=2024-06-14&
type=alquiler&
clinic=Santa%20Fe&
inv_type=5&
doctor=Dra.%20Ana%20Karen&
notes=test


 */

session_start();
require_once "../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recolectar los datos del POST
    $num_med_record = $_POST['num_med_record'];
    $date = $_POST['date'];
    $clinic = $_POST['clinic'];
    $inv_type = $_POST['inv_type'];
    $doctor = $_POST['doctor'];
    $type = $_POST['type'];
    $notes = $_POST['notes'];
    $created_by = $_SESSION['user_id'];
    // Prepara la consulta
    $sql = $conn->prepare("INSERT INTO  enf_protocols_appointments  (num_med_record, date, clinic, doctor, type, inv_type, notes, created_by) VALUES (?, ?, ?,?, ?, ?, ?, ?)");
    // Vincula los parámetros
    $sql->bind_param("issssssi", $num_med_record, $date, $clinic, $doctor, $type, $inv_type, $notes, $created_by);

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
