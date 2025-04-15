<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../common/connection_db.php";

// Lista de parámetros requeridos
$required_params = [
    'identifier',
    'identifier_type',
    'phase',
    'author',
    'note',
    'date'
];

$missing_params = [];

// Verificar si los parámetros POST han sido enviados
foreach ($required_params as $param) {
    if (!isset($_POST[$param])) {
        $missing_params[] = $param;
    }
}

if (empty($missing_params)) {
    // Obtener los datos del formulario
    $identifier = $_POST['identifier']; // Puede ser num_med o id
    $identifier_type = $_POST['identifier_type']; // 'num_med' o 'id'
    $identifier_type =strtolower($identifier_type); // a minusculas
    $phase = $_POST['phase'];
    $author = $_POST['author'];
    $note = $_POST['note'];
    $date = $_POST['date'];
    

    // Preparar la consulta SQL para insertar los datos en la tabla `enf_treatments_notes`
    $sql = "INSERT INTO enf_treatments_notes (identifier, identifier_type, phase, note, date, author) VALUES (?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param('ssssss', $identifier, $identifier_type, $phase, $note, $date, $author); // 's' para strings

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Consulta exitosa
            echo json_encode([
                'status' => 'success',
                'message' => 'Nota guardada correctamente.'
            ]);
        } else {
            // Error en la ejecución de la consulta
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al guardar la nota: ' . $stmt->error
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
