<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
date_default_timezone_set('America/Mexico_City');
session_start();

require_once __DIR__ . "/../common/bunnynet.php";
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

try {
    $i = 0;
    $date = date("Y-m-d");

    $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
    $storageZoneName = "rdi-enf-cdmx";
    $clinic = 1;

    $num_med_record = $_GET['num_med_record'];
    $step = $_GET['step'];
    $type = $_GET['type'];
    $month = isset($_GET['month']) ? $_GET['month'] : null;

    $preview = [];
    $config = [];

    $bunny = new BunnyCDNStorage($storageZoneName, $api_key, "LA");

    $files = $_FILES['file']['tmp_name'];

    if ($type === "procedure" || $type === "px_treatment") {
        $srcOrigin = "{$storageZoneName}/{$num_med_record}/{$step}/";
    } else if ($type === "treatment") {
        $srcOrigin = "{$storageZoneName}/treatments/{$num_med_record}/{$step}/";
    } else if ($type === "touchup") {
        $srcOrigin = "{$storageZoneName}/{$num_med_record}/retoque/{$step}/thumb";
    } else if ($type ==="protocolos") {
        $srcOrigin = "{$storageZoneName}/protocolos/{$num_med_record}/{$step}/{$month}/";
        //    $folder = "https://la.stor/protocolos/{$num_med_record}/{$step}/{$month}/thumb/";

    }

    $srcThumb = $srcOrigin . "thumb/";

    foreach ($files as $position => $tmp_name) {
        $success = false;

        $parsed_filename = str_replace(" ", "", $_FILES['file']['name'][$position]);

        $local_filename = $files[$position];

        if (!$bunny->uploadFile($local_filename, $srcOrigin . $parsed_filename)) {
            throw new Exception('Error al subir el archivo original a BunnyCDN.');
        }

        $imagick = new Imagick($local_filename);
        $imagick->setImageFormat('jpg');
        $imagick->autoOrient();

        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();

        $max = 250;

        if ($width > $max || $height > $max) {
            if ($width > $height) {
                $newWidth = round($max);
                $newHeight = round($max / ($width / $height));
            } else {
                $newHeight = round($max);
                $newWidth = round($max * ($width / $height));
            }
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }
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

        $generated_image = $imagick->getImageBlob();
        $temp_file = tempnam(sys_get_temp_dir(), 'thumbnail_');
        file_put_contents($temp_file, $generated_image);

        if (!$bunny->uploadFile($temp_file, $srcThumb . $parsed_filename)) {
            throw new Exception('Error al subir la miniatura a BunnyCDN.');
        }

        if (isset($_GET['procedure_date'])) {
            $procedure_date = $_GET['procedure_date'];
            $procedure_date = date("Y-m-d", strtotime($procedure_date));
        }
        if (($step == "post" && isset($_GET['procedure_date']) && $date == $procedure_date) || ($step == "diseno" && isset($_GET['procedure_date']) && $date == $procedure_date)) {

            $room = $_GET['room'];
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
            // Crear una instancia del cliente Guzzle
            $client = new Client([
                'base_uri' => 'https://api.telegram.org/bot' . $apiToken . '/',
            ]);
            $photoPath = "https://{$storageZoneName}.b-cdn.net/{$num_med_record}/$step/{$parsed_filename}";
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
            $success = ($response->getStatusCode() === 200) ? true : false;
        }

        $preview[] = "https://rdi-enf-cdmx.b-cdn.net/{$num_med_record}/{$step}/thumb/{$parsed_filename}";
        $config[] = [
            'caption' => $parsed_filename,
            'key' => $i,
            'url' => "scripts/photos/delete.php?filename={$parsed_filename}&clinic={$clinic}&num_med_record={$num_med_record}&step={$step}&type={$type}",
            'zoomData' => "https://rdi-enf-cdmx.b-cdn.net/{$num_med_record}/{$step}/{$parsed_filename}",
        ];
        $i++;
    }

    $response = ['initialPreview' => $preview, 'initialPreviewConfig' => $config, 'initialPreviewAsData' => true, 'extraData' => [
        'uploaded' => $success,  

        "step" => $step, 
    "type" => $type,
    ]];
    echo json_encode($response);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
