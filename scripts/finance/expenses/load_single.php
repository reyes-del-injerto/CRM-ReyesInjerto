<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/utilities.php";
require_once __DIR__ . "/../../common/connection_db.php";

$transaction_id = $_POST['transaction_id'];
$procedure_info = [];

try {

    $sql_row = "SELECT 
						t.id, 
						t.description, 
						t.payment_method_id, 
						t.amount, 
						t.date, 
						t.store, 
						t.cat_id, 
						t.clinic, 
						cat.name cat_name 
						FROM ad_transactions t 
						LEFT JOIN ad_categories cat ON t.cat_id = cat.id 
						WHERE t.id = ?";

    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $transaction_id);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener la info. de la transacción " . $sql->error);
    }
    $result = $sql->get_result();

    if ($result->num_rows != 1) {
        throw new Exception("Error de duplicidad. Contacta al administrador.");
    }

    $transaction = $result->fetch_assoc();

    $message = "Done.";
    $success = true;
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "transaction" => $transaction, "message" => $message]);

$conn->close();
