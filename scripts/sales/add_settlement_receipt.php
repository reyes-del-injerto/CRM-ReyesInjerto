<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . '/../common/connection_db.php';
require_once __DIR__ . "/../common/bunnynet.php";
require_once __DIR__ . '/../../vendor/autoload.php';
/* formData 
lead_id: 3
receipt_date: 2024-06-14
patient_name: Eliezer Solano Martinez
procedure_type: Barba
advance_date: 2024-06-15
advance_amount: 4000
advance_payment_method: TDC
settlement_date: 2024-06-16
total_amount: 33000
settlement_amount: 29000
settlement_payment_method: Depósito
clinic: Pedregal
public_notes: abc
*/
try {
    $success = false;
    $created_at = date("Y-m-d H:i:s");
    $uploaded_by = $_POST['userid'];
    $status = 1;

    //Lead_ID
    $id = $_POST['lead_id'];
    $receipt_type = "liquidacion";
    $clinic = $_POST['clinic'];

    $receipt_date = $_POST['receipt_date'];
    $date = parse_date($receipt_date);

    $name = $_POST['patient_name'];
    $type = $_POST['procedure_type'];

    $conversion = $_POST['price_dls'];
    $amount_dls = $_POST['advance_amount_dls'];

    $total_amount = $_POST['total_amount'];
    $parsed_total_amount = "$" . number_format($total_amount, 2, '.', ',');

    //Datos del Anticipo
    $advance_amount = $_POST['advance_amount'];
    $parsed_advance_amount = "$" . number_format($advance_amount, 2, '.', ',');

    $advance_payment_method = (isset($_POST['advance_payment_method']) && !empty(trim($_POST['advance_payment_method']))) ? $_POST['advance_payment_method'] : '';

    $advance_date = $_POST['advance_date'];
    $formatted_advance_date = parse_date($advance_date);

    //Datos de la liquidación
    $settlement_amount = $_POST['settlement_amount'];
    $parsed_settlement_amount = format_amount($settlement_amount);

    $settlement_payment_method = $_POST['settlement_payment_method'];

    $settlement_date = $_POST['settlement_date'];
    $formatted_settlement_date = parse_date($settlement_date);

    $Notas2 = nl2br($_POST['public_notes']);
    $Notas = explode("<br />", $Notas2);
    $Nota1 = isset($Notas[0]) ? ltrim(rtrim($Notas[0])) : '';
    $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
    $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';

    $file = ($clinic == "Pedregal") ? '../../files/cdmx/pago-pedregal.pdf'
        : (($clinic == "Santa Fe") ? '../../files/cdmx/pago-santafe.pdf'
            : (($clinic == "Queretaro") ? '../../files/cdmx/liquidacion-qro.pdf' : '../../files/cdmx/pago-default.pdf'));

    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'Letter'
    ]);

    $mpdf->SetTitle("Liquidacion_" . $name);

    $pagecount = $mpdf->SetSourceFile($file);
    $tplId = $mpdf->ImportPage($pagecount);
    $mpdf->UseTemplate($tplId);
    $mpdf->SetFont('leagues', 'B', 14);

    if ($clinic == "Queretaro") {
        // Mover la fecha más arriba para Queretaro
        $mpdf->WriteText(155, 46.79, $date); // Y coordenada 48.79 en vez de 50.79
    } else {
        $mpdf->WriteText(155, 50.79, $date);
    }

    $mpdf->WriteText(45.90, 72.34, $name);

    $mpdf->WriteText(60.90, 85, $parsed_total_amount);

    // Escribir el tipo de procedimiento (Capilar, Barba o Ambos)
    if ($clinic == "Queretaro") {
        // Mover las marcas "X" un poco más a la derecha para Queretaro
        if ($type == "Capilar") {
            $mpdf->WriteText(136.3, 83, "X"); // X coordenada 136.3 en vez de 134.3
        } else if ($type == "Barba") {
            $mpdf->WriteText(183.90, 83, "X"); // X coordenada 183.90 en vez de 181.90
        } else if ($type == "Ambos") {
            $mpdf->WriteText(136.3, 83, "X"); // X coordenada 136.3 en vez de 134.3
            $mpdf->WriteText(185.90, 85, "X"); // X coordenada 185.90 en vez de 183.90
        }
    } else {
        // Código existente para otras clínicas
        if ($type == "Capilar") {
            $mpdf->WriteText(134.3, 83, "X");
        } else if ($type == "Barba") {
            $mpdf->WriteText(181.90, 83, "X");
        } else if ($type == "Ambos") {
            $mpdf->WriteText(134.3, 83, "X");
            $mpdf->WriteText(183.90, 85, "X");
        }
    }

    //Escribir datos de anticipo
    $mpdf->WriteText(38.56, 117.74, $parsed_advance_amount);

    if ($advance_payment_method == "TDD" || $advance_payment_method == "TDC") {
        $mpdf->WriteText(103.12, 117.74, $advance_payment_method);
    } else if ($advance_payment_method == "Efectivo" || $advance_payment_method == "Depósito") {
        $mpdf->WriteText(97.80, 117.74, $advance_payment_method);
    } else {
        $mpdf->WriteText(91.80, 117.74, $advance_payment_method);
    }

    $mpdf->WriteText(150.56, 117.74, $formatted_advance_date);

    //Escribir datos de la liquidación
    $mpdf->WriteText(38.56, 147.74, $parsed_settlement_amount);

    if ($advance_payment_method == "TDD" || $settlement_payment_method == "TDC") {
        $mpdf->WriteText(103.12, 147.74, $settlement_payment_method);
    } else if ($advance_payment_method == "Efectivo" || $settlement_payment_method == "Depósito") {
        $mpdf->WriteText(97.80, 147.74, $settlement_payment_method);
    } else {
        $mpdf->WriteText(91.80, 147.74, $settlement_payment_method);
    }

    $mpdf->WriteText(150.56, 147.74, $formatted_settlement_date);

    $mpdf->WriteText(33.85, 181.3, $Nota1);
    $mpdf->WriteText(33.85, 187.28, $Nota2);
    $mpdf->WriteText(33.85, 194, $Nota3);


    $sql_row = "INSERT INTO sa_info_payment_px (lead_id, type, amount,conversion,amount_conversion, method, payment_date, receipt_date, public_notes, clinic, created_at, uploaded_by, status) VALUES (?, ?, ?,?,?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $sql = $conn->prepare($sql_row);

    $sql->bind_param("isdddssssssii", $id, $receipt_type, $settlement_amount,$conversion,$amount_dls, $settlement_payment_method, $settlement_date, $receipt_date, $Notas2, $clinic, $created_at, $uploaded_by, $status);

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

    $filePath = "../../storage/leads/{$id}/receipts/liquidacion_{$insert_id}.pdf";

    if ($mpdf->Output($filePath, 'F') === false) {
        throw new Exception("Al generar el archivo PDF" . $sql->error);
    }

    $success = true;
    $message = "Done";
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
