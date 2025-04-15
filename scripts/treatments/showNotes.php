<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../common/connection_db.php";

// Función para convertir la fecha al formato español
function formatDateToSpanish($date) {
    $months = [
        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ago', 9 => 'Sept', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
    ];

    $dt = new DateTime($date);
    $day = $dt->format('d');
    $month = $months[(int)$dt->format('m')];
    $year = $dt->format('Y');

    return "$day de $month, $year";
}

// Lista de parámetros requeridos
$required_params = ['identifier', 'type_identifier', 'phase'];
$missing_params = [];

// Verificar si los parámetros POST han sido enviados
foreach ($required_params as $param) {
    if (!isset($_POST[$param])) {
        $missing_params[] = $param;
    }
}

if (empty($missing_params)) {
    $identifier = $_POST['identifier']; // Puede ser un valor de tipo varchar
    $identifier_type = $_POST['type_identifier']; // 'exp' o 'id'
    $phase = $_POST['phase']; // Puede ser nulo según la estructura

    // Consulta SQL con filtro por identifier y phase
    $sql = "SELECT id, identifier, identifier_type, phase, note, date, author, procedure_type 
            FROM enf_treatments_notes 
            WHERE identifier = ? AND identifier_type = ? AND phase = ?";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param('sss', $identifier, $identifier_type, $phase); // 's' para strings

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            $data = [];

            // Extraer los datos y almacenarlos en un array
            while ($row = $result->fetch_assoc()) {
                // Convertir la fecha al formato español
                $row['date'] = formatDateToSpanish($row['date']);

                // Obtener el nombre del autor
                $authorId = $row['author'];
                $authorSql = "SELECT nombre FROM usuarios WHERE id = ?";
                
                if ($authorStmt = $conn->prepare($authorSql)) {
                    $authorStmt->bind_param('i', $authorId);
                    $authorStmt->execute();
                    $authorResult = $authorStmt->get_result();

                    if ($authorResult->num_rows > 0) {
                        $authorRow = $authorResult->fetch_assoc();
                        $row['author_name'] = $authorRow['nombre'];
                    } else {
                        $row['author_name'] = 'Desconocido';
                    }

                    $authorStmt->close();
                } else {
                    $row['author_name'] = 'Error en la consulta de autor';
                }

                $data[] = $row;
            }

            // Devolver los datos en formato JSON
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } else {
            // Si no hay resultados, devolver un mensaje
            echo json_encode([
                'status' => 'no_results',
                'message' => 'No se encontraron registros con los filtros proporcionados.'
            ]);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Error en la preparación de la consulta
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la preparación de la consulta.'
        ]);
    }
} else {
    // Si faltan parámetros, retornar cuáles
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan parámetros necesarios: ' . implode(', ', $missing_params)
    ]);
}

// Cerrar la conexión
$conn->close();
?>
