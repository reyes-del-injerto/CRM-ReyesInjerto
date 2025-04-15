<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

session_start();
require_once '../common/connection_db.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Inicializar un array para almacenar errores
    $errors = [];

    // Recolectar los datos del POST
    $num_med_record = isset($_POST['num_med_record']) && $_POST['num_med_record'] !== '' ? $_POST['num_med_record'] : null;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $clinic_num_med = isset($_POST['clinic_num_med']) ? trim($_POST['clinic_num_med']) : '';

    // Validaciones
    if (empty($name)) {
        $errors[] = 'El campo nombre es requerido.';
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $name)) {
        $errors[] = 'El nombre solo puede contener letras, espacios, y caracteres especiales como acentos y la ñ.';
    }

    if ($num_med_record !== null && !is_numeric($num_med_record)) {
        $errors[] = 'El número de expediente debe ser un valor numérico.';
    }

    // Si hay errores, devolverlos y detener la ejecución
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => 'Errores de validación.', 'errors' => $errors]);
        exit;
    }

    $datenow = date('Y-m-d');

    // Verificar si ya existe un registro con el mismo número de expediente y clínica
    $check_sql = "SELECT COUNT(*) AS count FROM enf_treatments WHERE num_med_record IS NOT NULL AND num_med_record = ? AND clinic = ?";
    if ($stmt = $conn->prepare($check_sql)) {
        $stmt->bind_param("is", $num_med_record, $clinic_num_med);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        // Si existe un duplicado
        $duplicated = $count > 0;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de verificación: ' . $conn->error]);
        exit;
    }

    // Insertar el nuevo registro
    $insert_sql = "INSERT INTO enf_treatments (num_med_record, name, created_at, clinic) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($insert_sql)) {
        $stmt->bind_param("isss", $num_med_record, $name, $datenow, $clinic_num_med);

        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Tratamiento añadido',
                'fecha añadida' => $datenow
            ];

            // Añadir advertencia si hay duplicados
            if ($duplicated) {
                $response['warning'] = "Se añadió al paciente pero verificar por duplicidad de número de expediente en $clinic_num_med.";
            }

            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    }

    // Cerrar la conexión
    $conn->close();
} else {
    // Método de solicitud no válido
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido. Solo se acepta POST.']);
}
