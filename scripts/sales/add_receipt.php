<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . '/../common/connection_db.php';
require_once __DIR__ . "/../common/bunnynet.php";
require_once __DIR__ . '/../../vendor/autoload.php';

$lead_id = $_POST['lead_id'];

$type = $_POST['invoice_type'];
$name = $_POST['full_name'];
$amount = "$" . number_format($_POST['amount'], 2, '.', ',');
$payment_method_num = $_POST['payment_method'];
$notes = $_POST['notes'];
$product = isset($_POST['product']) ? $_POST['product'] : '';
$treatment = isset($_POST['treatment']) ? $_POST['treatment'] : '';
$clinic = $_POST['clinic'];
$advance_amount_dls	=  $_POST['advance_amount_dls'];
$price_dls	=  $_POST['price_dls'];

$file = "../../files/cdmx/{$type}-{$clinic}.pdf";

// Mapeo de métodos de pago
$payment_methods = [
    1 => 'Efectivo',
    2 => 'Transferencia',
    3 => 'TDD',
    4 => 'TDC',
    5 => 'Dólares',
    6 => 'Depósito'
];

// Convertir el número a texto
$payment_method = $payment_methods[$payment_method_num] ?? 'Desconocido';

if ($type == "producto") {
    $product = $_POST['product'];
}

//!Fecha
$date = $_POST['receipt_date'];
$date = explode('-', $date);
$date = $date[2] . "/" . $date[1] . "/" . $date[0];

$Notas2 = nl2br($_POST['notes']);

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'orientation' => 'P',
    'format' => 'Letter'
]);

$mpdf->SetTitle("Recibo");

$pagecount = $mpdf->SetSourceFile($file);
$tplId = $mpdf->ImportPage($pagecount);
$mpdf->UseTemplate($tplId);
$mpdf->SetFont('leagues', 'B', 14);

write_name($name);
write_date($date);
($type == "producto") ? write_product_notes($Notas2) : write_treatment_notes($Notas2);
($type == "producto") ? payment_for_products($payment_method_num) : payment_for_treatments($payment_method_num);
($type == "producto") ? write_amount_product($amount) : write_amount_treatment($amount);
($type == "producto") ? write_product($product) : choose_treatment($treatment);

function write_product($product)
{
    global $mpdf;
    $mpdf->WriteText(32, 86.5, $product);
}
function choose_treatment($treatment)
{
    global $mpdf;
    switch ($treatment) {
        case 1:
            $mpdf->WriteText(36.5, 95.3, 'X');
            break;
        case 2:
            $mpdf->WriteText(36.5, 104.3, 'X');
            break;
        case 3:
            $mpdf->WriteText(36.5, 113.8, 'X');
            break;
        default:
            $mpdf->WriteText(36.5, 80, 'X');
            break;
    }
}
//!Monto
function write_amount_product($amount)
{
    global $mpdf;
    $mpdf->WriteText(41.2, 122.80, $amount);
}
//!Monto
function write_amount_treatment($amount)
{
    global $mpdf;
    $mpdf->WriteText(41.2, 140.80, $amount);
}

function payment_for_treatments($payment_method_num)
{
    global $mpdf;
    switch ($payment_method_num) {
        case 1:
            $mpdf->WriteText(90.8, 140, 'X');
            break;
        case 2:
            $mpdf->WriteText(90.8, 149, 'X');
            break;
        case 3:
            $mpdf->WriteText(90.8, 159, 'X');
            break;
        case 4:
            $mpdf->WriteText(90.8, 168, 'X');
            break;
        default:
            $mpdf->WriteText(90.8, 140, 'X');
            break;
    }
}

function payment_for_products($payment_method_num)
{
    global $mpdf;
    switch ($payment_method_num) {
        case 1:
            $mpdf->WriteText(89.5, 121, 'X');
            break;
        case 2:
            $mpdf->WriteText(89.5, 129.7, 'X');
            break;
        case 3:
            $mpdf->WriteText(89.5, 139.33, 'X');
            break;
        case 4:
            $mpdf->WriteText(89.5, 148.2, 'X');
            break;
        case 5:
            $mpdf->WriteText(89.5, 158.4, 'X');
            break;
        case 6:
            $mpdf->WriteText(89.5, 168, 'X');
            break;
        default:
            $mpdf->WriteText(89.5, 121, 'X');
            break;
    }
}

function write_name($name)
{
    //!Nombre
    global $mpdf;
    $mpdf->WriteText(32, 71.8, $name);
}
//!Fecha 
function write_date($date)
{
    global $mpdf;
    $mpdf->WriteText(140, 49.5, $date);
}
function write_product_notes($Notas2)
{
    $Notas = explode("<br />", $Notas2);
    $Nota1 = $Notas[0];
    $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
    $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';


    //!Notas
    global $mpdf;
    $mpdf->WriteText(32, 187.50, $Nota1);
    $mpdf->WriteText(32, 192.80, $Nota2);
    $mpdf->WriteText(32, 199.40, $Nota3);
    return true;
}


function write_treatment_notes($Notas2)
{
    $Notas = explode("<br />", $Notas2);
    $Nota1 = $Notas[0];
    $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
    $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';


    //!Notas
    global $mpdf;
    $mpdf->WriteText(32, 197.40, $Nota1);
    $mpdf->WriteText(32, 204.60, $Nota2);
    $mpdf->WriteText(32, 213.40, $Nota3);
    return true;
}
$created_at = date("Y-m-d H:i:s");
$uploaded_by = $_POST['userid'];
$status = 1;

$sql_row = "INSERT INTO sa_info_payment_px (lead_id, type, amount,conversion,amount_conversion, method, payment_date, receipt_date, public_notes, clinic, created_at, uploaded_by, status) VALUES (?, ?, ?, ?,?,?, ?, ?, ?, ?, ?, ?, ?);";
$sql = $conn->prepare($sql_row);

// Se utiliza el nombre del método de pago en lugar del número
$sql->bind_param("isdddssssssii", $lead_id, $type, $_POST['amount'],$price_dls,$advance_amount_dls, $payment_method, $_POST['receipt_date'], $_POST['receipt_date'], $Notas2, $clinic, $created_at, $uploaded_by, $status);

if (!$sql->execute()) {
    throw new Exception("Al crear el registro en la Base de Datos" . $sql->error);
}

$insert_id = $conn->insert_id;
$uploadDirectory = "../../storage/leads/{$lead_id}/receipts/";

if (!file_exists($uploadDirectory)) {
    if (!mkdir($uploadDirectory, 0775, true)) {
        throw new Exception("Al crear la carpeta de Recibos" . $sql->error);
    }
}

$timestamp = time();
$filePath = ($type == "producto") ? "../../storage/leads/{$lead_id}/receipts/producto_{$insert_id}.pdf" : "../../storage/leads/{$lead_id}/receipts/tratamiento_{$insert_id}.pdf";
$mpdf->Output($filePath, 'F');

echo json_encode(["success" => true, "path" => $filePath]);
