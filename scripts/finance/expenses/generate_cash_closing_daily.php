<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
header('Content-Type: application/json');

session_start();

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../common/connection_db.php";

date_default_timezone_set('America/Mexico_City');

// Validación de campos requeridos
$required_fields = ['tableData', 'fecha', 'clinic','user_id'];
if (empty($_POST['tableData']) || empty($_POST['fecha']) || empty($_POST['user_id']) || empty($_POST['clinic'])) {
    echo json_encode([
        "success" => false,
        "message" => "El campo 'tableData', 'fecha' o 'clinic' es obligatorio y falta en la solicitud."
    ]);
    exit;
}


$userid = $_POST['user_id'];
// Consulta para obtener el nombre del usuario
try {
    $query = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($nombre_usuario);
    $stmt->fetch();
    $stmt->close();

    // Verificar si se obtuvo el nombre
    if (!$nombre_usuario) {
        throw new Exception("No se encontró el nombre del usuario con ID: $userid.");
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al obtener el nombre del usuario: " . $e->getMessage()
    ]);
    exit;
}



// Recibir la imagen en formato Base64 si está presente
$firmaBase64 = $_POST['firma'] ?? null;

// Eliminar el prefijo 'data:image/png;base64,' si está presente
if ($firmaBase64 && strpos($firmaBase64, 'data:image/png;base64,') === 0) {
    $firmaBase64 = substr($firmaBase64, strlen('data:image/png;base64,'));
}

