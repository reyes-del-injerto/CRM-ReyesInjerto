<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../common/utilities.php";
require_once __DIR__ . "/../common/connection_db.php";

$sql = "SELECT scp.id, scp.lead_id, scp.status, 
        CONCAT(sla.first_name, ' ', sla.last_name) AS name, 
        DATE_FORMAT(sla.procedure_date, '%d/%m/%Y') procedure_date, 
        sla.procedure_type, 
        sla.clinic,  -- Asegúrate de seleccionar el campo clinic
        COALESCE(sl.seller, 'desconocido') AS seller,
        COALESCE((SELECT ep.num_med_record FROM enf_procedures ep WHERE ep.lead_id = scp.lead_id LIMIT 1), 'NA') AS num_med_record 
        FROM sa_closed_px scp 
        LEFT JOIN sa_leads_assessment sla ON scp.lead_id = sla.lead_id 
        LEFT JOIN sa_leads sl ON scp.lead_id = sl.id";

// Construcción de la consulta base
$sql .= " WHERE 1=1 ";
$sql .= " AND sla.status != 0 ";

$main_query = true;
$types = "";
$data_clients = [];
$total_values = [];

// FILTROS: sa_leads.seller | sa_leads_assessment.procedure_type | sa_closed_px.status | sa_leads_assessment.clinic
$filters = ["seller", "status", "type", "clinic"];
foreach ($filters as $filter_name) {
    if (isset($_POST[$filter_name])) {
        $filter = $_POST[$filter_name];
        [$sql, $types, $total_values] = checkboxFilters($sql, $types, $filter, $filter_name, $total_values);
        $main_query = false;
    }
}

// Filtro Daterangepicker
if (isset($_POST['date_range']) && !isset($_POST['chosen_time'])) {
    $date_range = $_POST['date_range'];
    [$start_date, $end_date] = parseDates($date_range);
    $sql .= " AND sla.procedure_date BETWEEN ? AND ?";
    $types .= "ss";
    $total_values = array_merge($total_values, [$start_date, $end_date]);
    $main_query = false;
}

// FILTROS: Tomorrow - Today - Yesterday - This Week - This Month - All
if (isset($_POST['chosen_time']) && !isset($_POST['date_range'])) {
    $chosen_time = $_POST['chosen_time'];
    [$sql, $types, $total_values] = dateButtons($sql, $types, $chosen_time, $total_values);
    $main_query = false;
}

// Ordenar por fecha del procedimiento, priorizando el día de hoy
$sql .= " ORDER BY 
            CASE 
                WHEN DATE(sla.procedure_date) = CURDATE() THEN 0
                ELSE 1
            END, 
            sla.procedure_date DESC";

$stmt = $conn->prepare($sql);
$stmt = ($main_query) ? $conn->prepare($sql) : $stmt;

($main_query) ? "" : $stmt->bind_param($types, ...$total_values);

$stmt->execute();
$result = $stmt->get_result();

$available_status = [
    0 => "<a class='dropdown-item cancelado' data-color='danger' data-status=0 href='#' >Cancelado</a>",
    1 => "<a class='dropdown-item proximo' data-color='warning' data-status=1 href='#' >Próximo</a>",
    2 => "<a class='dropdown-item asignar' data-color='info' data-status=2 href='#' >Asignar Exped.</a>"
];

$status_options = [
    2 => ["color" => "info", "class" => "status-green", "label" => "Exp. Asignado", "dropdown" => $available_status[0] . $available_status[1]],
    1 => ["color" => "warning", "class" => "status-orange", "label" => "Próximo", "dropdown" => $available_status[0] . $available_status[2]],
    0 => ["color" => "danger", "class" => "status-pink", "label" => "Cancelado", "dropdown" => $available_status[1]]
];

