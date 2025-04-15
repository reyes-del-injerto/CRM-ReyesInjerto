<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once "../connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recolectar los datos del POST
    $transaction_id = $_POST['transaction_id'];
    $description = $_POST['description'];
    $payment_method_id = $_POST['payment_method_id'];
    $amount = $_POST['amount'] * -1;
    $date = $_POST['date'];
    $store = $_POST['store'];
    $cat_id = $_POST['cat_id'];

    // Prepara la consulta
    $sql = $conn->prepare("UPDATE ad_transactions SET description = ?, payment_method_id = ?, amount = ?, date = ?, store = ?, cat_id = ? WHERE id = ?");
    // Vincula los parámetros
    $sql->bind_param("siissii", $description, $payment_method_id, $amount, $date, $store, $cat_id, $transaction_id);

    // Ejecuta la consulta
    if ($sql->execute() === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $sql->error]);
    }
    // Cierra la conexión
    $conn->close();
}
