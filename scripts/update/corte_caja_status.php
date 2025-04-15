<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once "../connection_db.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $public_id = $_POST['public_id'];

    $sql_row = "UPDATE sa_corte_caja SET approved = 1 WHERE public_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("s", $public_id);

    $success = ($sql->execute()) ? true : false;

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Datos actualizados correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ocurrió un error. Conta a administración.'
        ]);
    }
    // Cerrar la declaración y la conexión
    $sql->close();
}
