<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../../common/connection_db.php';

$data_array = [];
$clinic = isset($_GET['clinic']) ? $_GET['clinic'] : ''; // Obtener el filtro de clínica del GET

// Verificar si $clinic tiene un valor válido
if (empty($clinic)) {
    echo json_encode(['error' => 'El parámetro "clinic" es requerido.']);
    exit;
}

// Validar que $conn esté definido correctamente
if (!$conn) {
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}

// SQL para obtener los datos usando DISTINCT y el filtro de clínica
$sql = "SELECT DISTINCT id, num_progresivo, cuenta, importe, nombre, clinic 
        FROM ad_nomina 
        WHERE clinic = ? 
        ORDER BY num_progresivo ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Error en la preparación de la consulta.']);
    exit;
}

$stmt->bind_param("s", $clinic);
$stmt->execute();
$result = $stmt->get_result();

// Recorrer los resultados
while ($data = $result->fetch_object()) {
    $data_array[] = [
        'id' => $data->id,
        'num_progresivo' => $data->num_progresivo,
        'cuenta' => $data->cuenta,
        'importe' => $data->importe,
        'nombre' => $data->nombre,
        'clinic' => $data->clinic
    ];
}

// Crear el JSON a partir del array
echo json_encode(["data" => $data_array]);

$stmt->close();
$conn->close();
