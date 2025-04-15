<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../../common/connection_db.php";

/* type_submit=1&
transaction_id=532&
description=Fina&
date=2024-06-02T00%3A00&
store=&
cat_id=9&
payment_method_id=3&
amount=40240&
clinic=Santafe
 */

try {
    $transaction_id = $_POST['transaction_id'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $store = $_POST['store'];
    $cat_id = $_POST['cat_id'];
    $amount = $_POST['amount'];
    $ParseAmount = $amount * -1;
    $payment_method_id = $_POST['payment_method_id'];
    $clinic = $_POST['clinic'];


    $sql_row = "UPDATE ad_transactions SET description = ? , payment_method_id = ?, date = ?, store = ?, cat_id = ?, amount = ?, clinic = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("sissidsi", $description, $payment_method_id, $date, $store, $cat_id, $ParseAmount, $clinic, $transaction_id);

    if (!$sql->execute()) {
        throw new Exception("Error al actualizar la información. Contacta al administrador");
    }

    $success = true;
    $message = "Transaccion Actulizada . Gracias! 🦁";
} catch (Exception $e) {
    $success = false;
    $message = "Error:" . $e->getMessage();
}

$conn->close();

$response = ["success" => $success, "message" => $message];
echo json_encode($response);
