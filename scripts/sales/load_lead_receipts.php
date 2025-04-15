<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

try {
    $lead_id = $_POST['lead_id'];
    $status = 1;
    $receipts = [];
    $debug_info = []; // Variable para almacenar información de depuración

    $sql_row = "SELECT id, lead_id, type, amount, method, DATE_FORMAT(payment_date, '%d/%m/%Y') AS date, public_notes FROM sa_info_payment_px WHERE lead_id = ? AND status = ?;";

    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ii", $lead_id, $status);

    if (!$sql->execute()) {
        throw new Exception($sql->error);
    }
    $pdf_path = "path/to/default/no_file.pdf";
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        while ($data = $result->fetch_object()) {
            // Definir la primera ruta
            $pdf_path = "../../storage/leads/{$data->lead_id}/receipts/{$data->type}_{$data->id}.pdf";
            $debug_info[] = "Intentando ruta 1: $pdf_path";
           
            
            // Comprobar si el archivo existe en la primera ruta
            if (!file_exists($pdf_path)) {
                // Definir la carpeta de la segunda ruta
                $dir_path = "../../../cdmx/temporal_storage/docs/{$data->lead_id}/invoices/";
                $debug_info[] = "Archivo no encontrado en ruta 1. Intentando en carpeta: $dir_path";
                
                // Verificar si hay archivos PDF en la segunda ruta
                $files = glob($dir_path . "*.pdf");
                if (count($files) > 0) {
                    // Tomar el primer archivo encontrado
                    $pdf_path = $files[0];
                    $debug_info[] = "Archivo encontrado en segunda ruta: $pdf_path";
                    $pdf_path = "../cdmx/temporal_storage/docs/{$data->lead_id}/invoices/" . substr($files[0], 50);
                } else {
                    // Si no se encuentra ningún archivo, asignar un valor por defecto (puede ser una imagen o mensaje de "no disponible")
                    $pdf_path = "#";
                    $debug_info[] = "Ningún archivo PDF encontrado en la segunda ruta.";
                }
            } else {
                $debug_info[] = "Archivo encontrado en primera ruta: $pdf_path";
                $pdf_path = "storage/leads/{$data->lead_id}/receipts/{$data->type}_{$data->id}.pdf";
            }
            
            // Generar las opciones del botón
            $options = "
            <a href='$pdf_path' target='_blank' class='btn btn-dark btn-sm btn-square rounded-pill'><i class='btn-icon fa fa-external-link'></i> </a>
            <a href='#' data-id='{$data->id}' class='btn btn-danger btn-sm btn-square rounded-pill delete_receipt'><i class='btn-icon fa fa-trash'></i> </a>";

            $amount = "$" . number_format($data->amount, 2, '.', ',');

            $receipts[] = array(
                $data->id,
                ucfirst($data->type),
                $data->date,
                $amount,
                $data->method,
                $data->public_notes,
                $options
            );
        }
    }
    $success = true;
    $message = "Done";
} catch (Exception $e) {
    $success = false;
    $message = $e->getMessage();
    $debug_info[] = "Excepción capturada: " . $e->getMessage();
}

$data  = [
    "success" => $success,
    "message" => $message,
    "data" => $receipts,
    "debug_info" => $debug_info
];

echo json_encode($data);
?>
