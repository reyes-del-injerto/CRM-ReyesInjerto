<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

session_start();
require_once '../common/connection_db.php';

try {
    // Recibe los datos del formulario
    $px_fullname = $_POST['px_fullname']; // Nombre completo del paciente
    $px_identifier = $_POST['px_identifier']; // Puede ser el num_med_record o el px_id
    $px_identifier_type = strtolower($_POST['px_identifier_type']); // 'exp' o 'id'
    $num_med_record = null; // Inicializamos en null
    $date = $_POST['date'];
    $clinic = $_POST['clinic'];
    $origin = $_POST['origin'];
    $doctor = $_POST['doctor'];
    $type = $_POST['type'];
    $notes = $_POST['notes'];
    $created_by = $_POST['user_id_tr'];

    // Verifica si el num_med_record ya existe en la tabla `enf_treatments`
    if ($px_identifier_type === 'exp') {
        $check_sql = $conn->prepare("SELECT id FROM enf_treatments WHERE num_med_record = ?");
        $check_sql->bind_param("i", $px_identifier);
        $check_sql->execute();
        $check_sql->store_result();

        if ($check_sql->num_rows > 0) {
            $check_sql->bind_result($treatment_id);
            $check_sql->fetch();
            $message = "El número de expediente registrado. ";
        } else {
            // Si el nombre no existe, insertamos un nuevo registro en `enf_treatments`
            $num_med_record = $px_identifier; // Asignamos el identificador como num_med_record si es de tipo 'exp'

            $insert_sql = $conn->prepare("INSERT INTO enf_treatments (name, num_med_record) VALUES (?, ?)");
            $insert_sql->bind_param("si", $px_fullname, $num_med_record); // num_med_record será null si el tipo es 'id'

            if (!$insert_sql->execute()) {
                throw new Exception("Error al insertar en enf_treatments: " . $insert_sql->error);
            }

            $treatment_id = $insert_sql->insert_id; // Obtenemos el ID del nuevo tratamiento
            $message = "Tratamiento añadido exitosamente.";
        }
    } else {
        $message = "Tipo de identificador no es 'exp', omitiendo verificación en `enf_treatments`.";
    }

    // Insertamos la cita en la tabla correspondiente
    if ($px_identifier_type === 'exp') {
        $table = 'enf_treatments_appointments';
        $column = 'num_med_record'; // Insertamos en la columna num_med_record
    } else if ($px_identifier_type === 'id') {
        $table = 'enf_treatments_appointments_ext';
        $column = 'px_id'; // Insertamos en la columna px_id si es de tipo 'id'
    } else {
        throw new Exception("Tipo de identificador no válido.");
    }

    // Prepara la consulta SQL para insertar la cita
    $sql = $conn->prepare("INSERT INTO $table ($column, date, clinic, doctor, type, notes, created_by,origin) VALUES (?, ?, ?, ?, ?, ?, ?,?)");
    $sql->bind_param("isssssis", $px_identifier, $date, $clinic, $doctor, $type, $notes, $created_by,$origin);

    if (!$sql->execute()) {
        throw new Exception("Error al añadir la cita: " . $sql->error);
    }

    $message .= " Cita añadida exitosamente.";
    $success = true;

} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

// Cierra la conexión
$conn->close();

// Devuelve la respuesta como JSON
echo json_encode(["success" => $success, "message" => $message]);
?>
