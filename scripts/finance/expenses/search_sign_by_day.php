<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../../common/connection_db.php";

// Verificar si se recibieron los parámetros 'dia' y 'clinic'
if (isset($_POST['dia']) && isset($_POST['clinic'])) {
    $dia = $_POST['dia'];
    $clinic = $_POST['clinic'];

    // Preparar la consulta para obtener la firma en Base64
    $stmt = $conn->prepare("SELECT firma FROM daily_cortes WHERE dia = ? AND clinic = ?");
    $stmt->bind_param("ss", $dia, $clinic);
    $stmt->execute();
    $stmt->bind_result($firma);
    
    if ($stmt->fetch()) {
        // Si se encontró la firma
        echo json_encode(["success" => "Firma encontrada.", "firma" => $firma]);
    } else {
        // Si no se encontró la firma
        echo json_encode(["error" => "No se encontró firma para los parámetros dados."]);
    }

    $stmt->close();
} else {
    // Si faltan los parámetros
    echo json_encode(["error" => "Faltan los parámetros necesarios: dia o clinic"]);
}

$conn->close();
?>
