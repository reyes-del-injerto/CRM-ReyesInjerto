<?php
header('Content-Type: application/json');
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

try {
    // Parámetros de entrada
    $num_med_record = $_GET['num_med_record'];
    $filename       = $_GET['filename'];
    $type           = $_GET['type'];
    $step           = $_GET['step'];
    $clinic         = $_GET['clinic'] ?? '';

    // Configuración de API keys y Storage Zone según la clínica
    $api_key_qro         = '3796debd-ae4c-407d-b632a4f78b58-bd50-461b';
    $storageZoneName_qro = 'rdi-enf-qro';

    $default_api_key         = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
    $default_storageZoneName = 'rdi-enf-cdmx';

    if (strtolower($clinic) === 'queretaro') {
        $api_key         = $api_key_qro;
        $storageZoneName = $storageZoneName_qro;
    } else {
        $api_key         = $default_api_key;
        $storageZoneName = $default_storageZoneName;
    }

    $client = new Client();

    // Construcción de la ruta de borrado según el tipo de archivo
    if ($type === "procedure" || $type === "px_treatment") {
        $path = "{$storageZoneName}/{$num_med_record}/{$step}/";
    } else if ($type === "treatment") {
        $path = "{$storageZoneName}/treatments/{$num_med_record}/{$step}";
    } else if ($type === "touchup") {
        $path = "{$storageZoneName}/{$num_med_record}/retoque/{$step}";
    } else {
        // Ruta por defecto (puedes ajustarla si es necesario)
        $path = "{$storageZoneName}/{$num_med_record}/{$step}/";
    }
    // Quitar la barra final para evitar dobles barras en la URL
    $path = rtrim($path, '/');

    // Eliminación del archivo original
    $response = $client->request('DELETE', "https://la.storage.bunnycdn.com/{$path}/{$filename}", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept'    => '*/*',
        ]
    ]);

    // Eliminación de la miniatura
    $response = $client->request('DELETE', "https://la.storage.bunnycdn.com/{$path}/thumb/{$filename}", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept'    => '*/*',
        ]
    ]);

    echo json_encode(['success' => true]);
} catch (RequestException $e) {
    echo json_encode(['error' => "Error: " . $e->getMessage()]);
}