while ($data = $result->fetch_object()) {
    $link_name = "<a data-lead-id='{$data->lead_id}' href='view_lead.php?id={$data->lead_id}&client=yes' type='button' class='single_client'>{$data->name}</a>";

    $status_color = $status_options[$data->status]['color'];
    $status_class = $status_options[$data->status]['class'];
    $status_label = $status_options[$data->status]['label'];
    $status_dropdown = $status_options[$data->status]['dropdown'];

    $status = "<div class='dropdown action-label'>
                <a data-status={$data->status} data-color='{$status_color}' class='custom-badge {$status_class} dropdown-toggle' href='#' data-bs-toggle='dropdown' aria-expanded='false'>
                {$status_label}
                </a>
                <div class='dropdown-menu dropdown-menu-end client-status status-staff' data-lead-id={$data->lead_id}>
                    {$status_dropdown}
                </div>
            </div>";

    $data_clients[] = [
        $link_name,
        $data->procedure_type,
        $data->procedure_date,
        $data->clinic,  // Agregar la clínica
        $data->seller,
        $status,
        $data->num_med_record // Agregar num_med_record al resultado
    ];
}

$response  = ["data" => $data_clients];

echo json_encode($response);



// FILTROS: sa_leads.seller | sa_leads_assessment.procedure_type | sa_closed_px.status

function checkboxFilters($sql, $types, $filter, $filter_name, $total_values)
{
    if ($filter_name == "seller") {
        $column_name = "sl.$filter_name";
        $param_type = "s";
    } else if ($filter_name == "type") {
        $column_name = "sla.procedure_type";
        $param_type = "s";
    } else if ($filter_name == "status") {
        $column_name = "scp.$filter_name";
        $param_type = "i";
    } else if ($filter_name == "clinic") { // Filtro para clínica
        $column_name = "sla.clinic";
        $param_type = "s";
    }

    parse_str($filter, $chosen_options);
    $options = array_values($chosen_options[$filter_name]);

    $placeholders = implode(',', array_fill(0, count($options), '?'));
    $sql .= " AND {$column_name} IN ({$placeholders})";

    $add_qty = count($options);
    $types .= str_repeat($param_type, $add_qty);
    $total_values = array_merge($total_values, $options);

    return [$sql, $types, $total_values];
}


function dateButtons($sql, $types, $chosen_time, $total_values)
{
    switch ($chosen_time) {
        case 'tomorrow':
            $custom_dates = [date('Y-m-d', strtotime('+1 day'))];
            $sql .= " AND DATE(sla.procedure_date) = ?";
            $types .= "s";
            break;
        case 'today':
            $custom_dates = [date("Y-m-d")];
            $sql .= " AND DATE(sla.procedure_date) = ?";
            $types .= "s";
            break;
        case 'yesterday':
            $custom_dates = [date('Y-m-d', strtotime('-1 day'))];
            $sql .= " AND DATE(sla.procedure_date) = ?";
            $types .= "s";
            break;
        case 'thisweek':
            $custom_dates = thisWeek();
            $sql .= " AND sla.procedure_date BETWEEN ? AND ? ";
            $types .= "ss";
            break;
        case 'thismonth':
            $custom_dates = [];
            $sql .= " AND YEAR(sla.procedure_date) = YEAR(CURDATE()) AND MONTH(sla.procedure_date) = MONTH(CURDATE())";
            $types .= "";
            break;
        case 'all':
            $custom_dates = [];
            $sql .= "";
            $types .= "";
            break;
    }

    $total_values = array_merge($total_values, $custom_dates);

    return [$sql, $types, $total_values];
}

function parseDates($dates)
{
    $dates = explode("-", $dates);

    $raw_start_date = ltrim(rtrim($dates[0]));
    $object_start_date = DateTime::createFromFormat('d/m/Y', $raw_start_date);
    $start_date = $object_start_date->format('Y-m-d');

    $raw_end_date = ltrim(rtrim($dates[1]));
    $object_end_date = DateTime::createFromFormat('d/m/Y', $raw_end_date);
    $end_date = $object_end_date->format('Y-m-d');
    $end_date = $end_date . " 23:59:59";

    return [$start_date, $end_date];
}

function thisWeek()
{
    $today = new DateTime();
    $today = $today->modify('last Sunday');
    $last_sunday = $today->format('Y-m-d');

    $today = date("Y-m-d");
    return [$last_sunday, $today];
}
