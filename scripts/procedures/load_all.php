<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

require_once __DIR__ . "/../common/utilities.php";
require_once __DIR__ . "/../common/connection_db.php";

$main_query = true;
$types = "";
$data_procedures = [];
$total_values = [];

// Define the base SQL query
$sql = "SELECT sla.lead_id, CONCAT(sla.first_name, ' ', sla.last_name) AS name, DATE_FORMAT(sla.procedure_date, '%d/%m/%y') AS procedure_date, 
        sla.procedure_type, ep.num_med_record, ep.touchup, ep.room, ep.specialist, ep.notes, ep.clinic 
        FROM enf_procedures ep 
        INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id 
        WHERE sla.status <> 0";

// FILTERS: procedure_type | specialist | clinic
$filters = ["type", "specialist", "clinic"];
foreach ($filters as $filter_name) {
    if (isset($_POST[$filter_name])) {
        $filter = $_POST[$filter_name];
        [$sql, $types, $total_values] = checkboxFilters($sql, $types, $filter, $filter_name, $total_values);
        $main_query = false;
    }
}

// Daterangepicker Filter
if (isset($_POST['date_range']) && !isset($_POST['chosen_time'])) {
    $date_range = $_POST['date_range'];
    [$start_date, $end_date] = parseDates($date_range);
    $sql .= " AND sla.procedure_date BETWEEN ? AND ?";
    $types .= "ss";
    $total_values = array_merge($total_values, [$start_date, $end_date]);
    $main_query = false;
}

// FILTERS: Tomorrow - Today - Yesterday - This Week - This Month - All
if (isset($_POST['chosen_time']) && !isset($_POST['date_range'])) {
    $chosen_time = $_POST['chosen_time'];
    [$sql, $types, $total_values] = dateButtons($sql, $types, $chosen_time, $total_values);
    $main_query = false;
}

// Ensure the ORDER BY clause is not overwritten
if (strpos($sql, 'ORDER BY') === false) {
    $sql .= " ORDER BY DATEDIFF(CURDATE(), sla.procedure_date) ASC";
}

$stmt = $conn->prepare($sql);
$stmt = ($main_query) ? $conn->prepare($sql) : $stmt;

($main_query) ? "" : $stmt->bind_param($types, ...$total_values);

$stmt->execute();
$result = $stmt->get_result();

while ($data = $result->fetch_object()) {
    $link_name = "<a data-id={$data->lead_id} href='#' data-clinic='{$data->clinic}' data-exp={$data->num_med_record} type='button' class='single_procedure'>{$data->name}</a>";

    $notes = (($data->notes != null) && strlen($data->notes) > 25) ? substr($data->notes, 0, 10) . "" : $data->notes;

    $data_procedures[] = array(
        $data->procedure_date,
        $data->num_med_record,
        $link_name,
        $data->procedure_type,
        $data->clinic, // Muestra la clínica
        $data->room,
        $data->specialist,
        $data->notes
    );
}

echo json_encode(["sql" => $sql, "data" => $data_procedures]);

// Función para aplicar los filtros de checkbox (ajustada para incluir 'clinic')
function checkboxFilters($sql, $types, $filter, $filter_name, $total_values)
{
    if ($filter_name == "type") {
        $column_name = "sla.procedure_type";
        $param_type = "s";
    } else if ($filter_name == "specialist") {
        $column_name = "ep.$filter_name";
        $param_type = "s";
    } else if ($filter_name == "clinic") {
        $column_name = "ep.clinic";
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
