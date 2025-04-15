<?php
date_default_timezone_set('America/Mexico_City');

header('Content-Type: application/json');

if (isset($_GET['num_med_record'])) {
    $id = $_GET['num_med_record'];
    $fileName = 'id_' . $id . '.pdf';
    $filePath = __DIR__ . '/../../files/protocols/' . $fileName;

    if (file_exists($filePath)) {
        echo json_encode(['exists' => true, 'filePath' => './files/protocols/' . $fileName]);
    } else {
        echo json_encode(['exists' => false]);
    }
} else {
    echo json_encode(['exists' => false]);
}
?>
