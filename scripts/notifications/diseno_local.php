<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); //
date_default_timezone_set('America/Mexico_City');

session_start();
require_once "../connection_db.php";
require_once "../../test_whatsapp.php";

require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

$room = $_POST['room'];
// Configuración de la API de Telegram
switch ($room) {
    case 1:
        $apiToken = "6579613970:AAHl-G5We20gpxYkred0bKJnA_U_iu2oPpo";
        $chat_id = '-1002138129798';
        break;
    case 2:
        $apiToken = "6657742338:AAFZZEFnJ4Phm-fqGLNCx2NWUCkLMrbIkaA";
        $chat_id = '-1002016137935';
        break;
    case 3:
        $apiToken = "6446216208:AAGmWFmBgYnjCF8ScZ4ng21k54oWkbkK6f4";
        $chat_id = '-1002135608367';
        break;
    default:
        $apiToken = "6720020904:AAEANxvaEd5p_RBZA7Ov3xCMsgcXHxEWj7U";
        $chat_id = '-1002138129798';
        break;
}

$bucketName = "rdi-enf-cdmx";
$num_med_record = $_POST['num_med_record'];
$step = 'diseno';
// Crear una instancia del cliente Guzzle
$client = new Client([
    'base_uri' => 'https://api.telegram.org/bot' . $apiToken . '/',
]);

// Recorrer y mostrar los archivos
foreach ($_POST['selected_photos'] as $archivo) {
    $photoPath = "https://{$bucketName}.b-cdn.net/{$num_med_record}/{$step}/{$archivo}";
    // Enviar la foto al grupo
    $response = $client->post('sendPhoto', [
        'multipart' => [
            [
                'name' => 'chat_id',
                'contents' => $chat_id
            ],
            [
                'name' => 'photo',
                'contents' => fopen($photoPath, 'r'),
                'filename' => basename($photoPath)
            ]
        ]
    ]);

    // Verificar el código de estado de la respuesta

    $success = ($response->getStatusCode() === 200) ? true : false;
}

$telegram_message = "Envío fotos de diseño de *" . $_POST['name'] . ".* Comentarios realizarlos (provisionalmente) al celular personal del especialista.";
$data = [
    'chat_id' => $chat_id,
    'text' => $telegram_message,
    'parse_mode' => 'markdown'
];

$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));

if (isset($_POST['id'])) {
    header("Location: ../../procedure_photos.php?id=$id");
} else {
    header("Location: ../../index.php");
}
