<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$data_array = [];
$sql = "SELECT 
            ad_holidays.id, 
            ad_employees.name, 
            DATE_FORMAT(start, '%d/%m/%Y') AS start, 
            DATE_FORMAT( DATE_SUB(end, INTERVAL 1 DAY) , '%d/%m/%Y') AS end,
            notes, 
            approved_by 
        FROM ad_holidays 
        LEFT JOIN ad_employees 
            ON ad_holidays.employee_id = ad_employees.id 
        WHERE status = 1 ORDER BY id DESC";

$query = $conn->query($sql);

if ($query->num_rows > 0) {
    while ($data = $query->fetch_object()) {

        $options = "<a href='#' data-id='{$data->id}' class='btn btn-danger btn-sm btn-square rounded-pill delete_holiday'><i class='btn-icon fa fa-trash'></i> </a>";


        $data_array[] = array(
            $data->id,
            $data->name,
            $data->start,
            $data->end,
            $data->notes,
            $data->approved_by,
            $options
        );
    }
}

$holidays = ["data" => $data_array];

echo json_encode($holidays);
