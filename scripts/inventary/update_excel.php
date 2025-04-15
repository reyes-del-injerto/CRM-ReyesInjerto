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
$archivoExcel = './in_5_12_2024_completo.xlsx';

// Cargar el archivo Excel
$spreadsheet = IOFactory::load($archivoExcel);
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray(null, true, true, true); // Convertir la hoja a un array

// Recorrer las filas del Excel y actualizar los registros en la base de datos
foreach ($data as $key => $row) {
    // Saltar la primera fila si es un encabezado
    if ($key == 1) {
        continue;
    }

    // Verificar que el ID existe y es válido
    $id = isset($row['A']) ? intval($row['A']) : null;
    if (empty($id)) {
        echo "ID inválido en la fila $key, omitiendo actualización.<br>";
        continue;
    }

    // Verificar y preparar los valores solo si no son nulos o vacíos
    $name = (!empty($row['B']) && strtolower($row['B']) !== 'null') ? $row['B'] : null;
    $description = (!empty($row['C']) && strtolower($row['C']) !== 'null') ? $row['C'] : null;
    $category = (!empty($row['D']) && strtolower($row['D']) !== 'null') ? $row['D'] : null;
    $quantity_package = (isset($row['E']) && strtolower($row['E']) !== 'null' && $row['E'] !== '') ? intval($row['E']) : null;
    $unit = (!empty($row['F']) && strtolower($row['F']) !== 'null') ? $row['F'] : null;
    $add = (isset($row['G']) && strtolower($row['G']) !== 'null' && $row['G'] !== '') ? intval($row['G']) : 0;

    // Primero, obtener el stock actual del producto
    $query = "SELECT stock FROM ad_inventory_items WHERE id = $id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Obtener el stock actual
        $current_stock = $result->fetch_assoc()['stock'];
        $new_stock = $current_stock + $add; // Actualizar el stock sumando 'add'

        // Construir la consulta de actualización dinámica
        $updateFields = [];
        
        if ($name !== null) $updateFields[] = "name = '$name'";
        if ($description !== null) $updateFields[] = "description = '$description'";
        if ($category !== null) $updateFields[] = "category = '$category'";
        if ($quantity_package !== null) $updateFields[] = "quantity_package = $quantity_package";
        if ($unit !== null) $updateFields[] = "unit = '$unit'";
        
        // Incluir siempre el stock actualizado
        $updateFields[] = "stock = $new_stock";

        // Si hay campos para actualizar, construir y ejecutar la consulta
        if (!empty($updateFields)) {
            $sql = "UPDATE ad_inventory_items SET " . implode(', ', $updateFields) . " WHERE id = $id";

            if ($conn->query($sql) === TRUE) {
                echo "- Registro actualizado correctamente para: $name (ID: $id) - Nuevo stock: $new_stock<br>";
            } else {
                echo "Error al actualizar el registro con ID: $id. Error: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "Producto con ID: $id no encontrado. No se puede actualizar el stock.<br>";
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
