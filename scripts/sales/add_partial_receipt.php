<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../common/connection_db.php";

$id = $_POST['lead_id'];
$invoice_type = "abono";

$conversion = $_POST['price_dls'];
$amount_dls = $_POST['advance_amount_dls'];


$date = $_POST['receipt_date'];
$exploded_date = explode('-', $date);
$formatted_date = $exploded_date[2] . "/" . $exploded_date[1] . "/" . $exploded_date[0];

$name = $_POST['patient_name'];
$type = $_POST['procedure_type'];

$partial_amount = $_POST['partial_amount'];
$parsed_partial_amount = "$" . number_format($partial_amount, 2, '.', ',');

$payment_method = $_POST['payment_method'];

$partial_date = $_POST['partial_date'];
$exploded_partial_date = explode('-', $partial_date);
$formatted_partial_date = $exploded_partial_date[2] . "/" . $exploded_partial_date[1] . "/" . $exploded_partial_date[0];


if (!isset($_POST['procedure_date'])) {
    $formatted_procedure_date = "Por definir";
} else {
    $procedure_date = $raw_procedure_date = $_POST['procedure_date'];
    $procedure_date = explode('-', $procedure_date);
    $formatted_procedure_date = $procedure_date[2] . "/" . $procedure_date[1] . "/" . $procedure_date[0];
}

$Notas2 = nl2br($_POST['public_notes']);
$Notas = explode("<br />", $Notas2);
$Nota1 = $Notas[0];
$Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
$Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';

$clinic = $_POST['clinic'];
$file = ($clinic == "Pedregal") ? '../../files/cdmx/abono-pedregal.pdf' 
    : (($clinic == "Santa Fe") ? '../../files/cdmx/abono-santafe.pdf' 
    : (($clinic == "Queretaro") ? '../../files/cdmx/abono-queretaro.pdf' : '../../files/cdmx/abono-default.pdf'));

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'Letter'
]);

$mpdf->SetTitle("Abono " . $name);

$pagecount = $mpdf->SetSourceFile($file);
$tplId = $mpdf->ImportPage($pagecount);
$mpdf->UseTemplate($tplId);
$mpdf->SetFont('leagues', 'B', 14);

$mpdf->WriteText(140, 53.59, $formatted_date);

$mpdf->WriteText(50.90, 74.34, $name);

if ($type == "Capilar") {
    $mpdf->WriteText(136.3, 85, "X");
} else if ($type == "Barba") {
    $mpdf->WriteText(183.90, 85, "X");
} else if ($type == "Ambos") {
    $mpdf->WriteText(135.76, 85, "X");
    $mpdf->WriteText(183.90, 85, "X");
}

$mpdf->WriteText(38, 113.74, $parsed_partial_amount);

if ($payment_method == "TDD" || $payment_method == "TDC") {
    $mpdf->WriteText(103.12, 113.74, $payment_method);
} else if ($payment_method == "Efectivo" || $payment_method == "Depósito") {
    $mpdf->WriteText(97.80, 113.74, $payment_method);
} else {
    $mpdf->WriteText(91.80, 113.74, $payment_method);
}
$mpdf->WriteText(147.32, 113.74, $formatted_partial_date);

$mpdf->WriteText(93, 149.80, $formatted_procedure_date);




if ($clinic == "Pedregal") {
    $mpdf->SetFont('leagues', 'B', 14);
    $mpdf->WriteText(37.85, 171.68, $Nota1);
    $mpdf->WriteText(37.85, 180.00, $Nota2);
    $mpdf->WriteText(37.85, 188, $Nota3);
} else {
    $mpdf->SetFont('leagues', 'B', 14);
    $mpdf->WriteText(37.85, 170.68, $Nota1);
    $mpdf->WriteText(37.85, 179.28, $Nota2);
    $mpdf->WriteText(37.85, 187.5, $Nota3);
}

$created_at = date("Y-m-d H:i:s");

$uploaded_by = $_POST['userid'];
$status = 1;

$sql = "SELECT SUM(sip.amount) amount,
        quoted_cc_amount
        FROM sa_info_payment_px  sip
        INNER JOIN sa_closed_px sqp
        ON sip.lead_id = sqp.lead_id
        WHERE sip.lead_id = {$id} AND sip.status = 1;
";

$result = $conn->query($sql);

// Verifica si hay resultados
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $paid_amount = isset($row['amount']) ? $row['amount'] : 0;

    $paid_amount += $partial_amount;
    $total_amount = $row['quoted_cc_amount'];
    $pending_amount = $total_amount - $paid_amount;
}

$parsed_total_amount = "$" . number_format($total_amount, 2, '.', ',');
$parsed_pending_amount = "$" . number_format($pending_amount, 2, '.', ',');

$mpdf->WriteText(64, 83.67, $parsed_total_amount);
$mpdf->WriteText(37.56, 149.80, $parsed_pending_amount);

$sql_row = "INSERT INTO sa_info_payment_px (lead_id, type, amount,conversion,amount_conversion, method, payment_date, receipt_date, public_notes, clinic, created_at, uploaded_by, status) VALUES (?, ?, ?,?,?, ?, ?, ?, ?, ?, ?, ?, ?);";
$sql = $conn->prepare($sql_row);

$sql->bind_param("isdddssssssii", $id, $invoice_type, $partial_amount,$conversion,$amount_dls, $payment_method, $partial_date, $date, $Notas2, $clinic, $created_at, $uploaded_by, $status);

if ($sql->execute() && $sql->affected_rows > 0) {
    $insert_id = $conn->insert_id;
    $uploadDirectory = "../../storage/leads/{$id}/receipts/";

    // Create the uploads directory if it doesn't exist
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0775, true);
    }

    $filePath = "../../storage/leads/{$id}/receipts/{$invoice_type}_{$insert_id}.pdf";
    $mpdf->Output($filePath, 'F');
    echo json_encode(["success" => true, "path" => $filePath]);
} else {
    echo json_encode(["success" => false, "message" => 'Contacta a administración']);
}
