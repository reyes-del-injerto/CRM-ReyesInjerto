<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/connection_db.php";

try {
    $success = false;
    $sub_cats = [];
    $message = "No se obtuvieron subcategorías";
    
    $cat_id = isset($_POST['cat_id']) ? $_POST['cat_id'] : null;

    if ($cat_id) {
        $sql_row = "SELECT id, name FROM ad_subcategories WHERE category_id = ?";
        $sql = $conn->prepare($sql_row);
        $sql->bind_param("i", $cat_id);
        $sql->execute();
        $query = $sql->get_result();

        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $sub_cats[] = $row;
            }
            $message = "Done";
        }
        $success = true;
    } else {
        $message = "No se recibió la categoría";
    }
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(["sub_cats" => $sub_cats, "success" => $success, "message" => $message]);
