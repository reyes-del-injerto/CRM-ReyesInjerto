<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../../common/connection_db.php";

try {
    $conn->begin_transaction();

    $receipt_id = $_POST['receipt_id'];
    $public_id = $_POST['public_id'];

    $sql_row = "DELETE FROM sa_corte_caja WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $receipt_id);

    if (!$sql->execute()) {
        throw new Exception("Error al eliminar el Corte de Caja");
    }
    $dir = "../../files/cdmx/corte-caja/{$public_id}";
    if (!deleteDirectory($dir)) {
        throw new Exception("Error al eliminar archivos del Corte de Caja");
    }

    $success = true;
    $message = "Corte eliminado correctamente";
    $conn->commit();
} catch (Exception $e) {
    $success = false;
    $message = "Error:" . $e->getMessage();
    $conn->rollback();
}

$conn->close();

echo json_encode(['success' => $success, 'message' => $message]);


function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
