<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../common/connection_db.php";
$file = __DIR__ . '/../../../files/cdmx/corte-caja.pdf';

date_default_timezone_set('America/Mexico_City');

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => [215.9, 140]
]);

//!Fecha
$date = $_POST['date'];
$date = explode('-', $date);
$date = $date[2] . "/" . $date[1] . "/" . $date[0];

$efectivo_mxn = number_format($_POST['efectivo_mxn'], 2, '.', ',');
$efectivo_usd = number_format($_POST['efectivo_usd'], 2, '.', ',');
$efectivo_convertido = number_format($_POST['efectivo_convertido'], 2, '.', ',');
$tarjeta = number_format($_POST['tarjeta'], 2, '.', ',');
$deposito = number_format($_POST['deposito'], 2, '.', ',');
$transferencia = number_format($_POST['transferencia'], 2, '.', ',');
$cierre = number_format($_POST['cierre'], 2, '.', ',');

$mpdf->SetTitle("Recibo");

$pagecount = $mpdf->SetSourceFile($file);
$tplId = $mpdf->ImportPage($pagecount);
$mpdf->UseTemplate($tplId);
$mpdf->SetFont('leagues', 'B', 14);

//!Fecha 
$mpdf->WriteText(28, 43, $_SESSION['user_name']);
$mpdf->WriteText(170, 25.5, $date);
$mpdf->WriteText(58, 56, $efectivo_mxn);
$mpdf->WriteText(58, 72.7, $efectivo_convertido);
$mpdf->SetFont('leagues', 'B', 8);
$mpdf->WriteText(90, 72.7, "({$efectivo_usd}$)");
$mpdf->SetFont('leagues', 'B', 14);
$mpdf->WriteText(58, 89.5, $tarjeta);
$mpdf->WriteText(58, 106.3, $deposito);
$mpdf->WriteText(58, 122.6, $transferencia);
$mpdf->WriteText(146.5, 58.5, $cierre);

$mxn_cash = $_POST['efectivo_mxn'];
$usd_cash = $_POST['efectivo_usd'];
$converted_usd_cash = $_POST['efectivo_convertido'];
$exchange_rate = ($usd_cash > 0) ? $converted_usd_cash / $usd_cash : 0;
$card_total = $_POST['tarjeta'];
$deposit_total = $_POST['deposito'];
$transfer_total = $_POST['transferencia'];
$total = $_POST['cierre'];
$date = $_POST['date'];
$uploaded_by = $_SESSION['user_id'];
$notes = $_POST['notes'];
$approved = 0;

$clinic = $_POST['clinic'];

if ($clinic == "Santafe") {
    $sql_public_id = "SELECT public_id FROM sa_corte_caja WHERE id = (SELECT MAX(id) FROM sa_corte_caja WHERE public_id LIKE '%RDI-SF%');";
    $prefix = "RDI-SF-";
    $clinic = "Santa Fe";
} else if ($clinic == "Pedregal") {
    $sql_public_id = "SELECT public_id FROM sa_corte_caja WHERE id = (SELECT MAX(id) FROM sa_corte_caja WHERE public_id LIKE '%RDI-P%');";
    $prefix = "RDI-P-";
    $clinic = "Pedregal";
} else if ($clinic == "Queretaro") { // Nueva condición para Queretaro
    $sql_public_id = "SELECT public_id FROM sa_corte_caja WHERE id = (SELECT MAX(id) FROM sa_corte_caja WHERE public_id LIKE '%RDI-Q%');";
    $prefix = "RDI-Q-";
    $clinic = "Queretaro";
}

$query = mysqli_query($conn, $sql_public_id);
if (mysqli_num_rows($query) == 1) {
    $row = mysqli_fetch_assoc($query);
    $last_public_id = $row['public_id'];
    $last_public_id = explode("-", $last_public_id);
    $public_id_number = $last_public_id[2];
} else {
    $public_id_number = 0;
}

$new_public_id_number = intval($public_id_number) + 1;
$new_public_id = $prefix . $new_public_id_number;

$sql_row = "INSERT INTO sa_corte_caja (mxn_cash, usd_cash, exchange_rate, converted_usd_cash, card_total, deposit_total, transfer_total, total, public_id, date, uploaded_by, notes, clinic, approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
$sql = $conn->prepare($sql_row);

$sql->bind_param("ddddddddssissi", $mxn_cash, $usd_cash, $exchange_rate, $converted_usd_cash, $card_total, $deposit_total, $transfer_total, $total, $new_public_id, $date, $uploaded_by, $notes, $clinic, $approved);
if ($sql->execute() && $sql->affected_rows > 0) {
    $insert_id = $conn->insert_id;

    $uploadDirectory = "/var/www/html/CDMX3/files/cdmx/corte-caja/{$new_public_id}/";

    // Create the uploads directory if it doesn't exist
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }
    $filePath = $uploadDirectory . "corte_caja_{$new_public_id}.pdf";
    $mpdf->Output($filePath, 'F');
    echo json_encode(["success" => true, "message" => 'Recibo generado correctamente', "path"=>$uploadDirectory]);
} else {
    echo json_encode(["success" => false, "message" => $clinic]);
}
