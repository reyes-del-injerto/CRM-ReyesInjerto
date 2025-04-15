<?php
// export_reporte_leads.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../common/connection_db.php";
// Asegúrate de tener cargado el autoload de Composer
require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// --- Definición de arrays de opciones ---
$states = [
    'Descartado',
    'Inactivo',
    'Mal prospecto',
    'Interesado',
    'Seguimiento',
    'En negociación',
];

$semaforo_colors = [
    'Ya no responde'                    => '#4f0e00',
    'No es candidato'                    => '#a900d6',  
    'No respondió desde el primer mensaje' => '#d80000',
    'Interesado'                         => '#14db73',
    'Mando fotografias'                  => '#ce9e03',
    'Agendo valoración'                  => '#35a0ea',
    'Tratamineto'                        => '#e174f5',
    'Basura'                             => '#cb6310',
    'Cierre'                             => '#009346',
    'Promoción'                          => '#00ffe8',
];

// --- Construir la consulta principal (complementada) ---
$sql = "SELECT 
            l.id, 
            l.created_at, 
            l.first_name, 
            l.last_name, 
            l.phone, 
            l.interested_in, 
            l.stage,
            l.semaforo, 
            l.quali, 
            l.seller, 
            l.last_activity, 
            a.date AS assessment_date, 
            l.clinic 
        FROM sa_leads l 
        LEFT JOIN (
            SELECT lead_id, date 
            FROM sa_leads_assessment 
            WHERE status = 1 OR status IS NULL
        ) a ON l.id = a.lead_id 
        WHERE 1";

$types = "";
$total_values = [];

/**
 * Filtro de Propietaria(o) (seller)
 */
if (isset($_GET['seller']) && is_array($_GET['seller'])) {
    $options = $_GET['seller'];
    if (!empty($options)) {
        $placeholders = implode(',', array_fill(0, count($options), '?'));
        $sql .= " AND l.seller IN ($placeholders)";
        $types .= str_repeat('s', count($options));
        $total_values = array_merge($total_values, $options);
    }
}

/**
 * Filtro de Clínica (clinic)
 */
if (isset($_GET['clinic']) && is_array($_GET['clinic'])) {
    $options = $_GET['clinic'];
    if (!empty($options)) {
        $placeholders = implode(',', array_fill(0, count($options), '?'));
        $sql .= " AND l.clinic IN ($placeholders)";
        $types .= str_repeat('s', count($options));
        $total_values = array_merge($total_values, $options);
    }
}

/**
 * Filtro de Etapa (stage)
 */
if (isset($_GET['stage']) && is_array($_GET['stage'])) {
    $options = $_GET['stage'];
    if (!empty($options)) {
        $placeholders = implode(',', array_fill(0, count($options), '?'));
        $sql .= " AND l.stage IN ($placeholders)";
        $types .= str_repeat('s', count($options));
        $total_values = array_merge($total_values, $options);
    }
}

/**
 * Filtro de Calificación (quali)
 */
if (isset($_GET['quali'])) {
    $options = (array) $_GET['quali'];  // Forzamos a que sea un array
    if (!empty($options)) {
        $placeholders = implode(',', array_fill(0, count($options), '?'));
        $sql .= " AND l.quali IN ($placeholders)";
        $types .= str_repeat('s', count($options));
        $total_values = array_merge($total_values, $options);
    }
}

/**
 * Filtro de Semáforo (semaforo)
 */
if (isset($_GET['semaforo']) && is_array($_GET['semaforo'])) {
    $options = $_GET['semaforo'];
    if (!empty($options)) {
        $placeholders = implode(',', array_fill(0, count($options), '?'));
        $sql .= " AND l.semaforo IN ($placeholders)";
        $types .= str_repeat('s', count($options));
        $total_values = array_merge($total_values, $options);
    }
}

/**
 * Filtro de fecha: Si se envía chosen_time (filtro rápido) se usa; de lo contrario, se usa date_range.
 */
if (isset($_GET['chosen_time']) && !empty($_GET['chosen_time'])) {
    $chosen_time = $_GET['chosen_time'];
    if ($chosen_time !== 'all') { // Si es "all" no se aplica filtro de fecha
        list($sql, $types, $total_values) = dateButtons($sql, $types, $chosen_time, $total_values);
    }
} elseif (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
    $dates = explode("-", $_GET['date_range']);
    $raw_start_date = trim($dates[0]);
    $object_start_date = DateTime::createFromFormat('d/m/Y', $raw_start_date);
    $start_date = $object_start_date->format('Y-m-d');

    $raw_end_date = trim($dates[1] ?? $raw_start_date);
    $object_end_date = DateTime::createFromFormat('d/m/Y', $raw_end_date);
    $end_date = $object_end_date->format('Y-m-d') . " 23:59:59";

    $sql .= " AND l.created_at BETWEEN ? AND ?";
    $types .= "ss";
    $total_values = array_merge($total_values, [$start_date, $end_date]);
}

