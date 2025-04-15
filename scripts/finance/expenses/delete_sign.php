<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../../common/connection_db.php";

// Verificar si se recibieron los datos necesarios
if (isset($_POST['dia']) && isset($_POST['clinic'])) {
    $dia = $_POST['dia'];
    $clinic = $_POST['clinic'];

    // Preparar la consulta para eliminar la firma del día y clínica especificados
    $stmt = $conn->prepare("DELETE FROM daily_cortes WHERE dia = ? AND clinic = ?");
    $stmt->bind_param("ss", $dia, $clinic);

    if ($stmt->execute()) {
        // Si la eliminación fue exitosa
        echo json_encode(["success" => true]);
    } else {
        // Si hubo un error al eliminar
        echo json_encode(["error" => "Error al eliminar los datos: " . $stmt->error]);
    }

    $stmt->close();
} else {
    // Especificar cuál dato falta
    $faltantes = [];
    if (!isset($_POST['dia'])) $faltantes[] = 'dia';
    if (!isset($_POST['clinic'])) $faltantes[] = 'clinic'; // Verificar si falta el campo `clinic`

    echo json_encode(["error" => "Faltan datos: " . implode(", ", $faltantes)]);
}

$conn->close();
?>
