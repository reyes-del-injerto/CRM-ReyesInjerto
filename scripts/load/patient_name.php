<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

header('Content-Type: application/json');

require_once "../connection_db.php";
require_once "../../vendor/autoload.php"; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$num_med_record = $_POST['num_med_record'];

$sql = "SELECT CONCAT(sig.first_name, ' ', sig.last_name) AS fullname FROM sa_info_general_px sig INNER JOIN enf_procedures ep ON sig.id = ep.px_sales_id WHERE sig.clinic = 'CDMX' AND ep.num_med_record = $num_med_record;";

$query = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($query);

echo json_encode($row);
