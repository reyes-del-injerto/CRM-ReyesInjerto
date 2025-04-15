<?php
// Configuración de errores
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../common/connection_db.php';

$data_array = array();

// SQL ajustado para agrupar por belongs_to y fecha, y contar los registros
$sql = "
    SELECT 
        p.belongs_to, 
        DATE(p.date) as fecha, 
        COUNT(p.id) as total_registros, 
        u.nombre as created_by 
    FROM 
        photos_register p 
    INNER JOIN 
        usuarios u ON p.created_by = u.id 
    GROUP BY 
        p.belongs_to, DATE(p.date), u.nombre 
    ORDER BY 
        fecha DESC, p.belongs_to ASC 
    LIMIT 100
";

// Ejecutar la consulta
$query = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$query) {
    echo json_encode(array("error" => $conn->error)); // Manejo de errores
    exit;
}

// Recorrer los resultados
while ($data = $query->fetch_object()) {
    $data_array[] = array(
        "belongs_to" => $data->belongs_to,  // Nombre del campo agrupado
        "fecha" => $data->fecha,            // Fecha agrupada
        "created_by" => $data->created_by,  // Nombre del usuario de la tabla `usuarios`
        "total_registros" => $data->total_registros // Total de registros para cada combinación de belongs_to y fecha
    );
}

// Crear un array con los datos dentro de "data"
$new_array  = array("data" => $data_array);

// Crear el JSON a partir de los arrays
echo json_encode($new_array);
?>
