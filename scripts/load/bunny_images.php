<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.


require_once "../bunnycdn_storage.php";
$num_med_record = $_GET['num_med_record'];
$step = $_GET['step'];

try {

    $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
    $storageZoneName = 'rdi-enf-cdmx';


    foreach ($_FILES['file']['name'] as $position => $fileName) {
        $targetDir = "../../temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/";
        $targetDirThumbnail = "../../temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/thumb/";

        $file_name = basename($fileName);
        $targetFile = $targetDir . $file_name;

        $nombreArchivoLocal = $_FILES['file']['tmp_name'][$position];

        $imagick = new Imagick($nombreArchivoLocal);

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

        // Obtener la orientación de la imagen
        $orientation = $imagick->getImageOrientation();

        // Rotar la imagen según la orientación
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

        // Generar miniatura
        $imagick->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        $imagick->setImageCompressionQuality(80);

        // Obtener la miniatura generada por ImageMagick
        $imagenGenerada = $imagick->getImageBlob();
        $rutaCompletaThumbnail = $targetDirThumbnail . $file_name;

        // Verificar si el directorio de destino no existe, intentar crearlo
        if (!is_dir($targetDirThumbnail) && !mkdir($targetDirThumbnail, 0755, true)) {
            die('Error al crear el directorio de destino');
        }

        if (!file_put_contents($rutaCompletaThumbnail, $imagenGenerada)) {
            // Obtener detalles específicos del error
            die("Hubo un error al mover el archivo a {$rutaCompletaThumbnail}");
        }

        if (!move_uploaded_file($nombreArchivoLocal, $targetDir . $file_name)) {
            // Obtener detalles específicos del error
            die("Hubo un error al mover el archivo a {$targetDirThumbnail}/{$file_name}");
        }



        $preview[] = "<img data-zoom='temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/{$file_name}' src='temporal_storage/{$storageZoneName}/{$num_med_record}/{$step}/thumb/{$file_name}' class='file-preview-image'>";
        $config[] = [
            'caption' => basename($fileName),
            'key' => rand("100", "500"), // Asigna una clave única
            'url' => "scripts/delete/temporal_image.php?filename={$file_name}&storage_zone_name={$storageZoneName}&num_med_record={$num_med_record}&step={$step}"
        ];
    }

    $out = ['initialPreview' => $preview, 'initialPreviewConfig' => $config, 'initialPreviewAsData' => false];
    echo json_encode($out); // return json data

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
