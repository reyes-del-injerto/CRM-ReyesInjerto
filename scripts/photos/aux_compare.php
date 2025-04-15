<?php
// Obtener los parámetros desde la URL
$numMedico = isset($_GET['numMedico']) ? $_GET['numMedico'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

// Validar los parámetros
if (!$numMedico) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta el número de médico']);
    exit;
}

// Definir los valores de BunnyCDN
$api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$storageZoneName = 'rdi-enf-cdmx';

// Construir la URL base
$baseUrl = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$numMedico}/";

// Añadir la carpeta según el tipo
if ($type === "touchup") {
    $baseUrl .= "retoque/";
}

// Hacer la petición a BunnyCDN con cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "AccessKey: $api_key",
    "Accept: application/json",
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    // Si la respuesta es exitosa, retornar el contenido JSON al frontend
    header('Content-Type: application/json');
    echo $response;
} else {
    // En caso de error, retornar un mensaje de error al frontend
    http_response_code($httpCode);
    echo json_encode(['error' => 'Error al obtener los datos de BunnyCDN.']);
}
