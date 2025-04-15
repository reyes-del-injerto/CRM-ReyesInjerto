<?php

header('Content-Type: application/json');
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

try {
     // Obtener los parámetros de la URL
     $identifier = isset($_GET['identifier']) ? $_GET['identifier'] : null;
     $identifier_type = isset($_GET['identifier_type']) ? $_GET['identifier_type'] : null;
     $filename = isset($_GET['filename']) ? $_GET['filename'] : null;
     $type = isset($_GET['type']) ? $_GET['type'] : null;
     $treatment_id = isset($_GET['treatment_id']) ? $_GET['treatment_id'] : null;
 
     // Inicializar un array para almacenar los parámetros faltantes
     $missingParams = [];
 
     // Verificar qué parámetros están faltando
     if (!$identifier) {
         $missingParams[] = 'identifier';
     }
     if (!$identifier_type) {
         $missingParams[] = 'identifier_type';
     }
     if (!$filename) {
         $missingParams[] = 'filename';
     }
     if (!$type) {
         $missingParams[] = 'type';
     }
     if (!$treatment_id) {
         $missingParams[] = 'treatment_id';
     }
 
     // Si hay parámetros faltantes, devolver un error
     if (!empty($missingParams)) {
         echo json_encode([
             'error' => 'Faltan parámetros: ' . implode(', ', $missingParams)
         ]);
         exit;
     }
 
    // BunnyCDN API Key y zona de almacenamiento
    $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
    $storageZoneName = 'rdi-enf-cdmx';
    $client = new Client();

    // Definir la carpeta base según el tipo de identificador
    $baseFolder = ($identifier_type === 'id') ? "id" : "exp";
    $path = "{$storageZoneName}/treatments_new/{$identifier_type}/{$baseFolder}_{$identifier}/{$treatment_id}";

    // Eliminar el archivo original
    $responseOriginal = $client->request('DELETE', "https://la.storage.bunnycdn.com/{$path}/{$filename}", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '*/*',
        ]
    ]);

    // Eliminar la miniatura correspondiente
    $responseThumb = $client->request('DELETE', "https://la.storage.bunnycdn.com/{$path}/thumb/{$filename}", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '*/*',
        ]
    ]);

    // Responder con éxito si ambas solicitudes DELETE fueron exitosas
    if ($responseOriginal->getStatusCode() === 200 && $responseThumb->getStatusCode() === 200) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'No se pudo eliminar uno o más archivos']);
    }
} catch (RequestException $e) {
    // Manejo de excepciones
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
