<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../common/connection_db.php';

$treatments = [];

// Consulta modificada para buscar en enf_treatments_appoiments o enf_treatments_appoimments_ext
$sql = "
SELECT 
    t.name, 
    t.id AS treatment_id, 
    t.num_med_record, 
    IFNULL(MAX(a.date), MAX(ext.date)) AS date, 
    t.clinic,
    IFNULL(MAX(a.doctor), MAX(ext.doctor)) AS doctor, 
    IFNULL(MAX(a.type), MAX(ext.type)) AS type, 
    IFNULL(MAX(a.notes), MAX(ext.notes)) AS notes, 
    t.created_at, 
    IFNULL(MAX(a.created_by), MAX(ext.created_by)) AS created_by
FROM enf_treatments t 
LEFT JOIN enf_treatments_appointments a 
    ON t.num_med_record = a.num_med_record 
LEFT JOIN enf_treatments_appointments_ext ext 
    ON t.id = ext.px_id
GROUP BY t.name, t.id, t.num_med_record, t.created_at 
ORDER BY t.id DESC;
";
//385 pa miri
  
$response = "fail";
$data_array = [];

$query = $conn->query($sql);

if ($query->num_rows > 0) {
    $response = "success";

    while ($data = $query->fetch_object()) {
        if ($data->num_med_record) {
            $link_name = "<a href='view_a_treatment.php?num_med={$data->num_med_record}&clinic={$data->clinic}' id='id_tr' type='button' class='single_procedure'>{$data->name} </a>";
        } else {
            $link_name = "<a href='view_a_treatment.php?id={$data->treatment_id}&clinic={$data->clinic}' id='id_tr' type='button' class='single_procedure'>{$data->name} </a>";
        }

        // Si num_med_record es NULL, mostrar "Sin exp."
        $num_med_record_display = $data->num_med_record ? $data->num_med_record : "Sin exp.";

        $data_array[] = [
            'treatment_id' => $data->treatment_id,
            'name' => $data->name,
            'num_med_record' => $num_med_record_display, // Usar la variable con la leyenda
            'date' => $data->date,
            'clinic' => $data->clinic,
            'doctor' => $data->doctor,
            'type' => $data->type,
            'notes' => $data->notes,
            'created_by' => $data->created_by,
            'link_name' => $link_name,
            transactionOptions($data->treatment_id)
        ]; 

       
    }
}

echo json_encode([
    "success" => true,
    "data" => $data_array, 
]);

function transactionOptions($transaction_id)
{
    return "<button type='button' class='btn btn-rounded btn-outline-success edit' data-transaction-id={$transaction_id}><i class='fa fa-pencil'></i></button>";
}
