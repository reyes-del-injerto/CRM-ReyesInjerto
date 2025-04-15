<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

session_start();
require_once "../common/connection_db.php";

createFile($conn);

function addNewPayment($num_progresivo, $cuenta, $importe, $nombre, $clinic, $conn)
{
    $sql_row = "INSERT INTO ad_nomina (num_progresivo, cuenta, importe, nombre, clinic) VALUES (?, ?, ?, ?, ?);";
    $sql = $conn->prepare($sql_row);

    $sql->bind_param("issss", $num_progresivo, $cuenta, $importe, $nombre, $clinic);
    if (!$sql->execute()) {
        return ["status" => "error", "message" => "Error al insertar en la base de datos: " . $sql->error];
    }
    return ["status" => "success", "message" => "Registro insertado correctamente."];
}

function deletePaymentsByClinic($clinic, $conn)
{
    $sql_row = "DELETE FROM ad_nomina WHERE clinic = ?;";
    $sql = $conn->prepare($sql_row);

    $sql->bind_param("s", $clinic);
    if (!$sql->execute()) {
        return ["status" => "error", "message" => "Error al eliminar registros de la clínica: " . $sql->error];
    }
    return ["status" => "success", "message" => "Registros eliminados correctamente."];
}

function createFile($conn)
{
    $response = ["status" => "success", "message" => "", "details" => []];

    if (isset($_POST['content'])) {
        $lineaFinal = '';
        $content = $_POST['content'];
        $decodedContent = urldecode($content);
        $rows = explode("\n", $decodedContent);

        $clinicToDelete = '';
        $deletionDone = false;

        foreach ($rows as $index => $row) {
            if (trim($row) === '') continue;

            $cells = explode("/", $row);
            if (count($cells) < 5) {
                $response["details"][] = ["status" => "error", "message" => "Línea inválida", "line" => $row];
                continue;
            }

            $id = str_pad(trim($cells[0]), 9, '0', STR_PAD_LEFT);
            $cuenta = trim($cells[1]);
            $importe_raw = trim($cells[2]);
            $importe = str_replace(".", "", $importe_raw);
            $paddedImporte = str_pad($importe, 15, '0', STR_PAD_LEFT);
            $nombre = str_pad(trim($cells[3]), 40, ' ', STR_PAD_RIGHT); // Ajusta a 40 caracteres con espacios a la derecha
            $clinic = trim($cells[4]);
            $fixedCode = "001001"; // Código fijo

            if ($index === 0) {
                $clinicToDelete = $clinic;
                $deleteResult = deletePaymentsByClinic($clinicToDelete, $conn);
                $response["details"][] = $deleteResult;
                if ($deleteResult["status"] === "error") {
                    $response["status"] = "error";
                    $response["message"] = "Error al eliminar registros previos.";
                    echo json_encode($response);
                    return;
                }
                $deletionDone = true;
            }

            $lineaFinal .= $id . "                " . $cuenta . "          " . $paddedImporte . $nombre . $fixedCode . "\r\n";
            $insertResult = addNewPayment($id, $cuenta, $importe_raw, $nombre, $clinic, $conn);
            $response["details"][] = $insertResult;
            if ($insertResult["status"] === "error") {
                $response["status"] = "error";
                $response["message"] = "Error al insertar datos.";
            }
        }

        if ($deletionDone) {
            $file = '../../files/cdmx/layout_bbva.txt';
            if (file_put_contents($file, $lineaFinal) !== false) {
                $response["message"] = "Plantilla generada correctamente.";
            } else {
                $response["status"] = "error";
                $response["message"] = "Error al guardar el archivo.";
            }
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "No se proporcionó contenido para exportar.";
    }

    echo json_encode($response);
}

?>
