<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . '/../common/connection_db.php';
require_once __DIR__ . "/../common/bunnynet.php";
require_once __DIR__ . '/../../vendor/autoload.php';


// Define all functions at the beginning
function write_product($product)
{
    global $mpdf;
    // Coordenadas para el producto
    $mpdf->WriteText(32, 86.5, $product);
}

function write_product_custom($product)
{
    global $mpdf;
    // Coordenadas específicas para el tipo Producto
    $mpdf->WriteText(32, 86.5, $product); // Coordenadas actualizadas
}

function choose_treatment($treatment, $clinic)
{
    global $mpdf;

    // Definir coordenadas base
    $x = 36.5;

    // Verificar si la clínica es "Quereraro"
    if ($clinic == 'Queretaro') {
        $x = 30; // Cambiar la coordenada X
    }

    // Dependiendo del tratamiento, ajustar la coordenada Y
    switch ($treatment) {
        case 1:
            $mpdf->WriteText($x, 95.3, 'X');
            break;
        case 2:
            $mpdf->WriteText($x, 104.3, 'X');
            break;
        case 3:
            $mpdf->WriteText($x, 113.8, 'X');
            break;
        default:
            $mpdf->WriteText($x, 80, 'X');
            break;
    }
}


function write_amount_product($amount)
{
    global $mpdf;
    // Coordenadas para el monto del producto
    $mpdf->WriteText(41.2, 122.80, $amount); // Coordenadas actualizadas
}

function write_amount_treatment($amount)
{
    global $mpdf;
    $mpdf->WriteText(41.2, 140.80, $amount);
}

function payment_for_treatments($payment_method, $clinic)
{
    global $mpdf;

    // Verificar si la clínica es "Queretaro" y ajustar la coordenada X
    $x = ($clinic == 'Queretaro') ? 85 : 90.8; // Si la clínica es "Queretaro", X será 88, si no, X será 90.8

    // Según el método de pago, escribir la "X" en las coordenadas correctas
    switch ($payment_method) {
        case 1:
            $mpdf->WriteText($x, 140, 'X');
            break;
        case 2:
            $mpdf->WriteText($x, 149, 'X');
            break;
        case 3:
            $mpdf->WriteText($x, 159, 'X');
            break;
        case 4:
            $mpdf->WriteText($x, 168, 'X');
            break;

        case 5:
            $mpdf->WriteText($x, 177, 'X');
            break;
        default:
            $mpdf->WriteText($x, 140, 'X');
            break;
    }
}


function payment_for_products($payment_method)
{
    global $mpdf;
    switch ($payment_method) {
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
        default:
            $mpdf->WriteText(89.5, 121, 'X');
            break;
    }
}

function write_name($name, $clinic)
{
    global $mpdf;

    // Definir coordenadas base
    $x = 32;
    $y = 71.8; // Valores por defecto

    // Verificar si la clínica es "Queretaro"
    if ($clinic == 'Queretaro') {
        $x = 32; // Cambiar la coordenada X
        $y = 69.8; // Cambiar la coordenada Y (ajustado si es necesario)
    }

    // Escribir el nombre en las coordenadas especificadas
    $mpdf->WriteText($x, $y, $name);
}


function write_date($date)
{
    global $mpdf;
    $mpdf->WriteText(140, 49.5, $date);
}

function write_product_notes($Notas2, $clinic)
{
    $Notas = explode("<br />", $Notas2);
    $Nota1 = $Notas[0];
    $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
    $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';

    global $mpdf;

    // Si la clínica es "Queretaro", ajustar las coordenadas de las notas
    if ($clinic == 'Queretaro') {
        $y1 = 188;  // Coordenada Y ajustada para la primera nota
        $y2 = 195;  // Coordenada Y ajustada para la segunda nota
        $y3 = 202;  // Coordenada Y ajustada para la tercera nota
    } else {
        // Coordenadas predeterminadas
        $y1 = 177.50;
        $y2 = 185.80;
        $y3 = 194.40;
    }

    // Escribir las notas en las nuevas coordenadas
    $mpdf->WriteText(32, $y1, $Nota1);
    $mpdf->WriteText(32, $y2, $Nota2);
    $mpdf->WriteText(32, $y3, $Nota3);

    return true;
}


function write_treatment_notes($Notas2)
{
    $Notas = explode("<br />", $Notas2);
    $Nota1 = $Notas[0];
    $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
    $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';

    global $mpdf;
    $mpdf->WriteText(32, 197.40, $Nota1);
    $mpdf->WriteText(32, 204.60, $Nota2);
    $mpdf->WriteText(32, 213.40, $Nota3);
    return true;
}

