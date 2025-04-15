<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


$num_med_record = $_POST['num_med_record'];
$step = $_POST['step'];
$type = $_POST['type'];

$api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$storageZoneName = 'rdi-enf-cdmx';

$client = new Client();

if ($type === "procedure" || $type === "px_treatment") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/{$step}/thumb/";
}else if ($type === "treatment") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/treatments/{$num_med_record}/{$step}/thumb/";
}
 else if ($type === "touchup") {
    $folder = "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/retoque/{$step}/thumb/";
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
$i = 0;
foreach ($files as $file) {
    $file_name = $file['ObjectName'];

    $images[] = "https://rdi-enf-cdmx.b-cdn.net/treatments/{$num_med_record}/{$step}/thumb/$file_name";
    $filesListConfig[] = [
        'caption' => $file['ObjectName'],
        'key' => $i,
        'url' => "scripts/delete/bunny_image.php?filename={$file['ObjectName']}&type={$type}&num_med_record={$num_med_record}&step={$step}",
        'zoomData' => "https://rdi-enf-cdmx.b-cdn.net/{$num_med_record}/{$step}/$file_name",

    ];
    $i++;
}

$images = ($images == null) ? '' : $images;
$filesListConfig = ($filesListConfig == null) ? '' : $filesListConfig;

echo json_encode([
    "message" => "success",
    "initialPreview" => $images,
    "initialPreviewConfig" => $filesListConfig,
    "folder" => $folder
]);
