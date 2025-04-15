<?php
header('Content-Type: application/json');
require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$px_id = $_POST['px_id'];
$step = "valoracion";
$clinic = 1;


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
    default:
        echo 0;
}
$client = new Client();

try {
    $response = $client->request('GET', "https://la.storage.bunnycdn.com/{$storageZoneName}/ventas/{$px_id}/{$step}/thumb/", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '*/*',
        ]
    ]);

    $body = $response->getBody();
    $files = json_decode($body, true);
    $images = [];

    foreach ($files as $file) {
        $images[] = "<img data-zoom='scripts/load/sales_bunny_imgs.php?filename=" . $file['ObjectName'] . "&clinic=1&px_id=" . $px_id . "&step=valoracion&type=zoom' src='scripts/load/sales_bunny_imgs.php?filename=" . $file['ObjectName'] . "&clinic=1&px_id=" . $px_id . "&step=valoracion' class='file-preview-image'>";
        $filesListConfig[] = [
            'caption' => $file['ObjectName'],
            'key' => rand("100", "500"), // Asigna una clave única
            'url' => "scripts/delete/sales_bunny_img.php?filename={$file['ObjectName']}&clinic=1&px_id={$px_id}&step=valoracion"
        ];
    }

    echo json_encode([
        "message" => "success",
        "initialPreview" => $images,
        "initialPreviewConfig" => $filesListConfig,
        "api_key" => $api_key,
        "storage" => $storageZoneName
    ]);
} catch (RequestException $e) {
    echo "Error: " . $e->getMessage();
}
