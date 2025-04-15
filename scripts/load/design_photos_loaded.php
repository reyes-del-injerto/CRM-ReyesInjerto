<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_POST['num_med_record'];
$step = $_POST['step'];

$api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$clinic = 1;
$storageZoneName = 'rdi-enf-cdmx';

$content = '';
$disableButton = false; // Define el valor de $disableButton según sea necesario
$i = 0; // Inicializar contador para IDs únicos

$client = new Client();


$response = $client->request('GET', "https://la.storage.bunnycdn.com/{$storageZoneName}/{$num_med_record}/{$step}/thumb/", [
    'headers' => [
        'AccessKey' => $api_key,
        'accept' => '*/*',
    ]
]);

$body = $response->getBody();
$files = json_decode($body, true);

foreach ($files as $file) {
    $content .=
        "<li>
                <input type='checkbox' id='myCheckbox{$i}' name='selected_photos[]' value='{$file['ObjectName']}'/>
                <label for='myCheckbox{$i}'><img src='scripts/load/bunny_patient_image.php?filename=" . $file['ObjectName'] . "&clinic=" . $clinic . "&num_med_record=" . $num_med_record . "&step=" . $step . "' /></label>
            </li>";
    $i++;
}
// Convertir contenido a JSON
echo json_encode(['disableButton' => $disableButton, 'content' => $content]);
