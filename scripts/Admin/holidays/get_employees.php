<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../../common/connection_db.php';
$response= "fail";
$data_array = [];
/* $sql = "SELECT 
            ad_holidays.id, 
            ad_employees.name, 
            DATE_FORMAT(start, '%d/%m/%Y') AS start, 
            DATE_FORMAT( DATE_SUB(end, INTERVAL 1 DAY) , '%d/%m/%Y') AS end,
            notes,  
            approved_by 
        FROM ad_holidays 
        LEFT JOIN ad_employees 
            ON ad_holidays.employee_id = ad_employees.id 
        WHERE status = 1 ORDER BY id DESC"; */

        $sql= "SELECT * FROM `ad_employees`";

$query = $conn->query($sql);

if ($query->num_rows > 0) {
    $response = "sucess";
    while ($data = $query->fetch_object()) {

        $data_array[] = $data;
    }
}


echo json_encode([
	"success" => true, "data" => $data_array, 
]);