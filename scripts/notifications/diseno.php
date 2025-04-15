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


// Configuración de la API de Telegram
$token = "6657742338:AAFZZEFnJ4Phm-fqGLNCx2NWUCkLMrbIkaA";
$chatId = '-4147146974';

$storageZoneName = "rdi-enf-cdmx";
$num_med_record = $_POST['num_med_record'];


// Crear una instancia del cliente Guzzle
$client = new Client([
    'base_uri' => 'https://api.telegram.org/bot' . $token . '/',
]);

$ruta_temporal = "../../temporal_storage/{$storageZoneName}/{$num_med_record}/diseno/";


if (is_dir($ruta_temporal)) {

    $elementos = scanDir($ruta_temporal);

    // Filtrar solo los archivos, excluyendo "." y ".."
    $archivos = array_filter($elementos, function ($elemento) use ($ruta_temporal) {
        return is_file($ruta_temporal . '/' . $elemento);
    });

    // Recorrer y mostrar los archivos
    foreach ($_POST['selected_photos'] as $archivo) {
        $photoPath = "../../temporal_storage/{$storageZoneName}/{$num_med_record}/diseno/{$archivo}";
        // Enviar la foto al grupo
        $response = $client->post('sendPhoto', [
            'multipart' => [
                [
                    'name' => 'chat_id',
                    'contents' => $chatId
                ],
                [
                    'name' => 'photo',
                    'contents' => fopen($photoPath, 'r'),
                    'filename' => basename($photoPath)
                ]
            ]
        ]);

        // Verificar el código de estado de la respuesta
        if ($response->getStatusCode() === 200) {
            $success = true;
        } else {
            $success = false;
        }
    }

    $telegram_message = "Envío fotos de diseño de *" . $_POST['name'] . ".* Comentarios realizarlos (provisionalmente) al celular personal del especialista.";
    $data = [
        'chat_id' => $chatId,
        'text' => $telegram_message,
        'parse_mode' => 'markdown'
    ];

    $response = file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data));
}
