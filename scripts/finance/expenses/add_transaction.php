<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../../common/connection_db.php";

try {
    // Obtener datos del POST
    $description = $_POST['description'];
    $payment_method_id = $_POST['payment_method_id'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $store = $_POST['store'];
    $cat_id = $_POST['cat_id'];
    $sub_cat_id = $_POST['sub_cat_id'];
    // Verifica si el valor es un número entero
    if (!is_numeric($sub_cat_id) || (int)$sub_cat_id != $sub_cat_id) {
        // Si el valor no es un número entero, asigna 73
        $sub_cat_id = 73;
    } else {
        // Si el valor es un número, conviértelo a entero
        $sub_cat_id = (int)$sub_cat_id;
    }
    $clinic = $_POST['clinic'];
    $created_by = $_POST['user_id'];

    // Actualizar el amount en la tabla ad_subcategories (agregar el valor positivo)
    $updateSubCatSql = $conn->prepare("
        UPDATE ad_subcategories
        SET current = current + ?
        WHERE id = ?
    ");
    $updateSubCatSql->bind_param("di", $amount, $sub_cat_id);

    if (!$updateSubCatSql->execute()) {
        throw new Exception("Error al actualizar el amount en ad_subcategories.");
    }

    // Preparar el amount negativo para ad_transactions
    $negativeAmount = $amount * -1;
    $current_statuus = 1;
    // Insertar el nuevo gasto en ad_transactions (usar el valor negativo)
    $insertSql = $conn->prepare("
        INSERT INTO ad_transactions (description, payment_method_id, amount, date, store, cat_id,current_status, subcategory, clinic, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)
    ");
    $insertSql->bind_param("sidssiiisi", $description, $payment_method_id, $negativeAmount, $date, $store, $cat_id, $current_statuus, $sub_cat_id, $clinic, $created_by);

    if (!$insertSql->execute()) {
        throw new Exception("Error al agregar el gasto.");
    }

    $success = true;
    $message = "Gasto añadido y subcategoría actualizada correctamente";
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

echo json_encode(['success' => $success, 'message' => $message]);

$conn->close();
