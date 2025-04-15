<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');

session_start();
require_once '../connection_db.php';

$sql = "SELECT id, created_at, first_name, last_name, phone, interested_in, stage, quali, seller, last_activity FROM sa_leads;";
$query = $conn->query($sql);

$data_array = [];

if ($query->num_rows > 0) {
    while ($data = $query->fetch_object()) {
        $lead_name = $data->first_name . " " . $data->last_name;
        $link_name = "<a data-id='{$data->id}' href='view_lead.php?id={$data->id}' type='button'>{$lead_name}</a>";

        $last_activity_timestamp = strtotime($data->last_activity);
        $last_activity = date("d/m/Y H:i", $last_activity_timestamp);

        $created_at_timestamp = strtotime($data->created_at);
        $created_at = date("d/m/Y H:i", $created_at_timestamp);

        $data_array[] = [
            $data->id,
            $link_name,
            $data->phone,
            $data->interested_in,
            $data->stage,
            $data->quali,
            $last_activity,
            $data->seller,
            $created_at
        ];
    }
}
$result  = ["data" => $data_array];
echo json_encode($result);
