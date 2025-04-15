<?php
require_once "../connection_db.php";

$px_id = $_POST['px_id'];

$sql = "SELECT sig.*, sipa.full_amount, sipa.advance_amount, sipa.advance_method, sipa.advance_date, sipa.pending_amount, sipa.notes, sipr.procedure_date, sipr.procedure_type, sipr.first_meet_type, sipr.closure_type, sipr.evaluation_date, sipr.evaluation, sipr.seller, sipr.purpose FROM sa_info_general_px sig INNER JOIN sa_info_payment_px sipa ON sig.id = sipa.px_sales_id INNER JOIN sa_info_procedure_px sipr ON sig.id = sipr.px_general_id WHERE sig.id = $px_id;";

$query = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($query);
if ($row['status'] == 2) {
    $sql = "SELECT num_med_record FROM enf_procedures WHERE px_sales_id = $px_id;";
    $query = mysqli_query($conn, $sql);
    $row_enf_procedures = mysqli_fetch_assoc($query);
    $num_med_record = (isset($row_enf_procedures['num_med_record'])) ? "#" . $row_enf_procedures['num_med_record'] : 'Exped. desconocido';
} else {
    $num_med_record = "Exped. no asignado";
}

$row['num_med_record'] = $num_med_record;
echo json_encode($row);
