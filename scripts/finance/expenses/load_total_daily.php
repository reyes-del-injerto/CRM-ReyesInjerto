<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../../common/utilities.php";
require_once __DIR__ . "/../../common/connection_db.php";

// Obtén la fecha y la clínica desde las variables POST
$fechaSeleccionada = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$clinicSeleccionada = isset($_POST['clinic']) ? $_POST['clinic'] : null;
$success = "false";

// Verifica si se ha proporcionado una fecha y una clínica válidas
if ($fechaSeleccionada && $clinicSeleccionada) {
    // Sanitiza las entradas para evitar inyecciones SQL
    $fechaSeleccionada = $conn->real_escape_string($fechaSeleccionada);
    $clinicSeleccionada = $conn->real_escape_string($clinicSeleccionada);

    // Para depuración: imprime la fecha y el formato
    $fechaFormato = date('Y-m-d', strtotime($fechaSeleccionada)); // Formato esperado en la consulta
    $responseDebug = [
        "fecha_recibida" => $fechaSeleccionada,
        "formato_fecha" => $fechaFormato
    ];

    // Consulta para obtener el total por método de pago filtrado por fecha y clínica en `sa_info_payment_px`
    $totalPxSql = "SELECT
                    p.method AS metodo_de_pago,
                    SUM(p.amount) AS total_importe
                 FROM sa_info_payment_px p
                 WHERE DATE(p.payment_date) = '$fechaFormato'
                 AND p.clinic = '$clinicSeleccionada'  AND status=1
                 GROUP BY p.method";
    
    $totalsPxResult = $conn->query($totalPxSql);

    $totals = [];

    if ($totalsPxResult->num_rows > 0) {
        while ($totalRow = $totalsPxResult->fetch_assoc()) {
            $metodoPago = $totalRow["metodo_de_pago"] ?? 'NA';
            $totalImporte = $totalRow["total_importe"] ?? 0;

            if (isset($totals[$metodoPago])) {
                $totals[$metodoPago] += $totalImporte;
            } else {
                $totals[$metodoPago] = $totalImporte;
            }
        }
        $success = "true";
    }

    // Consulta para obtener el total por método de pago filtrado por fecha y clínica en `sa_info_payment_treatments`
    $totalTreatmentsSql = "SELECT
                            t.method AS metodo_de_pago,
                            SUM(t.amount) AS total_importe
                         FROM sa_info_payment_treatments t
                         WHERE DATE(t.payment_date) = '$fechaFormato'
                         AND t.clinic = '$clinicSeleccionada'
                         GROUP BY t.method";
    
    $totalsTreatmentsResult = $conn->query($totalTreatmentsSql);

    if ($totalsTreatmentsResult->num_rows > 0) {
        while ($totalRow = $totalsTreatmentsResult->fetch_assoc()) {
            $metodoPago = $totalRow["metodo_de_pago"] ?? 'NA';
            $totalImporte = $totalRow["total_importe"] ?? 0;

            if (isset($totals[$metodoPago])) {
                $totals[$metodoPago] += $totalImporte;
            } else {
                $totals[$metodoPago] = $totalImporte;
            }
        }
        $success = "true";
    }

    $conn->close();

    // Formatear los resultados para la salida JSON
    $combinedTotals = [];
    foreach ($totals as $metodoPago => $totalImporte) {
        $combinedTotals[] = [
            "metodo_de_pago" => $metodoPago,
            "total_importe" => $totalImporte
        ];
    }

    // Respuesta JSON
    $response = [
        "success" => $success,
        "fecha" => $responseDebug['fecha_recibida'],
        "formato_fecha" => $responseDebug['formato_fecha'],
        "clinica" => $clinicSeleccionada,
        "totals" => $combinedTotals
    ];

    echo json_encode($response, JSON_PRETTY_PRINT);
}
?>
