<?php
// load_leads.php
ini_set('error_reporting', -1);
ini_set('display_errors', 0); // Desactivamos la muestra de errores en la salida JSON
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

// Arreglo final de datos para DataTables
$leads = [];
$types = "";
$total_values = [];

// ------------------------------------------------------------------------------------
// 1. Construir la parte común de la consulta (FROM y JOIN) y la parte de filtros WHERE
// ------------------------------------------------------------------------------------
$sql_filters = " FROM sa_leads l
    LEFT JOIN (
        SELECT lead_id, date
        FROM sa_leads_assessment
        WHERE status = 1 OR status IS NULL
    ) a ON l.id = a.lead_id
    WHERE 1 ";

// 1.1 Filtros de checkboxes: stage, seller, clinic, semáforo y quali
$filters = ["stage", "seller", "clinic", "semaforo", "quali"];
foreach ($filters as $filter_name) {
    if (isset($_POST[$filter_name])) {
        $filter = $_POST[$filter_name];
        list($sql_filters, $types, $total_values) = checkboxFilters($sql_filters, $types, $filter, $filter_name, $total_values);
    }
}

// 1.2 Filtro de fechas: se evalúa el rango manual o el botón rápido
if (!empty($_POST['date_range'])) {
    $date_range = $_POST['date_range'];
    list($start_date, $end_date) = parseDates($date_range);
    $sql_filters .= " AND l.created_at BETWEEN ? AND ?";
    $types .= "ss";
    $total_values = array_merge($total_values, [$start_date, $end_date]);
} elseif (!empty($_POST['chosen_time'])) {
    $chosen_time = $_POST['chosen_time'];
    if ($chosen_time !== 'all') {
        list($sql_filters, $types, $total_values) = dateButtons($sql_filters, $types, $chosen_time, $total_values);
    }
}

// 1.3 Búsqueda global (campo "Buscar" en DataTables)
$search_value = $_POST['search']['value'] ?? '';
if ($search_value !== '') {
    // Definimos en qué columnas aplicar la búsqueda
    // (id, first_name, last_name, phone, interested_in, stage, semaforo, quali, seller, clinic)
    $sql_filters .= " AND (
        l.id LIKE ?
        OR l.first_name LIKE ?
        OR l.last_name LIKE ?
        OR l.phone LIKE ?
        OR l.interested_in LIKE ?
        OR l.stage LIKE ?
        OR l.semaforo LIKE ?
        OR l.quali LIKE ?
        OR l.seller LIKE ?
        OR l.clinic LIKE ?
    )";
    $types .= str_repeat('s', 10); // Aumentamos a 10 parámetros porque ahora incluimos el ID
    $param = "%{$search_value}%";
    $total_values = array_merge($total_values, [
        $param, $param, $param, $param, $param, $param, $param, $param, $param, $param
    ]);
}

