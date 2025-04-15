<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require '../connection_db.php';

$lead_id = $_POST['id'];
$quoted_cash_amount = floatval($_POST['quoted_cash_amount']);
$quoted_cc_amount = floatval($_POST['quoted_cc_amount']);
$installments = trim($_POST['installments']);
try {
    // Inicia la transacción
    $conn->begin_transaction();

    $sql_row = "UPDATE sa_closed_px SET quoted_cash_amount = ?, quoted_cc_amount = ?, installments = ? WHERE lead_id = ?";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ddsi", $quoted_cash_amount, $quoted_cc_amount, $installments, $lead_id);

    /* Update Lead Stage and Info */
    if (!$sql->execute()) {
        throw new Exception("Contacta al Administrador");
    }
    /* Successful Result */
    $conn->commit();

    echo json_encode(["success" => true, "message" => "Info. actualizada correctamente"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    $conn->close();
}
