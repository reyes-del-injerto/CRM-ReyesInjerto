<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');

session_start();
require_once '../common/connection_db.php';

$treatments = [];
$sql = "
SELECT 
    t.id AS treatment_id,
    t.name,
    t.num_med_record,
    t.sex,
    a.date,
    a.clinic,
    a.doctor,
    a.type,
    a.notes,
    a.created_by
FROM 
    enf_protocols t
LEFT JOIN 
    enf_protocols_appointments  a 
ON 
    t.num_med_record = a.num_med_record
";

$response = "fail";
$data_array = [];

$query = $conn->query($sql);

if ($query->num_rows > 0) {
    $response = "success";
    
    while ($data = $query->fetch_object()) {
        // Verificar si ya existe un tratamiento con el mismo ID
        $existing_treatment = array_filter($data_array, function($treatment) use ($data) {
            return $treatment['treatment_id'] == $data->treatment_id;
        });

        // Si no existe, agregarlo al array
        if (empty($existing_treatment)) {
            $link_name = "<a href='view_a_protocol.php?id={$data->treatment_id}' id='id_tr' type='button' class='single_procedure'>{$data->name}</a>";

            $data_array[] = [
                'treatment_id' => $data->treatment_id,
                'name' => $data->name,
                'num_med_record' => $data->num_med_record,
                'sex' => $data->sex,
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
}

function transactionOptions($transaction_id) {
    return "<button ' type='button' class='btn btn-rounded btn-outline-success edit' data-transaction-id={$transaction_id}><i class='fa fa-pencil'></i></button>";
}

echo json_encode([
    "success" => true, 
    "data" => $data_array,
]);
?>
