<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); //
date_default_timezone_set('America/Mexico_City');

session_start();
require_once "../connection_db.php"; // DB Connection

/* Constant*/
$status = 1;
$generalId = $_POST['id'];

/*! Patient General Info */
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$gender = $_POST['gender'];
$clinic = $_POST['clinic'];
$phone_1 = $_POST['phone_1'];
$phone_2 = $_POST['phone_2'];
$email = $_POST['email'];
$address = $_POST['address'];
$allergies = $_POST['allergies'];

/* Procedure Info */
$procedure_date = $_POST['procedure_date'];
$procedure_type = $_POST['procedure_type'];
$first_meet_type = $_POST['first_meet_type'];
$closure_type = $_POST['closure_type'];
$evaluation_date = $_POST['evaluation_date'];
$evaluation = $_POST['evaluation'];
$seller = $_POST['seller'];
$purpose = $_POST['purpose'];

/* Payment Info*/
$full_amount = $_POST['full_amount'];
$advance_amount = $_POST['advance_amount'];
$advance_method = $_POST['advance_method'];
$advance_date = $_POST['advance_date'];
$pending_amount = $_POST['pending_amount'];
$notes = $_POST['notes'];


try {

    updateGeneralInfo($conn, $generalId, $first_name, $last_name, $gender, $clinic, $phone_1, $phone_2, $email, $address, $allergies);

    updateProcedureInfo($conn, $generalId, $procedure_date, $procedure_type, $first_meet_type, $closure_type, $evaluation_date, $evaluation, $seller, $purpose);

    updatePaymentInfo($conn, $generalId, $full_amount, $advance_amount, $advance_method, $advance_date, $pending_amount, $notes);

    echo json_encode(["success" => true, "message" => "Información actualizada correctamente"]);
} catch (Exception $e) {
    echo json_encode(["success" => true, "message" => $e->getMessage()]);
}


function updateGeneralInfo($conn, $generalId, $first_name, $last_name, $gender, $clinic, $phone_1, $phone_2, $email, $address, $allergies)
{
    $sql = $conn->prepare("UPDATE sa_info_general_px SET first_name = ?, last_name = ?, gender = ?, clinic = ?, phone_1 = ?, phone_2 = ?, email = ?, address = ?, allergies = ? WHERE id = ?");

    $sql->bind_param("sssssssssi", $first_name, $last_name, $gender, $clinic, $phone_1, $phone_2, $email, $address, $allergies, $generalId);

    return $sql->execute() && $sql->affected_rows;
}

function updateProcedureInfo($conn, $generalId, $procedure_date, $procedure_type, $first_meet_type, $closure_type, $evaluation_date, $evaluation, $seller, $purpose)
{
    $sql = $conn->prepare("UPDATE sa_info_procedure_px SET procedure_date = ?, procedure_type = ?, first_meet_type = ?, closure_type = ?, evaluation_date = ?, evaluation = ?, seller = ?, purpose = ? WHERE px_general_id = ?;");

    $sql->bind_param("ssssssssi", $procedure_date, $procedure_type, $first_meet_type, $closure_type, $evaluation_date, $evaluation, $seller, $purpose, $generalId);

    return $sql->execute() && $sql->affected_rows > 0;
}


function updatePaymentInfo($conn, $generalId, $full_amount, $advance_amount, $advance_method, $advance_date, $pending_amount, $notes)
{
    $sql = $conn->prepare("UPDATE sa_info_payment_px SET full_amount = ?, advance_amount = ?, advance_method = ?, advance_date = ?, pending_amount = ?, notes = ? WHERE px_sales_id = ?;");

    $sql->bind_param("ddssdsi", $full_amount, $advance_amount, $advance_method, $advance_date, $pending_amount, $notes, $generalId);

    return $sql->execute() && $sql->affected_rows > 0;
}
