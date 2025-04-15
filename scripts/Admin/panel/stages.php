<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');

session_start();
require_once "../../common/connection_db.php"; // Asegúrate de que este archivo contiene la función executeQuery

// Función para ejecutar consultas SQL
function executeQuery($conn, $sql) {
    // Ejecutar la consulta SQL y devolver el resultado
    return $conn->query($sql);
}

// Obtener fechas del POST
$first_day_of_period = isset($_POST['first_day_of_period']) ? $_POST['first_day_of_period'] : null;
$last_day_of_period = isset($_POST['last_day_of_period']) ? $_POST['last_day_of_period'] : null;

// Validar que se hayan proporcionado ambas fechas
if ($first_day_of_period && $last_day_of_period) {
    try {
        // Consultar las etapas de leads y contarlas
        $sql_leads_stages = "
        SELECT stage, COUNT(*) AS count
        FROM sa_leads
        WHERE created_at BETWEEN '$first_day_of_period' AND '$last_day_of_period'
        GROUP BY stage
        ";
        $result_leads_stages = executeQuery($conn, $sql_leads_stages);

        $labels = [];
        $data = [];

        while ($row = $result_leads_stages->fetch_assoc()) {
            $labels[] = $row['stage'];
            $data[] = $row['count'];
        }

        // Preparar la respuesta en JSON
        $response = [
            'status' => 'success',
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ];
    } catch (Exception $e) {
        // Manejo de errores
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
} else {
    // Fechas no proporcionadas
    $response = [
        'status' => 'error',
        'message' => 'Se requieren ambas fechas.'
    ];
}

// Devolver la respuesta en JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
