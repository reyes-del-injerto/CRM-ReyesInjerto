<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require '../common/connection_db.php'; // Incluir la conexión
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

header('Content-Type: application/json');

// Inicializa variables
$types = "s"; // Inicializa como una cadena
$total_values = [];

$response = []; // Inicializa respuesta
$dataArray = []; // Inicializa un array para almacenar los datos

try {
    // Verifica si se ha recibido la categoría a través de POST
    if (!isset($_POST['category'])) {
        throw new Exception('No se recibió la categoría.');
    }

    $category = $_POST['category'];

    // Verifica si la categoría es "All"
    if ($category === "All") {
        // Si la categoría es "All", selecciona todos los ítems sin filtrar
        $sql = "SELECT `id`, `name`, `description`, `category`, `stock`, `minimum_required`, `location`
                FROM `ad_inventory_items`
                ORDER BY `category` ASC";
    } else {
        // Si no es "All", aplica el filtro por categoría
        $sql = "SELECT `id`, `name`, `description`, `category`, `stock`, `minimum_required`, `location`
                FROM `ad_inventory_items`
                WHERE `category` = ?
                ORDER BY `category` ASC";
        $total_values = [$category];
    }

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare($sql);

    // Si no es "All", vincula el valor de la categoría
    if ($category !== "All") {
        $stmt->bind_param($types, ...$total_values);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si hay resultados
    if ($result->num_rows === 0) {
        throw new Exception('No se encontraron ítems en la base de datos.');
    }

    // Almacena los datos en un array para ordenarlos después
    while ($data = $result->fetch_object()) {
        $pendientePorComprar = max(0, $data->minimum_required - $data->stock);
        $dataArray[] = (object) [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'category' => $data->category,
            'stock' => $data->stock,
            'minimum_required' => $data->minimum_required,
            'location' => $data->location,
            'pendientePorComprar' => $pendientePorComprar
        ];
    }

    // Ordenar el array por "Pendiente por Comprar" de mayor a menor
    usort($dataArray, function($a, $b) {
        return $b->pendientePorComprar <=> $a->pendientePorComprar; // Orden descendente
    });

    // Crear el archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Inventario Categoría');

    // Encabezados del archivo Excel
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Nombre del Ítem');
    $sheet->setCellValue('C1', 'Descripción');
    $sheet->setCellValue('D1', 'Categoría');
    $sheet->setCellValue('E1', 'Mínimo Requerido');
    $sheet->setCellValue('F1', 'Stock Actual');
    $sheet->setCellValue('G1', 'Pendiente por Comprar');

    // Ajusta el ancho de las columnas
    $sheet->getColumnDimension('E')->setWidth(35);
    $sheet->getColumnDimension('F')->setWidth(35);
    $sheet->getColumnDimension('B')->setWidth(35);
    $sheet->getColumnDimension('C')->setWidth(50);
    $sheet->getColumnDimension('D')->setWidth(20);

    // Escribir los datos en el archivo Excel
    $rowNumber = 2;
    foreach ($dataArray as $data) {
        $sheet->setCellValue('A' . $rowNumber, $data->id);
        $sheet->setCellValue('B' . $rowNumber, $data->name);
        $sheet->setCellValue('C' . $rowNumber, $data->description);
        $sheet->setCellValue('D' . $rowNumber, $data->category);
        $sheet->setCellValue('E' . $rowNumber, $data->minimum_required);
        $sheet->setCellValue('F' . $rowNumber, $data->stock);
        $sheet->setCellValue('G' . $rowNumber, $data->pendientePorComprar);
        $rowNumber++;
    }

    // Define la ruta del archivo y asegúrate de que el directorio exista
    $filename = 'inventario_categoria_' . ($category === "All" ? 'todos' : $category) . '.xlsx';
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
    $fileUrl = './scripts/inventary/' . $filename;
    $response['url'] = $fileUrl;

} catch (Exception $e) {
    // Manejo de errores
    $response['error'] = $e->getMessage();
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
