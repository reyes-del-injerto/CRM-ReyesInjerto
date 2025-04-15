<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');
require '../../vendor/autoload.php';

use GuzzleHttp\Client;

$px_identifier_type = $_POST['px_identifier_type']; // Tipo de identificador (id o exp)
$px_identifier = $_POST['px_identifier']; // Identificador (id o número de expediente)
$treatment_id = $_POST['treatment_id']; // ID del tratamiento
$type = $_POST['type']; // Tipo de tratamiento

$api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$storageZoneName = 'rdi-enf-cdmx';

$client = new Client();

// Definir la carpeta base según el tipo de identificador
$baseFolder = ($px_identifier_type === 'id') ? "id" : "exp";

// Inicializar la carpeta y el path
$folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/treatments_new/{$baseFolder}/{$px_identifier_type}_{$px_identifier}/{$treatment_id}/";
$path = "{$baseFolder}/{$baseFolder}_{$px_identifier}/{$treatment_id}";

// Realizar la solicitud HTTP GET al servicio de almacenamiento
$response = $client->request('GET', $folder, [
    'headers' => [
        'AccessKey' => $api_key,
        'accept' => '*/*',
    ]
]);

// Procesar la respuesta
$body = $response->getBody();
$files = json_decode($body, true);

// Inicializar arrays para las imágenes y la configuración
$images = $filesListConfig = [];
$i = 0;

foreach ($files as $file) {
    $file_name = $file['ObjectName'];
    $imageUrl = "https://rdi-enf-cdmx.b-cdn.net/treatments_new/{$path}/thumb/$file_name";

    // Verificar si la URL no termina con "/thumb" y agregarla a la lista
    if (!preg_match('/\/thumb$/', $imageUrl)) {
        $images[] = $imageUrl;
        $filesListConfig[] = [
            'caption' => $file['ObjectName'],
            'key' => $i,
            'url' => "scripts/photos/delete_photos_treatments.php?filename={$file_name}&type={$type}&identifier_type={$px_identifier_type}&identifier={$px_identifier}&treatment_id={$treatment_id}",
            'zoomData' => "https://rdi-enf-cdmx.b-cdn.net/{$path}/$file_name",
        ];
        $i++;
    }
}

// Preparar la respuesta JSON
echo json_encode([
    "message" => "success",
    "initialPreview" => $images,
    "initialPreviewConfig" => $filesListConfig,
    "folder" => $folder,
    "treatment_id" => $treatment_id,
    "type" => $type,
]);