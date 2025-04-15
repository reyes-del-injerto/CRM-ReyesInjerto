<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . '/../common/connection_db.php';
require_once __DIR__ . "/../common/bunnynet.php";
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $success = false;
    //Lead_ID

    $id = $_POST['lead_id'];
    $receipt_type = "anticipo";
    $clinic = $_POST['clinic'];
    $userid = $_POST['userid'];

    $receipt_date = $_POST['receipt_date'];
    $date = parse_date($receipt_date);

    $name = $_POST['patient_name'];
    $type = $_POST['procedure_type'];
    $advance_amount_dls = $_POST['advance_amount_dls'];
    $price_dls = $_POST['price_dls'];
    // Nuevo parámetro para el idioma
    $ingles = isset($_POST['ingles']) ? $_POST['ingles'] : '';


    $total_amount = $_POST['total_amount'];
    $parsed_total_amount = "$" . number_format($total_amount, 2, '.', ',');

    $advance_amount = $_POST['advance_amount'];
    $parsed_advance_amount = "$" . number_format($advance_amount, 2, '.', ',');

    $payment_method = $_POST['payment_method'];

    $advance_date = $_POST['payment_date'];
    $formatted_advance_date = parse_date($advance_date);

    $pending_amount = $_POST['pending_amount'];
    $pending_amount = format_amount($_POST['pending_amount']);

    $formatted_procedure_date = (!isset($_POST['procedure_date'])) ? "Por definir" : parse_date($_POST['procedure_date']);

    $Notas2 = nl2br($_POST['public_notes']);
    $Notas = explode("<br />", $Notas2);
    $Nota1 = isset($Notas[0]) ? ltrim(rtrim($Notas[0])) : '';
    $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
    $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';

    // Selección del archivo PDF según la clínica y el idioma
    if ($ingles === "on") {
        if ($clinic == "Santa Fe") {
            $file = '../../files/cdmx/anticipo-santafe_ingles.pdf';
        } else if ($clinic == "Pedregal") {
            $file = '../../files/cdmx/anticipo-pedregal_ingles.pdf';
        }
    } else {
        // Si no está en inglés, seguir flujo normal
        $file = ($clinic == "Pedregal") ? '../../files/cdmx/anticipo-pedregal.pdf'
            : (($clinic == "Santa Fe") ? '../../files/cdmx/anticipo-santafe.pdf'
                : (($clinic == "Queretaro") ? '../../files/cdmx/anticipo-qro.pdf' : '../../files/cdmx/anticipo-default.pdf'));
    }


    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'Letter'
    ]);

    $mpdf->SetTitle("Anticipo_" . $name);

    $pagecount = $mpdf->SetSourceFile($file);
    $tplId = $mpdf->ImportPage($pagecount);
    $mpdf->UseTemplate($tplId);
    $mpdf->SetFont('leagues', 'B', 14);

    $mpdf->WriteText(144, 53.59, $date);

    $mpdf->WriteText(50.90, 74.34, $name);

    if ($type == "Capilar") {
        $mpdf->WriteText(136.3, 85, "X");
    } else if ($type == "Barba") {
        $mpdf->WriteText(183.90, 85, "X");
    } else if ($type == "Ambos") {
        $mpdf->WriteText(136.3, 85, "X");
        $mpdf->WriteText(183.90, 85, "X");
    }

    $mpdf->WriteText(64, 83.67, $parsed_total_amount);

    $mpdf->WriteText(37.56, 113.74, $parsed_advance_amount);

    if ($payment_method == "TDD" || $payment_method == "TDC") {
        $mpdf->WriteText(103.12, 113.74, $payment_method);
    } else if ($payment_method == "Efectivo" || $payment_method == "Depósito") {
        $mpdf->WriteText(97.80, 113.74, $payment_method);
    } else {
        $mpdf->WriteText(91.80, 113.74, $payment_method);
    }

    $mpdf->WriteText(146.05, 113.74, $formatted_advance_date);
    $mpdf->WriteText(37.56, 149.80, $pending_amount);
    $mpdf->WriteText(93, 149.80, $formatted_procedure_date);

    // Ajustar las coordenadas de las notas según la clínica
    if ($clinic == "Pedregal") {
        $mpdf->WriteText(37.85, 172, $Nota1);
        $mpdf->WriteText(37.85, 181.28, $Nota2);
        $mpdf->WriteText(37.85, 188, $Nota3);
    } else {
        $mpdf->WriteText(37.85, 172, $Nota1);
        $mpdf->WriteText(37.85, 180.28, $Nota2);
        $mpdf->WriteText(37.85, 186, $Nota3);
    }

    $created_at = date("Y-m-d H:i:s");

    $uploaded_by = $userid;
    $status = 1;

    $sql_row = "INSERT INTO sa_info_payment_px (lead_id, type, amount,conversion,amount_conversion, method, payment_date, receipt_date, public_notes, clinic, created_at, uploaded_by, status) VALUES (?, ?, ?,?,?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $sql = $conn->prepare($sql_row);

    $sql->bind_param("isdddssssssii", $id, $receipt_type, $advance_amount, $advance_amount_dls, $price_dls, $payment_method, $advance_date, $receipt_date, $Notas2, $clinic, $created_at, $uploaded_by, $status);

    if (!$sql->execute()) {
        throw new Exception("Al crear el registro en la Base de Datos" . $sql->error);
    }

    $insert_id = $conn->insert_id;
    $uploadDirectory = "../../storage/leads/{$id}/receipts/";

    if (!file_exists($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0775, true)) {
            throw new Exception("Al crear la carpeta de Recibos" . $sql->error);
        }
    }

    $filePath = "../../storage/leads/{$id}/receipts/anticipo_{$insert_id}.pdf";

    if ($mpdf->Output($filePath, 'F') === false) {
        throw new Exception("Al generar el archivo PDF" . $sql->error);
    }

    $success = true;
    $message = "";
} catch (Exception $e) {
    $success = false;
    $message = "Error:" . $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message, "path" => $filePath]);
$conn->close();

function parse_date($date)
{
    $exploded_date = explode('-', $date);
    $formatted_date = $exploded_date[2] . "/" . $exploded_date[1] . "/" . $exploded_date[0];
    return $formatted_date;
}

function format_amount($amount)
{
    return "$" . number_format($amount, 2, '.', ',');
}
