<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

header('Content-Type: application/json');

require_once "../connection_db.php";
require_once "../../vendor/autoload.php"; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_POST['num_med_record'];
$sql = "SELECT t_a.id, t_a.date, t_a.clinic, t_a.doctor, t_a.type, t_a.notes FROM enf_treatments_appointments t_a WHERE t_a.num_med_record = {$num_med_record} ORDER BY t_a.date ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($data = $result->fetch_object()) {
        $treatments[] = array(
            'id' => $data->id,
            'date' => $data->date,
            'clinic' => $data->clinic,
            'doctor' => $data->doctor,      // Asegúrate de que $end esté en el formato correcto
            'type' => $data->type, // Opcional, para personalizar el color de fondo del evento
            'notes' => $data->notes
        );
    }
} else {
    $treatments = [];
}

echo json_encode(["success" => true, "treatments" => $treatments]);
