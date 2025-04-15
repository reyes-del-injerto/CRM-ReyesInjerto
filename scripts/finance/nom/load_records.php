<?php
header('Content-Type: application/json');

// Configuración de la conexión a la base de datos
require_once '../../common/connection_db.php';


// Obtener el parámetro 'clinic' de la consulta
$clinic = isset($_GET['clinic']) ? $conn->real_escape_string($_GET['clinic']) : '';

// Consulta para obtener los registros, filtrando por clínica si se proporciona
$query = "SELECT `id`, `num_progresivo`, `cuenta`, `importe`, `nombre`, `quincena`, `clinic` FROM `ad_nomina` WHERE 1";
if ($clinic) {
    $query .= " AND `clinic` = '$clinic'";
}

$result = $conn->query($query);

// Crear un array para almacenar los resultados
$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'num_progresivo' => $row['num_progresivo'],
            'cuenta' => $row['cuenta'],
            'importe' => $row['importe'],
            'nombre' => $row['nombre'],
            'quincena' => $row['quincena'],
            'clinic' => $row['clinic']
        ];
    }
}

// Cerrar la conexión
$conn->close();

// Devolver la respuesta en formato JSON
echo json_encode(['data' => $data]);
?>
