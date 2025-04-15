<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$clinic = "cdmx";
$api_key = '630c8570-c058-4763-bff29c55f4e8-b1ca-47ae';
$storageZoneName = 'rdi-cdmx-leads';
$lead_id = $_POST['lead_id'];
$type = $_POST['type'];

$client = new Client();

$initialType = $type;
$currentStorageZoneName = $storageZoneName; // Variable para mantener el valor actual de storageZoneName

function makeRequest($client, $folder, $api_key) {
  try {
    $response = $client->request('GET', $folder, [
      'headers' => [
        'AccessKey' => $api_key,
        'accept' => '*/*',
      ]
    ]);
    $body = $response->getBody();
    return json_decode($body, true);
  } catch (RequestException $e) {
    return null;
  }
}

function filterOutPDFsIfNeeded($files, $type) {
  if ($type === 'assessment') {
    return array_filter($files, function($file) {
      $ext = strtolower(pathinfo($file['ObjectName'], PATHINFO_EXTENSION));
      return $ext !== 'pdf';
    });
  }
  return $files;
}

$folder = "https://la.storage.bunnycdn.com/{$currentStorageZoneName}/{$lead_id}/{$type}/";
$files = makeRequest($client, $folder, $api_key);
$files = filterOutPDFsIfNeeded($files, $type);

$message = "Petición realizada con type: $type";

if (empty($files) && $initialType == "photos") {
  $type = "valoracion";
  $folder = "https://la.storage.bunnycdn.com/{$currentStorageZoneName}/{$lead_id}/{$type}/";
  $files = makeRequest($client, $folder, $api_key);
  $files = filterOutPDFsIfNeeded($files, $type);
  $message .= ", $type";
}
if (empty($files) && $initialType == "photos" && $type == "valoracion") {
  $type = "assessment";
  $folder = "https://la.storage.bunnycdn.com/{$currentStorageZoneName}/{$lead_id}/{$type}/";
  $files = makeRequest($client, $folder, $api_key);
  $files = filterOutPDFsIfNeeded($files, $type);
  $message .= ", $type";
}
if (empty($files) && $initialType == "photos" && $type == "assessment") {
  $type = "valoracion";
  $currentStorageZoneName = 'rdi-enf-cdmx'; // Cambia el storageZoneName actual
  $folder = "https://la.storage.bunnycdn.com/{$currentStorageZoneName}/valoraciones/{$lead_id}/valoracion/";
  $api_key = "67708a26-bc3d-4637-bce324a44a8d-9766-4ecb";
  $files = makeRequest($client, $folder, $api_key);
  $files = filterOutPDFsIfNeeded($files, $type);
  $message .= ", y por ultimo con type: $type";
}

if (empty($files) && $initialType == "id") {
  $type = "ine";
  $folder = "https://la.storage.bunnycdn.com/{$currentStorageZoneName}/{$lead_id}/{$type}/";
  $files = makeRequest($client, $folder, $api_key);
  $message .= "Primero con id, y luego con type: $type";
}

$images = $filesListConfig = [];
$i = 0;

$preview = [];
$config = [];

if (is_array($files)) {
  foreach ($files as $file) {
    $fileName = $file['ObjectName'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $type_ext = getFileType($ext);

    $fileName  = basename($fileName);

    if ($currentStorageZoneName === 'rdi-enf-cdmx') {
      $originSrc = "https://{$currentStorageZoneName}.b-cdn.net/valoraciones/{$lead_id}/{$type}/{$fileName}";
    } else {
      $originSrc = "https://{$currentStorageZoneName}.b-cdn.net/{$lead_id}/{$type}/{$fileName}";
    }

    $preview[] = $originSrc;

    $config[] = [
      'caption' => $fileName,
      'type' => $type_ext,
      'key' => $i,
      'url' => "scripts/sales/delete_patient_file.php?filename={$fileName}&lead_id={$lead_id}&type={$type}",
      'downloadUrl' => "scripts/sales/download_patient_file.php?filename={$fileName}&lead_id={$lead_id}&type={$type}"
    ];
    $i++;
  }
}

$out = ['initialPreview' => $preview, 'initialPreviewConfig' => $config, 'initialPreviewAsData' => true, "path" => $folder, "message" => $message];
echo json_encode($out);

function getFileType($type_ext) {
  switch ($type_ext) {
    case 'jpg':
    case 'png':
    case 'jpeg':
      return 'image';
    case 'pdf':
      return 'pdf';
    case 'docx':
    case 'xlsx':
      return 'office';
    default:
      return 'other';
  }
}
?>
