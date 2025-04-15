<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/../common/connection_db.php";
require_once __DIR__ . "/../common/validate_session.php";

header('Content-Type: application/json');

// Verifica que se ha recibido un archivo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $id = $_POST['num_med_record'];
    
    // Genera el nombre del archivo
    $fileName = 'id_' . $id . '.pdf';
    $uploadDir = __DIR__ . '/../../files/protocols/';
    
    // Crea la carpeta si no existe
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Ruta completa donde se guardará el archivo
    $uploadFile = $uploadDir . basename($fileName);
    
    // Mueve el archivo a la carpeta deseada
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo json_encode(['success' => true, 'message' => 'Consentimiento añadido con éxito']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se ha recibido ningún archivo o hubo un error en la subida']);
}
?>
