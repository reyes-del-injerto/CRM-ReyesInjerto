<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

session_start();
require_once "../../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_name = $_POST['cat_name'];
    $cat_amount = $_POST['cat_amount'];
    $current = 1;

    $sql = $conn->prepare("INSERT INTO ad_categories (name, amount, current) VALUES (?, ?, ?)");

    $sql->bind_param("sdi", $cat_name, $cat_amount, $current);

    if ($sql->execute()) {
        echo json_encode(['success' => true, 'message' => 'Datos insertados correctamente']);
    } else {
        echo "Error al insertar en categories: " . $sql->error;
    }

    $conn->close();
}
