<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../common/connection_db.php";

// Verificar si los parámetros POST han sido enviados
if (isset($_POST['num_med_record']) && isset($_POST['phase']) && isset($_POST['author']) && isset($_POST['note']) && isset($_POST['date'])) {
    // Obtener los datos del formulario
    $num_med_record = $_POST['num_med_record'];
    $phase = $_POST['phase'];
    $author = $_POST['author'];
    $note = $_POST['note'];
    $date = $_POST['date'];
    $clinic = $_POST['clinic'];
    $procedure_type = $_POST['procedure_type']; 

    // Preparar la consulta SQL para insertar los datos
    $sql = "INSERT INTO medical_notes (num_med, phase, note, date, author, procedure_type,clinic) VALUES (?, ?, ?, ?, ?,?,?)";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param('isssiss', $num_med_record, $phase, $note, $date, $author,$procedure_type,$clinic); // 'i' para enteros, 's' para strings

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
                'message' => 'Error al guardar la nota.'
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
    // Si faltan parámetros
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan parámetros necesarios.'
    ]);
}

// Cerrar la conexión
$conn->close();
?>
