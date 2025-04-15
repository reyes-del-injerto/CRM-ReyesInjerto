<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/connection_db.php";


try {
    $success = false;
    $cats = [];
    $message = "No se obtuvieron categorías";

    $sql_row = "SELECT id,name FROM ad_categories;";
    $sql = $conn->prepare($sql_row);
    $sql->execute();
    $query = $sql->get_result();

    if ($query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            $cats[] = $row;
        }
        $message = "Done";
    }
    $success = true;
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}


echo json_encode(["cats" => $cats, "success" => $success, "message" => $message]);
