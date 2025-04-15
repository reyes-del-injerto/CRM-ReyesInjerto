<?php
//1
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $transaction_id = $_POST['transaction_id'];

        $sql = "DELETE FROM ad_transactions WHERE id = ?;";

        $sql = $conn->prepare($sql);
        $sql->bind_param("i", $transaction_id);

        if (!$sql->execute()) throw new Exception("Error al eliminar la transaccion: " . $sql->error);

        if ($sql->affected_rows === 0) throw new Exception("La transaccion ya no existe.");


        $success = true;
        $message = "Transaccion eliminada correctamente";
    } catch (Exception $e) {
        $success = false;
        $message = $e->getMessage();
    }

    echo json_encode(["success" => $success, "message" => $message]);
    $conn->close();
}
