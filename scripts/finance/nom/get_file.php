<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

require_once '../../common/connection_db.php';

// Crear archivo con los datos obtenidos de la base de datos
createFile($conn);

function createBlankString($length)
{
    return str_repeat(' ', $length);
}

function createFile($conn)
{
    // Consulta a la base de datos para obtener los datos
    $sql = "SELECT num_progresivo, cuenta, importe, nombre FROM ad_nomina"; // Ajusta esta consulta a tus necesidades
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $lineaFinal = '';

        while ($row = $result->fetch_assoc()) {
            $id = rtrim(ltrim($row['num_progresivo']));
            $paddedId = str_pad($id, 9, '0', STR_PAD_LEFT);
            $cuenta = rtrim(ltrim($row['cuenta']));
            $importe_raw = rtrim(ltrim($row['importe']));
            $importe = str_replace(".", "", $importe_raw);
            $paddedImporte = str_pad($importe, 15, '0', STR_PAD_LEFT);
            $nombre = rtrim(ltrim($row['nombre']));

            // Formar la línea
            $linea = $paddedId . "                " . $cuenta . "          " . $paddedImporte . $nombre;

            // Asegurarse de que la línea tenga exactamente 102 caracteres
            $lineLength = strlen($linea);
            if ($lineLength < 102) {
                $blankString = createBlankString(102 - $lineLength);
                $linea .= $blankString;
            } elseif ($lineLength > 102) {
                // Si la línea es más larga de lo esperado, cortar la línea (esto es raro, pero por si acaso)
                $linea = substr($linea, 0, 102);
            }

            // Agregar el código final y salto de línea
            $lineaFinal .= $linea . "001001\r\n";
        }

        // Guardar el contenido en un archivo
        $file = '../../../files/cdmx/layout_bbva.txt';
        if (file_put_contents($file, $lineaFinal) !== false) {
            echo json_encode(['status' => 'success', 'message' => 'Plantilla generada correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al exportar los datos.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos para exportar.']);
    }
}

?>
