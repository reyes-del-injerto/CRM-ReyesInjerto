<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once "../connection_db.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lead_id = $_POST['lead_id'];
    $collection_notes = $_POST['collection_notes'];

    $sql_row = "UPDATE sa_info_payment_px SET public_notes = ? WHERE lead_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("si", $collection_notes, $lead_id);

    $success = ($sql->execute()) ? true : false;

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Notas de cobro actualizadas correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ocurrió un error. Contacta a administración.'
        ]);
    }
    // Cerrar la declaración y la conexión
    $sql->close();
}
