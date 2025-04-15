<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
session_start();

require_once __DIR__ . "/../common/bunnynet.php";
require '../../vendor/autoload.php';
require_once __DIR__ . "/../common/connection_db.php"; // Asegúrate de incluir tu archivo de conexión

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

try {
    $i = 0;
    $date = date("Y-m-d");

    $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
    $storageZoneName = "rdi-enf-cdmx";
    $clinic = 1;

    // Obtener los parámetros de la URL
    $px_identifier_type = $_GET['px_identifier_type'];
    $px_identifier = $_GET['px_identifier'];
    $treatment_id = $_GET['treatment_id'];
    $type = "treatment";

    $preview = [];
    $config = [];

    $bunny = new BunnyCDNStorage($storageZoneName, $api_key, "LA");

    $files = $_FILES['file']['tmp_name'];

    // Definir la ruta según el formato especificado
    if ($type === "treatment") {
        $srcOrigin = "{$storageZoneName}/treatments_new/{$px_identifier_type}/{$px_identifier_type}_{$px_identifier}/{$treatment_id}/";
    } else {
        throw new Exception('El tipo de archivo no es válido para esta carga.');
    }

    $srcThumb = $srcOrigin . "thumb/";

    foreach ($files as $position => $tmp_name) {
        $success = false;

        if (empty($_FILES['file']['tmp_name'][$position])) {
            throw new Exception('Archivo temporal no encontrado para el índice: ' . $position);
        }

        $parsed_filename = str_replace(" ", "", $_FILES['file']['name'][$position]);
        $local_filename = $files[$position];

        // Subir archivo original
        if (!$bunny->uploadFile($local_filename, $srcOrigin . $parsed_filename)) {
            throw new Exception('Error al subir el archivo original a BunnyCDN.');
        }

        // Generar miniatura
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

        $imagick->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        $imagick->setImageCompressionQuality(80);

        $generated_image = $imagick->getImageBlob();
        $temp_file = tempnam(sys_get_temp_dir(), 'thumbnail_');
        file_put_contents($temp_file, $generated_image);

        // Subir miniatura
        if (!$bunny->uploadFile($temp_file, $srcThumb . $parsed_filename)) {
            throw new Exception('Error al subir la miniatura a BunnyCDN.');
        }

        // Ajustar las rutas de preview y config
        $preview[] = "https://rdi-enf-cdmx.b-cdn.net/treatments_new/{$px_identifier_type}/{$px_identifier_type}_{$px_identifier}/{$treatment_id}/thumb/{$parsed_filename}";
        $config[] = [
            'caption' => $parsed_filename,
            'key' => $i,
            'url' => "scripts/photos/delete.php?filename={$parsed_filename}&clinic={$clinic}&px_identifier={$px_identifier}&px_identifier_type={$px_identifier_type}&treatment_id={$treatment_id}&type={$type}",
            'zoomData' => "https://rdi-enf-cdmx.b-cdn.net/treatments_new/{$px_identifier_type}/{$px_identifier_type}_{$px_identifier}/{$treatment_id}/{$parsed_filename}",
        ];

        // Guardar registro en la base de datos
        $created_by ="";// $_SESSION['user_id']; // Suponiendo que tienes el ID del usuario en la sesión
        $num_med = null; // Asigna el número de expediente si está disponible
        $message_id = null; // Asigna el ID del mensaje si está disponible
        $room = null; // Asigna el ID de la sala si está disponible

        $px_identifier = "Tratamiento No.". $px_identifier;

        $stmt = $conn->prepare("INSERT INTO photos_register (belongs_to, date, created_by, num_med, message_id, room) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $px_identifier, $date, $created_by, $num_med, $message_id, $room);

        if (!$stmt->execute()) {
            throw new Exception('Error al guardar el registro en la base de datos: ' . $stmt->error);
        }

        $stmt->close();
        $i++;
    }

    // Respuesta con initialPreview y config
    $response = [
        'initialPreview' => $preview,
        'initialPreviewConfig' => $config,
        'initialPreviewAsData' => true,
        'extraData' => [
            'uploaded' => true,
            'step' => $treatment_id,
            'type' => $type,
        ]
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
