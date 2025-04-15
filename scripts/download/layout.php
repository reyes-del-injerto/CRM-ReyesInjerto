<?php
// Ruta del archivo que deseas descargar
$file = '../../files/cdmx/layout_bbva.txt';

// Verifica si el archivo existe
if (file_exists($file)) {
    // Configura los encabezados para la descarga
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    // Lee el archivo y envíalo al navegador
    readfile($file);
    exit;
} else {
    // Si el archivo no existe, muestra un mensaje de error
    echo 'El archivo que intentas descargar no existe.';
}
