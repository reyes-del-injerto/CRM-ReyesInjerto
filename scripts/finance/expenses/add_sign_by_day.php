<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . "/../../common/connection_db.php";

// Verificar si se recibieron los datos necesarios
if (isset($_POST['dia']) && isset($_POST['firma']) && isset($_POST['clinic'])) {
    $dia = $_POST['dia'];
    $firma = $_POST['firma'];
    $clinic = $_POST['clinic']; // Nuevo campo

    // Extraer solo la parte base64 de la imagen
    $base64_string = $firma;
    if (preg_match('/^data:image\/\w+;base64,/', $base64_string)) {
        $base64_string = preg_replace('/^data:image\/\w+;base64,/', '', $base64_string);
    }

    // Preparar la consulta para insertar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO daily_cortes (dia, firma, clinic) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $dia, $base64_string, $clinic); // Incluir el nuevo parámetro
    
    if ($stmt->execute()) {
        // Si la inserción fue exitosa
        echo json_encode(["success" => "Datos insertados correctamente."]);
    } else {
        // Si hubo un error al insertar
        echo json_encode(["error" => "Error al insertar los datos: " . $stmt->error]);
    }

    $stmt->close();

} else {
    // Especificar cuál dato falta
    $faltantes = [];
    if (!isset($_POST['dia'])) $faltantes[] = 'dia';
    if (!isset($_POST['firma'])) $faltantes[] = 'firma';
    if (!isset($_POST['clinic'])) $faltantes[] = 'clinic'; // Verificar si falta el campo `clinic`

    echo json_encode(["error" => "Faltan datos: " . implode(", ", $faltantes)]);
}

$conn->close();
?>
