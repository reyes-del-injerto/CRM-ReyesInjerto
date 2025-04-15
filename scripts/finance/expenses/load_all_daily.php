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
$data = [];

// Verifica si se ha proporcionado una fecha y una clínica válidas
if ($fechaSeleccionada && $clinicSeleccionada) {
    // Sanitiza las entradas para evitar inyecciones SQL
    $fechaSeleccionada = $conn->real_escape_string($fechaSeleccionada);
    $clinicSeleccionada = $conn->real_escape_string($clinicSeleccionada);

    // Consulta SQL con filtro por fecha y clínica para `sa_info_payment_px`
    $sql_px = "SELECT
                p.id,
                DATE_FORMAT(p.payment_date, '%Y-%m-%d') AS fecha,
                CONCAT(l.first_name, ' ', l.last_name) AS nombre,
                l.id AS lead_id,
                p.public_notes AS concepto,
                p.type AS tipo,
                p.amount AS importe,
                COALESCE(p.conversion, 0) AS conversion,
                COALESCE(p.amount_conversion, 0) AS amount_conversion,
                p.method AS metodo_de_pago,
                p.clinic AS sucursal,
                './assets/img/pdf-file.png' AS imagen,
                p.public_notes,
                p.private_notes,
                'payment_px' AS source
            FROM sa_info_payment_px p
            LEFT JOIN sa_leads l ON p.lead_id = l.id
            WHERE DATE(p.payment_date) = '$fechaSeleccionada'
            AND p.clinic = '$clinicSeleccionada' AND status=1
            ORDER BY p.payment_date DESC";

    // Consulta SQL con filtro por fecha y clínica para `sa_info_payment_treatments`
    $sql_treatments = "SELECT
            t.id,
            t.px_id,
            DATE_FORMAT(t.payment_date, '%Y-%m-%d') AS fecha,
            e.name AS nombre,
            e.num_med_record,
            t.public_notes AS concepto,
            t.type AS tipo,
            t.amount AS importe,
            COALESCE(t.conversion, 0) AS conversion,
            COALESCE(t.amount_conversion, 0) AS amount_conversion,
            t.method AS metodo_de_pago,
            t.clinic AS sucursal,
            './assets/img/pdf-file.png' AS imagen,
            t.public_notes,
            t.private_notes,
            'payment_treatments' AS source
        FROM sa_info_payment_treatments t
        LEFT JOIN enf_treatments e ON t.px_id = e.id
        WHERE DATE(t.payment_date) = '$fechaSeleccionada'
        AND t.clinic = '$clinicSeleccionada'
        ORDER BY t.payment_date DESC";

    // Ejecutar ambas consultas
    $result_px = $conn->query($sql_px);
    $result_treatments = $conn->query($sql_treatments);

    if (!$result_px || !$result_treatments) {
        // Manejo de errores en las consultas
        echo json_encode([
            'success' => 'false',
            'error' => 'Error en la consulta SQL',
            'query1' => $sql_px,
            'query2' => $sql_treatments
        ]);
        exit;
    }

    // Procesar resultados de `sa_info_payment_px`
    if ($result_px->num_rows > 0) {
        while ($row = $result_px->fetch_assoc()) {
            // Construir rutas posibles para el archivo PDF
            $pdf_paths = [
                "../../storage/leads/{$row['lead_id']}/receipts/{$row['tipo']}_{$row['id']}.pdf",
                "../../../cdmx/temporal_storage/docs/{$row['lead_id']}/invoices/{$row['tipo']}_{$row['id']}.pdf"
            ];

            $pdf_path = "#"; // Valor por defecto si no se encuentra el archivo
            $tried_paths = [];
            foreach ($pdf_paths as $path) {
                $full_path = "../" . $path;
                $tried_paths[] = $full_path;
                if (file_exists($full_path)) {
                    $pdf_path = $full_path;
                    break;
                }
            }

            // Eliminar "../../../" de la ruta antes de añadirla al array de datos
            $display_path = str_replace("../../../", "", $pdf_path);

            // Generar el enlace para el nombre
            $link_name = "<a data-lead-id='{$row['lead_id']}' href='view_lead.php?id={$row['lead_id']}&client=yes' type='button' class='single_client'>{$row['nombre']}</a>";
            $delete_button = "<button type='button' class='btn btn-danger btn-sm delete_record' data-id='{$row['id']}' data-type='payment_px' title='Eliminar'>
                <i class='fas fa-trash'></i>
            </button>";

            // Reemplazar valores NULL por 'NA'
            $data[] = [
                "id" => $row["id"] ?? 'NA',
                "fecha" => $row["fecha"] ?? 'NA',
                "nombre" => $link_name,
                "concepto" => $row["concepto"] ?? 'NA',
                "tipo" => $row["tipo"] ?? 'NA',
                "importe" => $row["importe"] ?? 'NA',
                "conversion" => $row["conversion"] ?? 0,
                "amount_conversion" => $row["amount_conversion"] ?? 0,
                "metodo_de_pago" => $row["metodo_de_pago"] ?? 'NA',
                "imagen" => $display_path,
                "sucursal" => $row["sucursal"] ?? 'NA',
                "options" => $delete_button,
                "public_notes" => $row["public_notes"] ?? 'NA',
                "private_notes" => $row["private_notes"] ?? 'NA',
                "source" => $row["source"],
                "tried_paths" => $tried_paths
            ];

            $success = "true";
        }
    }

    // Procesar resultados de `sa_info_payment_treatments`
    if ($result_treatments->num_rows > 0) {
        while ($row = $result_treatments->fetch_assoc()) {
            // Construir rutas posibles para el archivo PDF
            $pdf_paths = [
                "../../storage/trats/{$row['id']}/receipts/Recibo_{$row['id']}.pdf"
            ];

            $pdf_path = "#"; // Valor por defecto si no se encuentra el archivo
            $tried_paths = [];
            foreach ($pdf_paths as $path) {
                $full_path = "../" . $path;
                $tried_paths[] = $full_path;
                if (file_exists($full_path)) {
                    $pdf_path = $full_path;
                    break;
                }
            }

            // Eliminar "../../../" de la ruta antes de añadirla al array de datos
            $display_path = str_replace("../../../", "", $pdf_path);

            // Generar el enlace para el nombre
            $link_name = "<a href='view_a_treatment.php?id={$row['px_id']}' type='button' class='single_client'>{$row['nombre']}</a>";
            $delete_button = "<button type='button' class='btn btn-danger btn-sm delete_record' data-id='{$row['id']}' data-type='payment_treatments' title='Eliminar'>
                <i class='fas fa-trash'></i>
            </button>";

            // Reemplazar valores NULL por 'NA'
            $data[] = [
                "id" => $row["id"] ?? 'NA',
                "fecha" => $row["fecha"] ?? 'NA',
                "nombre" => $link_name,
                "concepto" => $row["concepto"] ?? 'NA',
                "tipo" => $row["tipo"] ?? 'NA',
                "importe" => $row["importe"] ?? 'NA',
                "conversion" => $row["conversion"] ?? 0,
                "amount_conversion" => $row["amount_conversion"] ?? 0,
                "metodo_de_pago" => $row["metodo_de_pago"] ?? 'NA',
                "imagen" => $display_path,
                "sucursal" => $row["sucursal"] ?? 'NA',
                "options" => $delete_button,
                "public_notes" => $row["public_notes"] ?? 'NA',
                "private_notes" => $row["private_notes"] ?? 'NA',
                "source" => $row["source"],
                "tried_paths" => $tried_paths
            ];

            $success = "true";
        }
    }
}

// Devolver los datos en formato JSON
echo json_encode([
    'success' => $success,
    'data' => $data,
    'query1' => $sql_px,
    'query2' => $sql_treatments
]);
?>
