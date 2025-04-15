<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
session_start();

require_once "../connection_db.php";

try {
    $lead_id = $_POST['lead_id'];

    $sql = "SELECT quoted_cash_amount, quoted_cc_amount FROM sa_closed_px WHERE lead_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lead_id);

    if (!$stmt->execute()) {
        throw new Exception("Error: " . $stmt->error);
    }
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $quoted_cash_amount = $row['quoted_cash_amount'];
        $quoted_cc_amount = $row['quoted_cc_amount'];

        //Si los campos no son nulos, es porque ya existe una validación de la información
        //En la sección de Perfil del px -> Resumen
        $exist = (!is_null($quoted_cash_amount) && !is_null($quoted_cc_amount)) ? true : false;

        //Por lo tanto, de no existir, se manda el mensaje
        $message = ($exist) ? true : "Completa la información en: Perfil del Px -> Resumen -> Cotización";
    } else {
        $message = "Completa la información en Perfil del Px -> Resumen -> Cotización";
    }

    echo json_encode([
        "success" => true,
        "exist" => $exist,
        "message" => $message
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
    ]);
}
