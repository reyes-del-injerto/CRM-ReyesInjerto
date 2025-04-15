<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');
require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_POST['num_med_record'];
$step = $_POST['step'];
$clinic = $_POST['clinic'];

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
    case 5:
        $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
        $storageZoneName = 'rdi-enf-cdmx/treatments';
        break;
    default:
        echo 0;
}

$ruta_temporal = "../../temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/thumb/";

if (is_dir($ruta_temporal)) {
    $elementos = scanDir($ruta_temporal);

    // Filtrar solo los archivos, excluyendo "." y ".."
    $archivos = array_filter($elementos, function ($elemento) use ($ruta_temporal) {
        return is_file($ruta_temporal . '/' . $elemento);
    });

    // Recorrer y mostrar los archivos
    foreach ($archivos as $archivo) {
        $images[] = "<img data-zoom='temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/{$archivo}' src='temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/thumb/{$archivo}' class='file-preview-image'>";
        $filesListConfig[] = [
            'caption' => $archivo,
            'key' => rand("100", "500"), // Asigna una clave única
            'url' => "scripts/delete/temporal_image.php?filename={$archivo}&storage_zone_name={$storageZoneName}&num_med_record={$num_med_record}&step={$step}"
        ];
    }
}


$client = new Client();

if (isset($_POST['touchup']) && $_POST['touchup'] == 1) {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/retoque/{$step}/thumb/";
    $touchup = "yes";
} else {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/{$step}/thumb/";
    $touchup = "no";
}
$response = $client->request('GET', $folder, [
    'headers' => [
        'AccessKey' => $api_key,
        'accept' => '*/*',
    ]
]);

$body = $response->getBody();
$files = json_decode($body, true);

$images = $filesListConfig = [];

foreach ($files as $file) {
    $images[] = "<img data-zoom='scripts/load/bunny_patient_image.php?filename=" . $file['ObjectName'] . "&clinic=" . $clinic . "&num_med_record=" . $num_med_record . "&step=" . $step . "&type=zoom&touchup=" . $touchup . "' src='scripts/load/bunny_patient_image.php?filename=" . $file['ObjectName'] . "&clinic=" . $clinic . "&num_med_record=" . $num_med_record . "&step=" . $step . "&touchup=" . $touchup . "' class='file-preview-image'>";
    $filesListConfig[] = [
        'caption' => $file['ObjectName'],
        'key' => rand("100", "500"), // Asigna una clave única
        'url' => "scripts/delete/bunny_image.php?filename={$file['ObjectName']}&clinic={$clinic}&num_med_record={$num_med_record}&step={$step}"
    ];
}

$images = ($images == null) ? '' : $images;
$filesListConfig = ($filesListConfig == null) ? '' : $filesListConfig;

echo json_encode([
    "message" => "success",
    "initialPreview" => $images,
    "initialPreviewConfig" => $filesListConfig,
    "folder" => $folder
]);
