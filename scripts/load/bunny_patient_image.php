<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_GET['num_med_record'];
$step = $_GET['step'];
$clinic = $_GET['clinic'];

$client = new Client();

try {

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
            echo 0;
    }

    $touchup = (isset($_GET['touchup']) && $_GET['touchup'] == "yes") ? 'retoque/' : '';

    $directoryPath = $num_med_record . "/" . $touchup . $step . "/thumb/";
    if (isset($_GET['type']) && $_GET['type'] == 'zoom') {
        $directoryPath = $num_med_record . "/" . $touchup . $step . "/";
    }


    $file_name = $_GET['filename'];
    $img_url = 'https://la.storage.bunnycdn.com/' . $storageZoneName . "/" . $directoryPath . $file_name;
    $get_image = $client->request('GET', $img_url, [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '*/*',
        ],
    ]);
    // Salida directa del contenido de la imagen
    $body_image = $get_image->getBody()->getContents();
    header('Content-Type: image/jpeg'); // Asegúrate de establecer el tipo de contenido correcto
    echo $body_image;
    exit;
} catch (RequestException $e) {
    echo "Error: " . $e->getMessage();
}
