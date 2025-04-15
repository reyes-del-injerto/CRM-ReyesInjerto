<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

/* 
num_med_record=1010
&user_id_protocol=1
&date=2024-07-12
&clinic=Santa%20Fe
&inv_type=1
&doctor=Dra.%20Amairani%20Romero
&month=1
&notes=asdadsadasd
*/

session_start();
require_once "../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recolectar los datos del POST
    $num_med_record = $_POST['num_med_record'] ?? null;
    $date = $_POST['date'] ?? null;
    $clinic = $_POST['clinic'] ?? null;
    $inv_type = $_POST['inv_type'] ?? null;
    $doctor = $_POST['doctor'] ?? null;
    $type = $_POST['type'] ?? null;
    $notes = $_POST['notes'] ?? null;
    $month = $_POST['month'] ?? null;
    $created_by = $_POST['user_id_protocol'] ?? null;

    // Validar que todos los campos requeridos estén presentes
    if (!$num_med_record || !$date || !$clinic || !$inv_type || !$doctor || !$type || !$notes || !$month || !$created_by) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    // Prepara la consulta
    $sql = $conn->prepare("INSERT INTO enf_protocols_appointments (num_med_record, date, clinic, doctor, type, inv_type, notes, created_by, month) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Vincula los parámetros
    $sql->bind_param("issssssss", $num_med_record, $date, $clinic, $doctor, $type, $inv_type, $notes, $created_by, $month);

    // Ejecuta la consulta
    if ($sql->execute()) {
        echo json_encode(['success' => true, 'message' => 'Tratamiento añadido']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $sql->error]);
    }

    // Cierra la conexión
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
}
?>