try {
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => [215.9, 140]
    ]);

    $file = __DIR__ . '/../../../files/cdmx/corte_de_caja_diario.pdf';
    $pagecount = $mpdf->SetSourceFile($file);
    $tplId = $mpdf->ImportPage($pagecount);
    $mpdf->UseTemplate($tplId);
    $mpdf->SetFont('leagues', 'B', 14);

    // Datos del formulario
    $date = $_POST['fecha'];
    $userid = $_POST['user_id'];
    $date_formatted = explode('-', $date);
    $date_formatted = $date_formatted[2] . "/" . $date_formatted[1] . "/" . $date_formatted[0];

    // Formatear fecha para el nombre del archivo
    $date_for_filename = date('dmy', strtotime($date));
    $clinic = $_POST['clinic'];
    $filename = "corte_caja_{$clinic}_{$date_for_filename}.pdf";

    $tableData = json_decode($_POST['tableData'], true);

    // Acceder a los datos de la primera sub-matriz en tableData
    $dataRow = $tableData[0] ?? [];
    
    // Función para limpiar y convertir valores a números
    function limpiar_valor($valor) {
        $valor = str_replace(['$', ','], '', $valor);
        return (float)$valor;
    }

    // Asignar valores a variables desde el array basado en el nuevo orden
    $efectivo = limpiar_valor($dataRow[0] ?? '0.00');
    $dolares = limpiar_valor($dataRow[1] ?? '0.00');
    $tarjeta = limpiar_valor($dataRow[2] ?? '0.00');
    $deposito = limpiar_valor($dataRow[3] ?? '0.00');
    $transferencia = limpiar_valor($dataRow[4] ?? '0.00');
    $otro = limpiar_valor($dataRow[5] ?? '0.00');
    $enlace = limpiar_valor($dataRow[6] ?? '0.00');
    $credito = limpiar_valor($dataRow[7] ?? '0.00');
    $debito = limpiar_valor($dataRow[8] ?? '0.00');

    // Sumar Transferencia + Enlace Digital
    $transferencia_total = $transferencia + $enlace;
    $totales_tajeta = $credito + $debito;

    // Sumar totales de categorías
    $total = $efectivo + $dolares + $tarjeta + $deposito + $transferencia_total + $otro + $credito + $debito;

    // Formatear valores
    $efectivo = number_format($efectivo, 2, '.', ',');
    $dolares = number_format($dolares, 2, '.', ',');
    $tarjeta = number_format($tarjeta, 2, '.', ',');
    $deposito = number_format($deposito, 2, '.', ',');
    $transferencia_total = number_format($transferencia_total, 2, '.', ',');
    $creditoYdebito_total = number_format($totales_tajeta, 2, '.', ',');
    $otro = number_format($otro, 2, '.', ',');
    $credito = number_format($credito, 2, '.', ',');
    $debito = number_format($debito, 2, '.', ',');
    $total = number_format($total, 2, '.', ',');

    // Actualizar el PDF con los valores en las posiciones correctas
    $mpdf->WriteText(28, 43, $nombre_usuario); // Usar nombre del usuario
    $mpdf->WriteText(170, 25.5, $date_formatted);
    $mpdf->WriteText(58, 56, $efectivo);
    $mpdf->WriteText(58, 72.7, $dolares);
    $mpdf->WriteText(58, 89.5, $creditoYdebito_total);
    $mpdf->WriteText(58, 106.5, $deposito);
    $mpdf->WriteText(58, 122.3, $transferencia_total); // Imprimir Transferencia + Enlace Digital
    $mpdf->WriteText(150, 56.6, $otro);
    $mpdf->WriteText(150, 72.7, $total);

    // Añadir la firma al PDF si está disponible
    $firma_recibida = false;
    $firma_base64_response = null;
    if ($firmaBase64) {
        $firmaImage = base64_decode($firmaBase64);

        // Verifica si la decodificación fue exitosa
        if ($firmaImage === false) {
            throw new Exception("Error al decodificar la firma.");
        }

        $tempImagePath = __DIR__ . '/../../../files/temp_signature.png';
        file_put_contents($tempImagePath, $firmaImage);

        // Verifica si el archivo temporal se creó correctamente
        if (!file_exists($tempImagePath)) {
            throw new Exception("Error al guardar la firma temporalmente.");
        }

        // Añadir la imagen al PDF en las coordenadas 160, 92.7
        $mpdf->Image($tempImagePath, 160, 92.7, 50, 30, 'png', '', true, false);

        // Eliminar la imagen temporal después de añadirla
        unlink($tempImagePath);

        $firma_recibida = true;
        $firma_base64_response = $_POST['firma']; // Incluir la firma Base64 original en la respuesta
    }

    // Insertar el nombre de la clínica
    $mpdf->SetFont('leagues', 'B', 12); // Ajustar tamaño de fuente si es necesario
    $mpdf->WriteText(28, 132, $clinic); // Ajustar las coordenadas para colocar el nombre de la clínica

    // Guardar el PDF
    $uploadDirectory = "/var/www/html/CDMX3/files/cdmx/corte-caja/";
    $uploadDirectory = "../../../files/cdmx/corte-caja/";
    
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }
    $filePath = $uploadDirectory . $filename;
    $mpdf->Output($filePath, 'F');

    // Actualizar la base de datos con la ruta del archivo PDF
    $sql_row = "UPDATE daily_cortes SET pdf = ? WHERE dia = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ss", $filename, $_POST['fecha']);

    if ($sql->execute() && $sql->affected_rows > 0) {
        // Definir el path base que queremos eliminar
        $basePath = '../../..';
        $relativePath = str_replace($basePath, '', $filePath); // Eliminar el prefijo

        echo json_encode([
            "success" => true,
            "message" => 'Recibo generado correctamente',
            "path" => $relativePath, // Ruta relativa
            "received_data" => [
                "fecha" => $date,
                "tableData" => $tableData,
                "formatted_data" => [
                    "efectivo" => $efectivo,
                    "dolares" => $dolares,
                    "tarjeta" => $tarjeta,
                    "deposito" => $deposito,
                    "transferencia_total" => $transferencia_total,
                    "otro" => $otro,
                    "credito" => $credito,
                    "totales_tarjeta" => $totales_tajeta,
                    "clinic" => $clinic,
                    "debito" => $debito
                ],
                "total" => $total, // Total de todas las categorías
                "firma_recibida" => $firma_recibida, // Indica si la firma fue recibida
                "firma_base64" => $firma_base64_response // Respuesta de la firma en Base64
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => 'No se pudo guardar el PDF en la base de datos.',
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
