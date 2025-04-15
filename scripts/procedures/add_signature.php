<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../common/connection_db.php";

// Verificar si se recibió la imagen y los datos
if (isset($_POST['firma']) && isset($_POST['fase']) && isset($_POST['num_med'])) {
    $imagenBase64 = $_POST['firma'];
    $fase = $_POST['fase'];
    $num_med = $_POST['num_med'];
    $clinic = $_POST['clinic'];

    // Limpiar la cadena de imagen base64
    $imagenBase64 = str_replace('data:image/png;base64,', '', $imagenBase64);
    $imagenBase64 = str_replace(' ', '+', $imagenBase64);

    // Preparar la consulta para insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO enf_signs (num_med, step, url,clinic) VALUES (?, ?, ?,?)");
    $stmt->bind_param("isss", $num_med, $fase, $imagenBase64,$clinic);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Imagen guardada y datos insertados correctamente."]);
    } else {
        echo json_encode(["error" => "Error al insertar datos: " . $stmt->error]);
    }

    $stmt->close();
} else {
    // Especificar cuál dato falta
    $faltantes = [];
    if (!isset($_POST['firma'])) $faltantes[] = 'firma';
    if (!isset($_POST['fase'])) $faltantes[] = 'fase';
    if (!isset($_POST['num_med'])) $faltantes[] = 'num_med';

    echo json_encode(["error" => "Faltan datos: " . implode(", ", $faltantes)]);
}

$conn->close();
?>