// ------------------------------------------------------------------------------------
// 2. Calcular el total de registros filtrados (recordsFiltered)
// ------------------------------------------------------------------------------------
$sql_count = "SELECT COUNT(*) as total " . $sql_filters;
$stmt_count = $conn->prepare($sql_count);
if (!empty($types)) {
    $stmt_count->bind_param($types, ...$total_values);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row = $result_count->fetch_object();
$recordsFiltered = $row ? $row->total : 0;

// ------------------------------------------------------------------------------------
// 3. Calcular el total de registros sin filtros (recordsTotal)
// ------------------------------------------------------------------------------------
$sql_total = "SELECT COUNT(*) as total FROM sa_leads";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_object();
$recordsTotal = $row_total ? $row_total->total : 0;

// ------------------------------------------------------------------------------------
// 4. Construir la consulta principal de datos (SELECT + $sql_filters)
// ------------------------------------------------------------------------------------
$sql_data = "SELECT
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
    a.date,
    l.clinic
" . $sql_filters;

// 4.1 Orden dinámico según la columna y dirección que DataTables envía
$order_column_index = $_POST['order'][0]['column'] ?? 0;
$order_dir = $_POST['order'][0]['dir'] ?? 'desc';

// Mapeamos índices de columna de DataTables a campos de la BD
$columns_map = [
    0  => 'l.id',
    1  => 'l.first_name',      // Nombre
    2  => 'l.clinic',          // Clínica
    3  => 'l.phone',           // Teléfono
    4  => 'l.interested_in',   // Interés
    5  => 'l.stage',           // Etapa
    6  => 'l.semaforo',        // Semáforo
    7  => 'l.seller',          // Propietaria(o)
    8  => 'l.last_activity',   // Actividad próxima (aquí no es exacto, pero se mapea algo)
    9  => 'l.created_at',      // Valoración (fecha de creación)
    10 => 'a.date',            // Status (a.date)
    11 => 'l.quali'            // Opt. (quali)
];

// Validamos si la columna está en el mapeo, si no, por defecto ordenamos por ID
if (isset($columns_map[$order_column_index])) {
    $sql_data .= " ORDER BY " . $columns_map[$order_column_index] . " " . $order_dir;
} else {
    $sql_data .= " ORDER BY l.id DESC";
}

// 4.2 Paginación: DataTables envía 'start' y 'length'
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 15;

$sql_data .= " LIMIT ?, ?";
$types .= "ii";
$total_values[] = $start;
$total_values[] = $length;

// ------------------------------------------------------------------------------------
// 5. Preparar y ejecutar la consulta de datos
// ------------------------------------------------------------------------------------
$stmt = $conn->prepare($sql_data);
if (!empty($types)) {
    $stmt->bind_param($types, ...$total_values);
}
$stmt->execute();
$result = $stmt->get_result();

// ------------------------------------------------------------------------------------
// 6. Consulta para obtener la tarea más próxima para cada lead
// ------------------------------------------------------------------------------------
$tasks_sql = "
    SELECT
        lead_id,
        subject,
        comments,
        assigned_to,
        end_date,
        created_by,
        created_at,
        status
    FROM sa_lead_tasks
    WHERE status = 0
      AND end_date >= NOW()
    ORDER BY lead_id, end_date ASC
";
$tasks_stmt = $conn->prepare($tasks_sql);
$tasks_stmt->execute();
$tasks_result = $tasks_stmt->get_result();

$tasks_map = [];
while ($task = $tasks_result->fetch_object()) {
    // Almacenar la tarea más próxima (end_date más cercano)
    if (!isset($tasks_map[$task->lead_id]) || $task->end_date < $tasks_map[$task->lead_id]->end_date) {
        $tasks_map[$task->lead_id] = $task;
    }
}

// ------------------------------------------------------------------------------------
// 7. Construir el array final (data) para DataTables
// ------------------------------------------------------------------------------------
if ($result) {
    while ($data = $result->fetch_object()) {
        $task = isset($tasks_map[$data->id]) ? $tasks_map[$data->id] : null;
        $leads[] = loadLead($data, $task);
    }
}

// ------------------------------------------------------------------------------------
// 8. Respuesta JSON con el formato que DataTables requiere
// ------------------------------------------------------------------------------------
$response = [
    "draw"            => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    "recordsTotal"    => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data"            => $leads
];

echo json_encode($response);

// ----------------------------------------------------------------------
// Funciones Auxiliares
// ----------------------------------------------------------------------
function checkboxFilters($sql, $types, $filter, $filter_name, $total_values)
{
    parse_str($filter, $chosen_options);
    $options = array_values($chosen_options[$filter_name]);
    $placeholders = implode(',', array_fill(0, count($options), '?'));
    $sql .= " AND {$filter_name} IN ({$placeholders})";
    $types .= str_repeat('s', count($options));
    $total_values = array_merge($total_values, $options);
    return [$sql, $types, $total_values];
}

function dateButtons($sql, $types, $chosen_time, $total_values)
{
    switch ($chosen_time) {
        case 'today':
            $custom_dates = [date("Y-m-d")];
            $sql .= " AND DATE(l.created_at) = ?";
            $types .= "s";
            break;
        case 'yesterday':
            $custom_dates = [date('Y-m-d', strtotime('-1 day'))];
            $sql .= " AND DATE(l.created_at) = ?";
            $types .= "s";
            break;
        case 'thisweek':
            $custom_dates = thisWeek();
            $sql .= " AND l.created_at BETWEEN ? AND ?";
            $types .= "ss";
            break;
        case 'thismonth':
            $sql .= " AND YEAR(l.created_at) = YEAR(CURDATE())
                      AND MONTH(l.created_at) = MONTH(CURDATE())";
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

function parseDates($dates)
{
    $dates = explode("-", $dates);
    $raw_start_date = trim($dates[0]);
    $object_start_date = DateTime::createFromFormat('d/m/Y', $raw_start_date);
    $start_date = $object_start_date->format('Y-m-d');

    $raw_end_date = trim($dates[1]);
    $object_end_date = DateTime::createFromFormat('d/m/Y', $raw_end_date);
    $end_date = $object_end_date->format('Y-m-d') . " 23:59:59";

    return [$start_date, $end_date];
}

function thisWeek()
{
    $today = new DateTime();
    // Se mueve al domingo anterior
    $today = $today->modify('last Sunday');
    $last_sunday = $today->format('Y-m-d');
    $today = date("Y-m-d");
    return [$last_sunday, $today];
}

/**
 * Construye el arreglo (row) que se enviará a DataTables para cada lead.
 */
function loadLead($data, $task)
{
    $lead_name = $data->first_name . " " . $data->last_name;
    $link_name = "<a data-id='{$data->id}' href='view_lead.php?id={$data->id}' type='button'>{$lead_name}</a>";

    $last_activity_timestamp = strtotime($data->last_activity);
    $last_activity = date("d/m/Y H:i", $last_activity_timestamp);

    $created_at_timestamp = strtotime($data->created_at);
    $created_at = date("d/m/Y", $created_at_timestamp);

    $val_date = $data->date ? date("d/m/Y", strtotime($data->date)) : 'Sin valoración';

    // Opciones para la columna "Estado" (quali)
    $states = [
        'Descartado',
        'Inactivo',
        'Mal prospecto',
        'Interesado',
        'Seguimiento',
        'En negociación',
    ];

    // Opciones de semáforo y sus colores
    $semaforo = [
        'Ya no responde',
        'No es candidato',
        'No respondió desde el primer mensaje',
        'Interesado',
        'Mando fotografias',
        'Agendo valoración',
        'Tratamineto',
        'Basura',
        'Cierre',
        'Promoción',
    ];

    $semaforo_colors = [
        'Ya no responde' => '#4f0e00',
        'No es candidato' => '#a900d6',
        'No respondió desde el primer mensaje' => '#d80000',
        'Interesado' => '#14db73',
        'Mando fotografias' => '#ce9e03',
        'Agendo valoración' => '#35a0ea',
        'Tratamineto' => '#e174f5',
        'Basura' => '#cb6310',
        'Cierre' => '#009346',
        'Promoción' => '#00ffe8',
    ];

    // Dropdown para "Estado"
    $options_list = '';
    foreach ($states as $state) {
        $options_list .= "
            <li>
                <a class='dropdown-item update-stage' href='#' data-id='{$data->id}' data-value='{$state}'>
                    {$state}
                </a>
            </li>";
    }

    $options = "
    <div class='dropdown'>
        <button class='btn btn-light dropdown-toggle' type='button' 
                id='dropdownMenuButton1_{$data->id}' data-bs-toggle='dropdown' aria-expanded='false'>
            {$data->quali}
        </button>
        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton1_{$data->id}'>
            {$options_list}
        </ul>
    </div>";

    // Dropdown para semáforo con colores
    $semaforo_options_list = '';
    foreach ($semaforo as $option) {
        if (isset($semaforo_colors[$option])) {
            $text_color = ($option == 'Interesado' || $option == 'Promoción') ? 'black' : 'white';
            $color_li = "style='background-color: {$semaforo_colors[$option]}; color: {$text_color};'";
        } else {
            $color_li = "";
        }
        $semaforo_options_list .= "
        <li>
            <a class='dropdown-item update-semaforo' 
               href='#' 
               data-id='{$data->id}' 
               data-value='{$option}' 
               {$color_li}>
                {$option}
            </a>
        </li>";
    }

    $displaySemaforo = (!is_null($data->semaforo) && $data->semaforo !== "") ? $data->semaforo : "Semáforo";

    $btn_color = '';
    if (isset($semaforo_colors[$data->semaforo])) {
        if ($data->semaforo !== 'Interesado' && $data->semaforo !== 'Promoción') {
            $btn_color = "style='background-color: {$semaforo_colors[$data->semaforo]} !important; color: #fff !important;'";
        } else {
            $btn_color = "style='background-color: {$semaforo_colors[$data->semaforo]} !important;'";
        }
    }

    $semaforo_options = "
    <div class='dropdown'>
        <button class='btn dropdown-toggle' type='button' 
                id='dropdownMenuButtonSemaforo_{$data->id}' 
                data-bs-toggle='dropdown' 
                aria-expanded='false' 
                {$btn_color}>
            {$displaySemaforo}
        </button>
        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButtonSemaforo_{$data->id}'>
            {$semaforo_options_list}
        </ul>
    </div>";

    $next_task = $task
        ? "<strong class='subject_t'>{$task->subject}</strong> - {$task->comments}"
        : 'Sin tareas próximas';

    return [
        $data->id,         // Col 0
        $link_name,        // Col 1: Nombre completo con link
        $data->clinic,     // Col 2
        $data->phone,      // Col 3
        $data->interested_in, // Col 4
        $data->stage,      // Col 5
        $semaforo_options, // Col 6
        $data->seller,     // Col 7
        $next_task,        // Col 8
        $created_at,       // Col 9
        $val_date,         // Col 10
        $options,          // Col 11
    ];
}
?>