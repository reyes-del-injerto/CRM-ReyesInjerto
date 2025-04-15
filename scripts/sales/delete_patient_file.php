<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$lead_id = $_GET['lead_id'];
$filename = $_GET['filename'];

try {
  $clinic = 2;
  $api_key = '630c8570-c058-4763-bff29c55f4e8-b1ca-47ae';
  $storageZoneName = 'rdi-cdmx-leads';
  $lead_id = $_GET['lead_id'];
  $type = $_GET['type'];

  $client = new Client();

  $response = $client->request('DELETE', "https://la.storage.bunnycdn.com/{$storageZoneName}/{$lead_id}/{$type}/{$filename}", [
    'headers' => [
      'AccessKey' => $api_key,
      'accept' => '*/*',
    ]
  ]);

  echo json_encode(['success' => true]);
} catch (RequestException $e) {
  echo json_encode(['error' => "Error" . $e->getMessage()]);
}
