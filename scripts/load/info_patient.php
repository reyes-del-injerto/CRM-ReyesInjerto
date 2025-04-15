<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../connection_db.php';

$px_sales_id = 1;
$data_array = array();
// SQL para obtener los datos
$sql = "SELECT px_sales.*,px_payment.* FROM px_sales INNER JOIN px_payment ON px_sales.id = px_payment.px_sales_id WHERE px_sales.id = {$px_sales_id} AND status = 0;";


// Ejeuctar el SQL
$query = $conn->query($sql);
if ($query) {
    $row = $query->fetch_assoc();

    // Encode the row into JSON
    $jsonResult = json_encode($row);

    // Output the JSON
    echo $jsonResult;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