try {
    // Mapeo de métodos de pago
    $payment_methods_map = [
        '1' => 'Efectivo',
        '2' => 'Transferencia',
        '3' => 'TDD',
        '4' => 'TDC',
        '5' => 'Dólares',
        '6' => 'Deposito'
    ];

    $type = $_POST['invoice_type'];
    $name = $_POST['full_name'];
    $px_id = $_POST['px_identifier'];
    $px_identifier = $_POST['px_identifier_type']; // Obtener el tipo de identificación
    $px_identifier = strtolower($px_identifier); // Obtener el tipo de identificación
    $amount = "$" . number_format($_POST['amount'], 2, '.', ',');
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'];
    $product = isset($_POST['product']) ? $_POST['product'] : '';
    $treatment = isset($_POST['treatment']) ? $_POST['treatment'] : '';
    $clinic = $_POST['clinic'];

    // Obtener la descripción del método de pago
    if (isset($payment_methods_map[$payment_method])) {
        $payment_method_desc = $payment_methods_map[$payment_method];
    } else {
        throw new Exception("Método de pago inválido.");
    }

    // Verificar el tipo de px_identifier
    if ($px_identifier == 'id') {
        // Continuar con el px_id recibido directamente
    } elseif ($px_identifier == 'exp') {
        // Buscar el ID en enf_treatments utilizando el número de expediente
        $exp_number = $_POST['px_identifier']; // Obtener el número de expediente
        $query = "SELECT id FROM enf_treatments WHERE num_med_record = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $exp_number);
        $stmt->execute();
        $stmt->bind_result($treatment_id);
        $stmt->fetch();
        $stmt->close();

        if (!$treatment_id) {
            throw new Exception("No se encontró un tratamiento con el número de expediente: $exp_number");
        }

        $px_id = $treatment_id; // Usar el ID encontrado
    } else {
        throw new Exception("Tipo de px_identifier no válido.");
    }

    // Determine the base PDF file based on invoice_typeee
    $invoice_typeee = $_POST['invoice_typeee'];
    if ($invoice_typeee == "Producto") {
        $file = "../../files/cdmx/producto-{$clinic}.pdf";
    } else {
        $file = "../../files/cdmx/{$type}-{$clinic}.pdf";
    }

    if ($type == "producto") {
        $product = $_POST['product'];
    }

    $date = $_POST['receipt_date'];
    $date = explode('-', $date);
    $date = $date[2] . "/" . $date[1] . "/" . $date[0];

    $Notas2 = nl2br($_POST['notes']);

    // Inicializar mpdf
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

    // Escribir los datos en el PDF
    write_name($name, $clinic);
    write_date($date);

    if ($invoice_typeee == "Producto") {
        write_product_custom($product);
        write_product_notes($Notas2,$clinic);
        payment_for_products($payment_method);
        write_amount_product($amount);
    } else {
        write_treatment_notes($Notas2);
        payment_for_treatments($payment_method, $clinic);
        write_amount_treatment($amount);
        choose_treatment($treatment, $clinic);
    }

    $created_at = date("Y-m-d H:i:s");
    $uploaded_by = $_SESSION['user_id'];
    $status = 1;

    // Guardar los datos en la base de datos
    $sql_row = "INSERT INTO sa_info_payment_treatments (px_id, type, amount, method, payment_date, receipt_date, public_notes, clinic, created_at, uploaded_by, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("isdssssssii", $px_id, $type, $_POST['amount'], $payment_method_desc, $_POST['receipt_date'], $_POST['receipt_date'], $Notas2, $clinic, $created_at, $uploaded_by, $status);

    if (!$sql->execute()) {
        throw new Exception("Error al crear el registro en la base de datos: " . $sql->error);
    }

    $insert_id = $conn->insert_id;
    $uploadDirectory = realpath(__DIR__ . '/../../storage/trats') . "/{$px_id}/receipts/";

    if (!file_exists($uploadDirectory)) {
        /* if (!is_writable(dirname($uploadDirectory))) {
            throw new Exception("El directorio padre no tiene permisos de escritura: " . dirname($uploadDirectory));
        } */
        if (!mkdir($uploadDirectory, 0775, true)) {
            throw new Exception("No se pudo crear el directorio de recibos en: {$uploadDirectory}");
        }
    }

    // Generar el nombre del archivo PDF basado en el tipo y el ID insertado
    $pdfFileName = ($type == "Producto") ? "producto_{$insert_id}.pdf" : "Recibo_{$insert_id}.pdf";
    $filePath = $uploadDirectory . $pdfFileName;

    // Generar PDF y guardarlo en el sistema de archivos
    $mpdf->Output($filePath, 'F');

    // Construir la URL pública del PDF
    $pdfUrl = "storage/trats/{$px_id}/receipts/{$pdfFileName}";

    // Responder con éxito y la URL del PDF
    echo json_encode(["success" => true, "path" => $pdfUrl]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
