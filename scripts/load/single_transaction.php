<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

session_start();
require_once "../connection_db.php";

// Consulta para obtener los eventos
$transaction_id = $_POST['transaction_id'];
$sql = "SELECT ad_transactions.*, ad_categories.name cat_name FROM ad_transactions INNER JOIN ad_categories ON cat_id = ad_categories.id WHERE ad_transactions.id = $transaction_id;";

$result = $conn->query($sql);

// Verifica si hay resultados
if ($result->num_rows > 0) {
    // Inicializa un array para almacenar los eventos
    $transaction = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "transaction" => $transaction
    ]);
} else {
    echo json_encode([
        "success" => false
    ]);
}
