<?php

// Configuración de errores para depuración
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

try {
    // Verificar parámetros de entrada
    if (!isset($_GET['num_med_record']) || !isset($_GET['step']) || !isset($_GET['clinic']) || !isset($_GET['filename'])) {
        throw new Exception("Faltan parámetros en la URL.");
    }

    $num_med_record = $_GET['num_med_record'];
    $step = $_GET['step'];
    $clinic = $_GET['clinic'];
    $filename = $_GET['filename'];

    // Crear cliente Guzzle
    $client = new Client();

    // Determinar storage zone y API key basado en la clínica
    switch ($clinic) {
        case 1: // CDMX
            $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
            $storageZoneName = 'rdi-enf-cdmx';
            break;
        case 2: // Culiacán
            $api_key = '90086039-bce6-43d4-bc3dc22d891c-ee35-4e6b';
            $storageZoneName = 'rdi-enf-cul';
            break;
        case 3: // Mazatlán
            $api_key = 'bfae151f-118b-4428-acc65e702314-1987-4471';
            $storageZoneName = 'rdi-enf-mzt';
            break;
        case 4: // Tijuana
            $api_key = 'bc1fee1f-25c4-43cc-9662f7fd5588-a964-497b';
            $storageZoneName = 'rdi-enf-tij';
            break;
        case 5: // CDMX
            $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
            $storageZoneName = 'rdi-enf-cdmx/treatments/';
            break;
        default:
            throw new Exception("Clínica no válida.");
    }

    // Determinar si es retoque
    $touchup = (isset($_GET['touchup']) && $_GET['touchup'] == "yes") ? 'retoque/' : '';

    // Determinar el path del directorio
    $directoryPath = $num_med_record . "/" . $touchup . $step . "/thumb/";
    if (isset($_GET['type']) && $_GET['type'] == 'zoom') {
        $directoryPath = $num_med_record . "/" . $touchup . $step . "/";
    }

    // Construir la URL de la imagen
    $img_url = 'https://la.storage.bunnycdn.com/' . $storageZoneName . "/" . $directoryPath . $filename;

    // Depuración: Mostrar la URL de la imagen
    error_log("URL de la imagen: " . $img_url);

    // Realizar la solicitud GET para obtener la imagen
    $get_image = $client->request('GET', $img_url, [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '*/*',
        ],
    ]);

    // Obtener el contenido de la imagen
    $body_image = $get_image->getBody()->getContents();

    // Establecer el tipo de contenido y enviar la imagen
    header('Content-Type: image/jpeg');
    echo $body_image;
    exit;
} catch (RequestException $e) {
    // Depuración: Mostrar error de la solicitud
    error_log("Error en la solicitud: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    // Depuración: Mostrar cualquier otro error
    error_log("Error general: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
}
