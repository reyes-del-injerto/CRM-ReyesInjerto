<?php
// Incluir la conexión a la base de datos
require_once "../../common/connection_db.php"; // Asegúrate de que este archivo contiene la función executeQuery

// Obtener las fechas del AJAX (usar POST en lugar de GET)
$start_date = isset($_POST['first_day_of_period']) ? $_POST['first_day_of_period'] : date('Y-m-01 00:00:00');
$end_date = isset($_POST['last_day_of_period']) ? $_POST['last_day_of_period'] : date('Y-m-t 23:59:59');

// Convertir a objetos DateTime para asegurarse de que están bien formateadas
$startDateTime = new DateTime($start_date);
$endDateTime = new DateTime($end_date);

// Función para ejecutar consultas SQL con manejo de errores
function executeQuery($conn, $sql) {
    try {
        $result = $conn->query($sql);
        if ($result === false) {
            throw new Exception($conn->error);
        }
        return $result;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// SQL para obtener totales y desglose por tipo de procedimiento
$sql = "
SELECT 
    (SELECT COUNT(*) FROM sa_leads WHERE created_at BETWEEN '$start_date' AND '$end_date') AS total_leads,
    (SELECT COUNT(*) FROM sa_leads_assessment WHERE date BETWEEN '$start_date' AND '$end_date') AS total_valoraciones,
    (SELECT COUNT(DISTINCT id) FROM sa_leads WHERE stage = 'Dio anticipo' AND created_at BETWEEN '$start_date' AND '$end_date') AS total_cierres,
    (SELECT COUNT(*) FROM sa_leads_assessment WHERE procedure_type = 'Capilar' AND date BETWEEN '$start_date' AND '$end_date') AS total_capilar,
    (SELECT COUNT(*) FROM sa_leads_assessment WHERE procedure_type = 'Ambos' AND date BETWEEN '$start_date' AND '$end_date') AS total_ambos,
    (SELECT COUNT(*) FROM sa_leads_assessment WHERE procedure_type = 'Barba' AND date BETWEEN '$start_date' AND '$end_date') AS total_barba,
    (SELECT COUNT(*) FROM sa_leads_assessment WHERE procedure_type = 'Micro' AND date BETWEEN '$start_date' AND '$end_date') AS total_micro
";

// Consulta para obtener los leads por vendedora y el desglose por qualy, además de los que están en "Agendó valoración" o "Valorado"
$sql_vendedoras = "
SELECT 
    seller, 
    COUNT(*) AS total_leads, 
    SUM(CASE WHEN stage = 'Dio anticipo' THEN 1 ELSE 0 END) AS leads_convertidos, 
    SUM(CASE WHEN quali IN ('Negociación', 'En negociación') THEN 1 ELSE 0 END) AS leads_negociacion,
    SUM(CASE WHEN quali = 'En conversación' THEN 1 ELSE 0 END) AS leads_conversacion,
    SUM(CASE WHEN quali = 'Seguimiento pre-proced.' THEN 1 ELSE 0 END) AS leads_pre_proced,
    SUM(CASE WHEN quali = 'Está comparando opciones' THEN 1 ELSE 0 END) AS leads_comparando,
    SUM(CASE WHEN quali = 'Interesado' THEN 1 ELSE 0 END) AS leads_interesado,
    SUM(CASE WHEN quali = 'Fuera de su presupuesto' THEN 1 ELSE 0 END) AS leads_fuera,
    SUM(CASE WHEN quali = 'Seguimiento' THEN 1 ELSE 0 END) AS leads_seguimiento,
    SUM(CASE WHEN stage IN ('Agendó valoración', 'Valorado') THEN 1 ELSE 0 END) AS leads_val
FROM sa_leads 
WHERE created_at BETWEEN '$start_date' AND '$end_date'
GROUP BY seller 
ORDER BY seller
";


// Consultar valoraciones por clínica
$sql_valoraciones_clinica = "
SELECT clinic, COUNT(*) AS total_valoraciones
FROM sa_leads_assessment
WHERE date BETWEEN '$start_date' AND '$end_date'
GROUP BY clinic
ORDER BY total_valoraciones DESC
";

// Consultar cantidad de leads por etapa
$sql_leads_por_etapa = "
SELECT stage, COUNT(*) AS total_leads
FROM sa_leads
WHERE created_at BETWEEN '$start_date' AND '$end_date'
GROUP BY stage
";

// Ejecutar las consultas y preparar los resultados
$response = [];

$result = executeQuery($conn, $sql);
if ($result) {
    $data = $result->fetch_assoc();
    $response['total_leads'] = $data['total_leads'];
    $response['total_valoraciones'] = $data['total_valoraciones'];
    $response['total_cierres'] = $data['total_cierres'];
    $response['total_capilar'] = $data['total_capilar'];
    $response['total_ambos'] = $data['total_ambos'];
    $response['total_barba'] = $data['total_barba'];
    $response['total_micro'] = $data['total_micro'];
}

// Ejecutar y almacenar resultados para leads por vendedoras
$result_vendedoras = executeQuery($conn, $sql_vendedoras);
$response['vendedoras'] = [];
if ($result_vendedoras) {
    while ($row = $result_vendedoras->fetch_assoc()) {
        $response['vendedoras'][] = $row;
    }
}

// Ejecutar y almacenar resultados para valoraciones por clínica
$result_valoraciones_clinica = executeQuery($conn, $sql_valoraciones_clinica);
$response['valoraciones'] = [];
if ($result_valoraciones_clinica) {
    while ($row = $result_valoraciones_clinica->fetch_assoc()) {
        $response['valoraciones'][] = $row;
    }
}

// Ejecutar y almacenar resultados para leads por etapa (para el gráfico)
$result_etapas = executeQuery($conn, $sql_leads_por_etapa);
$response['etapas'] = ['tags' => [], 'data' => []];
if ($result_etapas) {
    while ($row = $result_etapas->fetch_assoc()) {
        $response['etapas']['tags'][] = $row['stage'];          // Agrega la etapa como etiqueta
        $response['etapas']['data'][] = (int)$row['total_leads']; // Agrega el total de leads para esa etapa
    }
}

// Agregar fechas al response
$response['startDate'] = $startDateTime->format('d-m-Y');
$response['endDate'] = $endDateTime->format('d-m-Y');

// Enviar la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
