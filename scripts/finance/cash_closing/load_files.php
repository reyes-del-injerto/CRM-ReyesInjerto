<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

$public_id = $_POST['public_id'];

// Define the base directory for your files
$baseDir = realpath(__DIR__ . '/../../../files/cdmx/corte-caja/') . "/{$public_id}/";

$files = [];
$filesListConfig = [];
$initialPreviewDownloadUrl = [];

if (is_dir($baseDir)) {
    $elementos = scanDir($baseDir);
    // Filtrar solo los archivos, excluyendo "." y ".."
    $archivos = array_filter($elementos, function ($elemento) use ($baseDir) {
        return is_file($baseDir . $elemento);
    });

    // Recorrer y mostrar los archivos
    foreach ($archivos as $archivo) {
        $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        $type = getFileType($ext);

        $files[] = "files/cdmx/corte-caja/{$public_id}/{$archivo}";
        $initialPreviewDownloadUrl = "scripts/download/corte_caja_files.php";
        $filesListConfig[] = [
            'type' => $type,
            'caption' => $archivo,
            'key' => $archivo . "&public_id={$public_id}",
            'url' => "scripts/delete/file.php?filename={$archivo}&route={$baseDir}{$archivo}"
        ];
    }
}

$files = ($files == null) ? '' : $files;
$filesListConfig = ($filesListConfig == null) ? '' : $filesListConfig;

echo json_encode([
    "message" => "success",
    "initialPreview" => $files,
    "initialPreviewDownloadUrl" => $initialPreviewDownloadUrl,
    "initialPreviewConfig" => $filesListConfig,
    "path" => $baseDir
]);

function getFileType($ext)
{
    switch ($ext) {
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
            return 'other'; // Tipo de archivo por defecto para otras extensiones
    }
}
