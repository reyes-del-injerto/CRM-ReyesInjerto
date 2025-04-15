<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/connection_db.php";
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

header('Content-Type: application/json');

// Inicializa variables
$types = "sss"; // Incluye un tercer "s" para el especialista
$total_values = [];

try {
    // Verifica si se han recibido el mes y el especialista a través de POST
    if (!isset($_POST['month']) || !isset($_POST['especialista'])) {
        throw new Exception('No se recibieron todos los parámetros.');
    }

    $month = $_POST['month'];
    $specialist = $_POST['especialista'];

    // Define las fechas de inicio y fin del mes
    $currentYear = date('Y');
    $startDate = "$currentYear-$month-01 00:00:00";
    $endDate = date("Y-m-t 23:59:59", strtotime($startDate));

    $total_values = [$startDate, $endDate, $specialist];

    // Define la consulta SQL con filtro por especialista
    $sql = "SELECT DISTINCT sla.lead_id, CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
            DATE_FORMAT(sla.procedure_date, '%d/%m/%y') AS procedure_date, 
            sla.procedure_type, ep.specialist, ep.num_med_record
            FROM enf_procedures ep 
            INNER JOIN (
                SELECT lead_id, MAX(procedure_date) AS procedure_date
                FROM sa_leads_assessment
                WHERE status = 1
                GROUP BY lead_id
            ) AS latest_sla ON ep.lead_id = latest_sla.lead_id
            INNER JOIN sa_leads_assessment sla ON sla.lead_id = latest_sla.lead_id 
            AND sla.procedure_date = latest_sla.procedure_date
            WHERE sla.procedure_date BETWEEN ? AND ?
            AND ep.specialist = ?
            ORDER BY sla.procedure_date ASC";

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$total_values);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear el archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Procedimientos');

    // Encabezados del archivo Excel
    $sheet->setCellValue('A1', 'Número Secuencial');
    $sheet->setCellValue('B1', 'Nombre del Paciente');
    $sheet->setCellValue('C1', 'Número de exp');
    $sheet->setCellValue('D1', 'Fecha');
    $sheet->setCellValue('E1', 'Tipo de Procedimiento');
    $sheet->setCellValue('F1', 'Especialista');

    // Ajusta el ancho de la columna B para que sea más ancha
    $sheet->getColumnDimension('B')->setWidth(35);

    // Escribir los datos en el archivo Excel con número secuencial
    $rowNumber = 2;
    $sequentialNumber = 1;
    while ($data = $result->fetch_object()) {
        $sheet->setCellValue('A' . $rowNumber, $sequentialNumber);
        $sheet->setCellValue('B' . $rowNumber, $data->name);
        $sheet->setCellValue('C' . $rowNumber, $data->num_med_record);
        $sheet->setCellValue('D' . $rowNumber, $data->procedure_date);
        $sheet->setCellValue('E' . $rowNumber, $data->procedure_type);
        $sheet->setCellValue('F' . $rowNumber, $data->specialist);
        $rowNumber++;
        $sequentialNumber++;
    }

    // Define la ruta del archivo y asegúrate de que el directorio exista
    $filename = 'procedimientos_' . $specialist . '_mes_' . $month . '_' . $currentYear . '.xlsx';
    $filePath = './' . $filename;

    // Verifica si el archivo ya existe y elimínalo si es necesario
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Verifica si el directorio existe, si no, créalo
    $directoryPath = dirname($filePath);
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0777, true);
    }

    // Guardar el archivo Excel en la ruta específica
    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    // Devolver la URL del archivo generado
    $fileUrl = './scripts/Admin/especialist/' . $filename;
    echo json_encode(['url' => $fileUrl]);

} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(['error' => $e->getMessage()]);
}
?>
