<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../common/bunnynet.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

try {
  $clinic = 2;
  $api_key = '630c8570-c058-4763-bff29c55f4e8-b1ca-47ae';
  $storageZoneName = 'rdi-cdmx-leads';
  $lead_id = $_GET['lead_id'];
  $type = $_GET['type'];

  $preview = [];
  $config = [];

  $bunnyCDNStorage = new BunnyCDNStorage($storageZoneName, $api_key, "LA");

  // Verificamos que se haya enviado el archivo
  if (!isset($_FILES["file{$type}"])) {
    throw new Exception("No se ha enviado ningún archivo.");
  }

  $files = $_FILES["file{$type}"]['tmp_name'];
  $i = 0;

  foreach ($files as $position => $tmp_name) {
    $bunnyCdnPath = "{$storageZoneName}/{$lead_id}/{$type}/";

    // Utilizamos directamente $tmp_name en lugar de volver a acceder al array
    $local_file_name = $tmp_name;
    // Se elimina espacios del nombre original del archivo
    $file_name_parsed = str_replace(" ", "", $_FILES["file{$type}"]['name'][$position]);

    // Se obtiene la extensión a partir del nombre original del archivo
    $ext = strtolower(pathinfo($file_name_parsed, PATHINFO_EXTENSION));
    $type_ext = getFileType($ext);

    if (!$bunnyCDNStorage->uploadFile($local_file_name, $bunnyCdnPath . $file_name_parsed)) {
      throw new Exception('Error al subir el archivo a BunnyCDN.');
    }

    $originSrc = "https://{$storageZoneName}.b-cdn.net/{$lead_id}/{$type}/{$file_name_parsed}";

    $preview[] = $originSrc;

    $config[] = [
      'caption'     => $file_name_parsed,
      'type'        => $type_ext,
      'key'         => $i,
      'url'         => "scripts/sales/delete_patient_file.php?filename={$file_name_parsed}&lead_id={$lead_id}&type={$type}",
      'downloadUrl' => "scripts/sales/download_patient_file.php?filename={$file_name_parsed}&lead_id={$lead_id}&type={$type}"
    ];
    $i++;
  }

  $out = [
    'initialPreview'         => $preview,
    'initialPreviewConfig'   => $config,
    'initialPreviewAsData'   => true,
    "lead_id"                => $lead_id,
    "type"                   => $type
  ];
  echo json_encode($out);
} catch (Exception $e) {
  // Se devuelve el error en formato JSON para mantener la coherencia con el header
  echo json_encode(["error" => $e->getMessage()]);
}

function getFileType($type_ext)
{
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
