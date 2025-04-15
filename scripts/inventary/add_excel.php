<?php
// Configuraciones de errores y zona horaria
ini_set('error_reporting', -1);
ini_set('memory_limit', '512M');
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require '../common/connection_db.php'; // Incluir la conexión

// Incluyendo la librería de PhpSpreadsheet
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Ruta del archivo Excel
$archivoExcel = './qro2_inv.xlsx';

// Cargar el archivo Excel
$spreadsheet = IOFactory::load($archivoExcel);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray(null, true, true, true); // Convertir la hoja a un array

// Recorrer las filas del Excel y guardarlas en la base de datos
foreach ($data as $key => $row) {
    // Saltar la primera fila si es un encabezado
    if ($key == 1) {
        continue;
    }

    // Asumimos que el archivo tiene las columnas: name, description, category, stock, minimum_required, location
    $name = $row['A'];  // Columna 'NAME'
    $description = $row['B'];  // Columna 'DESCRIPCION'
    $category = $row['C'];  // Columna 'CATEGORIA'
    $cantidad_por_paquete = intval($row['D']); // Columna 'Cantidad por paquete'
    $unidad = $row['E']; // Columna 'Unidad'
    $stock = intval(preg_replace('/\D/', '', $row['F'])); // Columna 'EXISTENCIA', limpiando texto
    $minimum_required = intval($row['G']); // Columna 'Minimo Requerido'
    $clinic = $row['H']; // Columna 'Clinic'

    // Verificar si los valores requeridos están definidos                      
    if (!empty($name) && isset($stock) && !empty($clinic)) {
        // Insertar en la base de datos con el orden correcto
        $sql = "INSERT INTO ad_inventory_items (name, description, category, quantity_package, unit, stock, minimum_required, location,clinic) 
        VALUES ('$name', '$description', '$category', $cantidad_por_paquete, '$unidad', $stock, '$minimum_required', 'Bodega','$clinic')";


        if ($conn->query($sql) === TRUE) {
            echo "- Registro insertado correctamente para: $name<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Faltan datos en alguna fila, omitiendo la inserción para $name.<br>";
    }
}

$conn->close();
