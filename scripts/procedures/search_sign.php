<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../common/connection_db.php";

// Verificar si se recibió la fase y el num_med
if (isset($_POST['fase']) && isset($_POST['num_med'])) {
    $fase = $_POST['fase'];
    $num_med = $_POST['num_med'];
    $clinic = $_POST['clinic'];

    // Preparar la consulta para obtener la firma en Base64
    $stmt = $conn->prepare("SELECT url FROM enf_signs WHERE num_med = ? AND step = ? AND clinic = ?");
    $stmt->bind_param("iss", $num_med, $fase,$clinic);
    $stmt->execute();
    $stmt->bind_result($url);
    
    if ($stmt->fetch()) {
        // Si se encontró la firma
        echo json_encode(["success" => "Firma encontrada.", "url" => $url]);
    } else {
        // Si no se encontró la firma
        echo json_encode(["error" => "No se encontró firma para los parámetros dados."]);
    }

    $stmt->close();
} else {
    // Especificar cuál dato falta
    $faltantes = [];
    if (!isset($_POST['fase'])) $faltantes[] = 'fase';
    if (!isset($_POST['num_med'])) $faltantes[] = 'num_med';
    if (!isset($_POST['clinic'])) $faltantes[] = 'clinic';

    echo json_encode(["error" => "Faltan datos: " . implode(", ", $faltantes)]);
}

$conn->close();
?>
