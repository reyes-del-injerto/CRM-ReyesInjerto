<?php
// fetch_receipts.php

require_once __DIR__ . '/../common/connection_db.php';

// Comprobar si se recibe 'identifier' y 'type_identifier'
$identifier = $_GET['identifier'];
$type_identifier = $_GET['type_identifier'];
$type_identifier = strtolower($type_identifier);

// Inicializar la variable para el ID del paciente
$px_id = null;

// Verificar el tipo de identificador
if ($type_identifier === 'id') {
    $px_id = $identifier; // Directamente asignar el ID recibido
} elseif ($type_identifier === 'exp') {
    // Buscar en enf_treatments para obtener el ID correspondiente
    $exp_number = $identifier;
    
    $query = "SELECT id FROM enf_treatments WHERE num_med_record = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $exp_number);
    $stmt->execute();
    $stmt->bind_result($treatment_id);
    $stmt->fetch();
    $stmt->close();

    if (!$treatment_id) {
        echo json_encode(["success" => false, "message" => "No se encontró un tratamiento con el número de expediente: $exp_number"]);
        exit;
    }

    $px_id = $treatment_id; // Asignar el ID encontrado
} else {
    echo json_encode(["success" => false, "message" => "Tipo de identificador no válido."]);
    exit;
}

// Ruta base donde se almacenan los recibos
$baseDirectory = "../../storage/trats/{$px_id}/receipts/";

// Verificar que el directorio exista
if (!file_exists($baseDirectory)) {
    echo json_encode(["success" => false, "message" => "No se encontraron recibos para este tratamiento."]);
    exit;
}

// Escanear el directorio y obtener los archivos
$files = scandir($baseDirectory);

$receipts = array();

// Recorrer los archivos encontrados
foreach ($files as $file) {
    // Excluir directorios "." y ".."
    if ($file === '.' || $file === '..') {
        continue;
    }
    
    $NewbaseDirectory = "storage/trats/{$px_id}/receipts/";

    // Construir la URL pública del recibo
    $pdfUrl = "{$NewbaseDirectory}{$file}";

    // Preparar el array de datos para cada recibo
    $receipt = array(
        "name" => pathinfo($file, PATHINFO_FILENAME),
        "url" => $pdfUrl
    );
    array_push($receipts, $receipt);
}

// Devolver la respuesta en formato JSON
echo json_encode(["success" => true, "receipts" => $receipts]);
?>
