<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // Usado para xdebug
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

// Incluye archivos de utilidades y conexión a la base de datos
require_once __DIR__ . "/../common/utilities.php";
require_once __DIR__ . "/../common/connection_db.php";

// Recupera los números de expediente desde POST
$old_num_med_record = isset($_POST['current_exp_num']) ? intval($_POST['current_exp_num']) : 0;
$new_num_med_record = isset($_POST['new_exp_num']) ? intval($_POST['new_exp_num']) : 0;


$response = array();
$response['success'] = false;
$response['updated_tables'] = array();

if ($old_num_med_record == 0 || $new_num_med_record == 0) {
    $response['message'] = "Número de expediente antiguo o nuevo no válido. {$new_num_med_record}";
    echo json_encode($response);
    exit;
}

// Usa la conexión definida en connection_db.php
global $conn; // Asegura que estás usando la conexión global

// Actualiza el número de expediente en enf_procedures
$sql_procedures = "UPDATE enf_procedures SET num_med_record = ? WHERE num_med_record = ?";
$stmt = $conn->prepare($sql_procedures);
$stmt->bind_param('ii', $new_num_med_record, $old_num_med_record);
$stmt->execute();

// Verifica si se actualizaron registros en enf_procedures
if ($stmt->affected_rows > 0) {
    $response['updated_tables'][] = 'enf_procedures';
}

// Lista de tablas a actualizar
$tables = [
    'enf_protocols',
    'enf_protocols_appointments',
    'enf_treatments',
    'enf_treatments_appointments',
    'photos_register'
];

// Actualiza el número de expediente en las demás tablas, si el campo existe
foreach ($tables as $table) {
    // Verifica si la columna num_med_record existe en la tabla
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE 'num_med_record'");
    if ($result->num_rows > 0) {
        // Verifica si hay registros con el número de expediente antiguo
        $result = $conn->query("SELECT COUNT(*) AS count FROM $table WHERE num_med_record = $old_num_med_record");
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $sql = "UPDATE $table SET num_med_record = ? WHERE num_med_record = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $new_num_med_record, $old_num_med_record);
            $stmt->execute();

            // Verifica si se actualizaron registros en la tabla actual
            if ($stmt->affected_rows > 0) {
                $response['updated_tables'][] = $table;
            }
        }
    }
}

// Caso especial para sa_events
$sql_sa_events = "UPDATE sa_events SET title = REPLACE(title, ?, ?) WHERE title LIKE CONCAT('%', ?, '%')";
$stmt = $conn->prepare($sql_sa_events);
$old_title_part = "$old_num_med_record";
$new_title_part = "$new_num_med_record";
$stmt->bind_param('sss', $old_title_part, $new_title_part, $old_title_part);
$stmt->execute();

// Verifica si se actualizaron registros en sa_events
if ($stmt->affected_rows > 0) {
    $response['updated_tables'][] = 'sa_events';
}

$stmt->close();
$conn->close();

// Establece el éxito de la operación
$response['success'] = !empty($response['updated_tables']);
$response['message'] = $response['success'] ? "Actualización completada." : "No se realizaron actualizaciones.";

echo json_encode($response);

?>