// Agregar orden por id de mayor a menor
$sql .= " ORDER BY l.id DESC";

// --- Funciones auxiliares para el filtro rápido de fecha ---
function dateButtons($sql, $types, $chosen_time, $total_values) {
    switch ($chosen_time) {
        case 'today':
            $custom_dates = [date("Y-m-d")];
            $sql .= " AND DATE(l.created_at) = ?";
            $types .= "s";
            break;
        case 'yesterday':
            $custom_dates = [date("Y-m-d", strtotime("-1 day"))];
            $sql .= " AND DATE(l.created_at) = ?";
            $types .= "s";
            break;
        case 'thisweek':
            $custom_dates = thisWeek();
            $sql .= " AND l.created_at BETWEEN ? AND ?";
            $types .= "ss";
            break;
        case 'thismonth':
            $sql .= " AND YEAR(l.created_at) = YEAR(CURDATE()) AND MONTH(l.created_at) = MONTH(CURDATE())";
            $custom_dates = [];
            break;
        case 'all':
            $custom_dates = [];
            break;
        default:
            $custom_dates = [];
            break;
    }
    $total_values = array_merge($total_values, $custom_dates);
    return [$sql, $types, $total_values];
}

function thisWeek() {
    $today = new DateTime();
    // Obtener el domingo de la semana actual (si hoy es domingo se mantiene)
    $dayOfWeek = $today->format('w'); // Domingo = 0
    $sunday = clone $today;
    $sunday->modify("-{$dayOfWeek} days");
    $start_date = $sunday->format('Y-m-d');
    $end_date = date("Y-m-d");
    return [$start_date, $end_date];
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$total_values);
}
$stmt->execute();
$result = $stmt->get_result();

// --- Consulta de tareas para determinar la actividad próxima ---
$tasks_sql = "SELECT 
                lead_id, 
                subject, 
                comments, 
                assigned_to, 
                end_date, 
                created_by, 
                created_at, 
                status
            FROM 
                sa_lead_tasks
            WHERE
                status = 0
                AND end_date >= NOW()
            ORDER BY 
                lead_id, 
                end_date ASC";
$result_tasks = $conn->query($tasks_sql);
$tasks = [];
while ($task = $result_tasks->fetch_assoc()) {
    $lead_id = $task['lead_id'];
    // Solo guardamos la primera tarea (la de menor end_date) por lead
    if (!isset($tasks[$lead_id])) {
        $tasks[$lead_id] = $task;
    }
}

// --- Creación del archivo Excel ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Definir encabezados según la tabla
$header = [
    "ID",
    "Nombre Completo",
    "Clinica",
    "Teléfono",
    "Interés en",
    "Etapa",
    "Semáforo",
    "Propietaria(o)",
    "Actividad proxima",
    "Valoración",
    "Status"
];
// Escribir la fila de encabezados en la primera fila (A1:K1)
$sheet->fromArray($header, null, 'A1');

$rowNumber = 2; // iniciar en la fila 2 para los datos

while ($row = $result->fetch_assoc()) {
    $lead_id = $row['id'];
    $nombreCompleto = trim($row['first_name'] . " " . $row['last_name']);
    // Para evitar que Excel formatee el teléfono en notación científica
    $telefono = '="' . $row['phone'] . '"';
    
    // Determinar la actividad próxima a partir de la tarea (si existe)
    if (isset($tasks[$lead_id])) {
        $actividad = $tasks[$lead_id]['subject'] . " (" . $tasks[$lead_id]['end_date'] . ")";
        $statusTask = "Pendiente";
    } else {
        $actividad = "";
        $statusTask = "";
    }
    
    // Armar la línea a escribir
    $line = [
        $lead_id,
        $nombreCompleto,
        $row['clinic'] ?? '',
        $telefono,
        $row['interested_in'] ?? '',
        $row['stage'] ?? '',
        $row['semaforo'] ?? '',
        $row['seller'] ?? '',
        $actividad,
        $row['quali'] ?? '',
        $statusTask
    ];
    
    // Escribir la fila en el Excel
    $sheet->fromArray($line, null, 'A' . $rowNumber);
    
    // Aplicar color a la celda "Semáforo" (columna G) si existe un color definido
    $semaforoValue = $row['semaforo'] ?? '';
    if (isset($semaforo_colors[$semaforoValue])) {
        // Convertir el color (quitando '#' y anteponiendo 'FF' para opacidad completa)
        $colorARGB = 'FF' . strtoupper(ltrim($semaforo_colors[$semaforoValue], '#'));
        $sheet->getStyle("G{$rowNumber}")
              ->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()
              ->setARGB($colorARGB);
    }
    
    $rowNumber++;
}

// Autoajustar el ancho de las columnas (A hasta K)
foreach (range('A', 'K') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// --- Enviar el archivo Excel al navegador ---
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="leads.xlsx"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
