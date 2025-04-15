<?php
// Configuración inicial y carga de librerías necesarias
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // Utilizado debido a xdebug.
date_default_timezone_set('America/Mexico_City');
session_start();

require_once "../common/connection_db.php";
require_once "./bunnycdn_storage.php";
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;



$responseData = []; // Array para almacenar datos de depuración

try {
    $date = date("Y-m-d");
    $dateNow = date('Y-m-d H:i:s');
    $num_med_record = $_GET['num_med_record'];
    $step = $_GET['step'];
    $procedure_type = $_GET['type'];
    $name = $_GET['name'];
    $user_id = $_GET['user_id'] ?? 0; // Obtener user_id de la URL, o 0 si no está presente 
    $clinic = $_GET['clinic'] ?? ''; // Recibe el valor de la clínica


    $api_key_qro = '3796debd-ae4c-407d-b632a4f78b58-bd50-461b';
    $storageZoneName_qro = 'rdi-enf-qro';

    // Datos de configuración por defecto
    $default_api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
    $default_storageZoneName = 'rdi-enf-cdmx';
    $valor = "";


    if ($clinic == 'Queretaro' || $clinic == 'queretaro') {

        $api_key = $api_key_qro;
        $storageZoneName = $storageZoneName_qro;
        $valor = "Qro";

    } else {
        $api_key = $default_api_key;
        $storageZoneName = $default_storageZoneName;
        $valor = "default";
    }


    $responseData['initialParams'] = [
        'dateNow' => $dateNow,
        'date' => $date,
        'num_med_record' => $num_med_record,
        'step' => $step,
        'procedure_type' => $procedure_type,
        'clinic' => $clinic
    ];

    $preview = [];
    $config = [];

    // Inicialización de BunnyCDNStorage
    $bunnyCDNStorage = new BunnyCDNStorage($storageZoneName, $api_key, "LA");

    // Obtener nombre para el mensaje de Telegram
    $name = $_GET['name'] ?? 'Desconocido';

    // Configuración de Telegram
    $room = $_GET['room'];
    if($clinic =="Queretaro" || $clinic =="queretaro"){
        $room = 4;
    }
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

        case 4:
            $apiToken = "7418565569:AAF81a3JiOjlUz9jSD_LiFkEy-2eKO5o4Jk";
            $chat_id = '-1002414738423';
            break;
        default:
            $apiToken = "7812313321:AAEVfuVS0H6q5VmmMnQtMVI4YRA5pRSk4P0";
            $chat_id = '-1002330562823';
            break;
    }

    $client = new Client([
        'base_uri' => 'https://api.telegram.org/bot' . $apiToken . '/',
    ]);

    $files = $_FILES['file']['tmp_name'];

    foreach ($files as $position => $tmp_name) {
        $success = false;

        // Inicializa las variables con valores por defecto
        $folder = '';
        $touchup = 'no';

        // Verifica si el parámetro 'type' está presente en la consulta
        if (isset($_GET['type'])) {
            if ($_GET['type'] == "touchup") {
                $folder = "retoque/";
                $touchup = 'yes';
            } elseif ($_GET['type'] == "micro") {
                $folder = "micro/";
                $touchup = 'micro';
            }
        }

        // Si no se cumple ninguna de las condiciones, los valores por defecto ya están establecidos


        $responseData['loopParams'][] = [
            'position' => $position,
            'folder' => $folder,
            'touchup' => $touchup
        ];

        $rutaBunnyCDNOriginal = "{$storageZoneName}/{$num_med_record}/{$folder}{$step}/";
        $rutaBunnyCDNThumbnail = $rutaBunnyCDNOriginal . "thumb/";

        $nombreArchivoOriginalBunnyCDN = str_replace(" ", "", $_FILES['file']['name'][$position]);
        $nombreArchivoThumbnailBunnyCDN = $nombreArchivoOriginalBunnyCDN;
        $nombreArchivoLocal = $files[$position];

        // Subida del archivo original a BunnyCDN
        if (!$bunnyCDNStorage->uploadFile($nombreArchivoLocal, $rutaBunnyCDNOriginal . $nombreArchivoOriginalBunnyCDN)) {
            throw new Exception('Error al subir el archivo original a BunnyCDN.');
        }

        $responseData['uploadOriginal'][] = [
            'rutaBunnyCDNOriginal' => $rutaBunnyCDNOriginal,
            'nombreArchivoOriginalBunnyCDN' => $nombreArchivoOriginalBunnyCDN
        ];

        // Procesamiento de la miniatura con Imagick
        $imagick = new Imagick($nombreArchivoLocal);
        $imagick->setImageFormat('jpg');
        $imagick->autoOrient();

        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        $max = 250;
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $max || $height > $max) {
            if ($width > $height) {
                $newWidth = round($max);
                $newHeight = round($max / ($width / $height));
            } else {
                $newHeight = round($max);
                $newWidth = round($max * ($width / $height));
            }
        }

        // Ajuste de la orientación de la imagen
        $orientation = $imagick->getImageOrientation();
        switch ($orientation) {
            case Imagick::ORIENTATION_TOPRIGHT:
                $imagick->rotateimage("#000", 90);
                break;
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $imagick->rotateimage("#000", 180);
                break;
            case Imagick::ORIENTATION_BOTTOMLEFT:
                $imagick->rotateimage("#000", -90);
                break;
        }

        $imagick->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        $imagick->setImageCompressionQuality(80);

        $imagenGenerada = $imagick->getImageBlob();
        $archivoTemporal = tempnam(sys_get_temp_dir(), 'thumbnail_');
        file_put_contents($archivoTemporal, $imagenGenerada);

        // Subida de la miniatura a BunnyCDN
        if (!$bunnyCDNStorage->uploadFile($archivoTemporal, $rutaBunnyCDNThumbnail . $nombreArchivoThumbnailBunnyCDN)) {
            throw new Exception('Error al subir la miniatura a BunnyCDN.');
        }

        $responseData['uploadThumbnail'][] = [
            'rutaBunnyCDNThumbnail' => $rutaBunnyCDNThumbnail,
            'nombreArchivoThumbnailBunnyCDN' => $nombreArchivoThumbnailBunnyCDN
        ];

        // Configuración de la fecha del procedimiento
        $procedure_date = null;
        if (isset($_GET['procedure_date'])) {
            $procedure_date = date("Y-m-d", strtotime($_GET['procedure_date']));
        }

        $responseData['datesAndSteps'] = [
            'procedure_date' => $procedure_date,
            'date' => $date,
            'datenow' => $dateNow,
            'step' => $step
        ];

        // Indicar si entra en la condición
        $enteredCondition = false;

        if (($step == "post" || $step == "diseno" || $step == "pre") && isset($_GET['procedure_date']) && $date == $procedure_date) {
            $enteredCondition = true;

            $photoPath = "https://{$storageZoneName}.b-cdn.net/{$num_med_record}/$step/{$nombreArchivoOriginalBunnyCDN}";

            $messageText = "Enviando fotos de px: $name, Fase: $step";

            // Enviar mensaje a Telegram solo si la fase es 'pre', 'diseno', o 'post'
            if (in_array($step, ['pre', 'diseno', 'post'])) {
                $response = $client->post('sendMessage', [
                    'json' => [
                        'chat_id' => $chat_id,
                        'text' => $messageText
                    ]
                ]);

                $responseData['telegramMessageText'] = $messageText;

                $success = ($response->getStatusCode() === 200);

                // Extraer el message_id de la respuesta
                $responseBody = json_decode($response->getBody(), true);
                $message_id = $responseBody['result']['message_id'] ?? null;
                if ($message_id) {
                    error_log("Telegram message_id: " . $message_id);
                    $responseData['telegramMessageId'] = $message_id;
                }
            }

            // Enviar foto a Telegram
            $response = $client->post('sendPhoto', [
                'multipart' => [
                    ['name' => 'chat_id', 'contents' => $chat_id],
                    ['name' => 'photo', 'contents' => fopen($photoPath, 'r'), 'filename' => basename($photoPath)]
                ]
            ]);

            $success = ($response->getStatusCode() === 200);

            $responseData['telegramResponse'] = [
                'statusCode' => $response->getStatusCode(),
                'body' => json_decode($response->getBody(), true)
            ];
        }

        $message_id = $message_id ?? 0; // Asignar 0 si $message_id no está definido
        $sql = "INSERT INTO photos_register (belongs_to, date, created_by, num_med, message_id, room) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssiss', $name, $dateNow, $user_id, $num_med_record, $message_id, $room);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success = true;
        }

        if ($message_id) {
            $sqlUpdate = "UPDATE photos_register SET message_id = ? WHERE num_med = ? AND date = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param('iss', $message_id, $num_med_record, $dateNow);
            $stmtUpdate->execute();
        }

        $responseData['databaseInsert'] = [
            'success' => $success,
            'num_med_record' => $num_med_record,
            'dateNow' => $dateNow,
            'username' => $user_id,
            'message_id' => $message_id
        ];

        // Limpiar el archivo temporal
        unlink($archivoTemporal);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Archivos subidos correctamente.',
        'valor' => $valor,
        'data' => $responseData
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'data' => $responseData
    ]);
}
