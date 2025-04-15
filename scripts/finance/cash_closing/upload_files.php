<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

$public_id = $_GET['public_id'];
$i = 0;

$preview = [];
$config = [];
$json_data = [];

try {
    // Iterar sobre cada archivo subido
    foreach ($_FILES['file']['name'] as $position => $fileName) {
        $targetDir = __DIR__ . "/../../../files/cdmx/corte-caja/{$public_id}/";
        $file_name = basename($fileName);
        $targetFile = $targetDir . $file_name;

        // Verificar si la carpeta ya existe
        if (!is_dir($targetDir)) {
            // Si no existe, intentar crearla
            if (!mkdir($targetDir, 0775, true)) {
                throw new Exception("Error al crear la carpeta de Recibos en {$targetDir}");
            }
        }

        // Mover el archivo a la carpeta destino
        $local_filename = $_FILES['file']['tmp_name'][$position];
        if (!move_uploaded_file($local_filename, $targetFile)) {
            throw new Exception("Hubo un error al mover el archivo a {$targetFile}. Verifique los permisos de la carpeta.");
        }

        // Generar la vista previa y la configuración del archivo
        $preview[] = "<div class='file-preview-other'>\
                            <i class='fa fa-file-pdf-o'></i>\
                            <div class='file-preview-other-content'>{$file_name}</div>\
                        </div>";
        $config[] = [
            'caption' => basename($fileName),
            'key' => $i,
            
        ];
        $i++;
    }

    // Generar el JSON de respuesta
    $json_data = [
        'initialPreview' => $preview,
        'initialPreviewConfig' => $config,
        'initialPreviewAsData' => false,
        'overwriteInitial' => false
    ];

} catch (Exception $e) {
    $json_data = [
        'error' => "Error: " . $e->getMessage()
    ];
}

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($json_data);
?>
