<?php
header('Content-Type: application/json');
require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_POST['num_med_record'];
$treatment_id = $_POST['treatment_id'];
$clinic = 5;

$api_key = 'f563abdc-5446-4e88-a80d6f0a563c-e164-4843';
$storageZoneName = 'rdi-cdmx-trats';

$client = new Client();

$response = $client->request('GET', "https://la.storage.bunnycdn.com/{$storageZoneName}/treatments/{$num_med_record}/{$treatment_id}/thumb/", [
    'headers' => [
        'AccessKey' => $api_key,
        'accept' => '*/*',
    ]
]);

$body = $response->getBody();
$files = json_decode($body, true);

foreach ($files as $file) {
    $images[] = "<img data-zoom='scripts/load/bunny_patient_image.php?filename=" . $file['ObjectName'] . "&clinic=" . $clinic . "&num_med_record=" . $num_med_record . "&step=" . $id . "&type=zoom' src='scripts/load/bunny_patient_image.php?filename=" . $file['ObjectName'] . "&clinic=" . $clinic . "&num_med_record=" . $num_med_record . "&step=" . $step . "' class='file-preview-image'>";
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
    "api_key" => $api_key,
    "storage" => $storageZoneName
]);
