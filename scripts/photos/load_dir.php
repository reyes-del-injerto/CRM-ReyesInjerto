<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_POST['num_med_record'];
$step           = $_POST['step'];
$type           = $_POST['type'];
$clinic         = $_POST['clinic'] ?? ''; // Recibe el valor de la clínica
$month          = $_POST['month'] ?? null;

// Claves y zonas de almacenamiento
$api_key_cdmx         = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$storageZoneName_cdmx = 'rdi-enf-cdmx';
$api_key_qro          = '3796debd-ae4c-407d-b632a4f78b58-bd50-461b';
$storageZoneName_qro  = 'rdi-enf-qro';

// Verifica la clínica y selecciona las credenciales correspondientes
if (strtolower($clinic) === 'queretaro') {
    $api_key         = $api_key_qro;
    $storageZoneName = $storageZoneName_qro;
} else {
    // Usa las credenciales de CDMX como valor por defecto
    $api_key         = $api_key_cdmx;
    $storageZoneName = $storageZoneName_cdmx;
}

$client = new Client();

// Definir la carpeta y el path basado en el tipo
if ($type === "procedure" || $type === "px_treatment") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/{$step}/";
    $path = "{$num_med_record}/{$step}";
} else if ($type === "treatment") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/treatments/{$num_med_record}/{$step}/thumb/";
    $path = "treatments/{$num_med_record}/{$step}";
} else if ($type === "touchup") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/retoque/{$step}/";
    $path = "{$num_med_record}/retoque/{$step}";
} else if ($type == "protocolos") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/protocolos/{$num_med_record}/{$step}/{$month}/thumb/";
    $path = "protocolos/{$num_med_record}/{$step}/{$month}";
} else if ($type == "micro") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/micro/{$step}/{$month}";
    $path = "{$num_med_record}/micro/{$step}/{$month}";
}

$response = $client->request('GET', $folder, [
    'headers' => [
        'AccessKey' => $api_key, // Usa la API key seleccionada
        'accept'    => '*/*',
    ]
]);

$body = $response->getBody();
$files = json_decode($body, true);

$images = [];
$filesListConfig = [];
$i = 0;
foreach ($files as $file) {
    $file_name = $file['ObjectName'];
    $imageUrl = "https://{$storageZoneName}.b-cdn.net/{$path}/thumb/$file_name";

    // Verificar que la URL no termine con "/thumb" (para descartar directorios vacíos)
    if (!preg_match('/\/thumb$/', $imageUrl)) {
        $images[] = $imageUrl;
        $filesListConfig[] = [
            'caption'  => $file['ObjectName'],
            'key'      => $i,
            'url'      => "scripts/photos/delete.php?filename={$file_name}&type={$type}&num_med_record={$num_med_record}&step={$step}&clinic={$clinic}",
            'zoomData' => "https://{$storageZoneName}.b-cdn.net/{$path}/$file_name",
        ];
        $i++;
    }
}

echo json_encode([
    "message"               => "success",
    "initialPreview"        => $images,
    "initialPreviewConfig"  => $filesListConfig,
    "folder"                => $folder,
    "step"                  => $step,
    "type"                  => $type,
]);
