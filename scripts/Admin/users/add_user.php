<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');

session_start();
require_once "../../common/connection_db.php"; // DB Connection

$success = true;
$missingFields = [];

// Verificar si los datos están en POST
if (!isset($_POST['nombre']) || empty(trim($_POST['nombre']))) {
    $missingFields[] = "nombre";
}
if (!isset($_POST['usuario']) || empty(trim($_POST['usuario']))) {
    $missingFields[] = "usuario";
}
if (!isset($_POST['contrasena']) || empty(trim($_POST['contrasena']))) {
    $missingFields[] = "contrasena";
}
if (!isset($_POST['verif_contrasena']) || empty(trim($_POST['verif_contrasena']))) {
    $missingFields[] = "verif_contrasena";
}
if (!isset($_POST['clinic']) || empty(trim($_POST['clinic']))) {
    $missingFields[] = "clinica";
}
if (!isset($_POST['department']) || empty(trim($_POST['department']))) {
    $missingFields[] = "department";
}

// Si hay campos faltantes, devolver mensaje de error
if (count($missingFields) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan los siguientes campos: " . implode(", ", $missingFields)
    ]);
    exit;
}

// Verificar que las contraseñas coincidan
if ($_POST['contrasena'] !== $_POST['verif_contrasena']) {
    echo json_encode(["success" => false, "message" => "Las contraseñas no coinciden."]);
    exit;
}

// Capturar los datos del formulario
$nombre = trim($_POST['nombre']);
$usuario = trim($_POST['usuario']);
$hashed_contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
$user_clinic = trim($_POST['clinic']);
$department = trim($_POST['department']);

// Debug: Verificar los valores que se están insertando
error_log("Nombre: $nombre");
error_log("Usuario: $usuario");
error_log("Contraseña: $hashed_contrasena");
error_log("Clinica: $user_clinic");
error_log("Department: $department");

// Verificar si el usuario ya existe
$sql_check = "SELECT id FROM usuarios WHERE usuario = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $usuario);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "El nombre de usuario ya existe. Elige otro."]);
    exit;
}

// Modificar la consulta SQL para incluir el departamento
$sql_row = "INSERT INTO usuarios (nombre, usuario, contrasena, clinica, department, ultimo_acceso) 
            VALUES (?, ?, ?, ?, ?, NOW());";
$sql = $conn->prepare($sql_row);

// Asignar los valores a la consulta
$sql->bind_param("sssss", $nombre, $usuario, $hashed_contrasena, $user_clinic, $department);

if ($sql->execute()) {
    if ($sql->affected_rows > 0) {
        $user_inserted_id = $conn->insert_id;

        // Insertar el nuevo empleado en la tabla ad_employees
        $allowed_days = 0; // valor por defecto
        $used_days = 0; // valor por defecto
        $status = 0; // valor por defecto

        $sql_employee = "INSERT INTO ad_employees (name, department, allowed_days, used_days, status, clinic) 
                         VALUES (?, ?, ?, ?, ?, ?);";
        $stmt_employee = $conn->prepare($sql_employee);
        $stmt_employee->bind_param("ssiiis", $nombre, $department, $allowed_days, $used_days, $status, $user_clinic);

        if (!$stmt_employee->execute()) {
            echo json_encode(["success" => false, "message" => "Error al añadir el empleado. Contacta al administrador."]);
            exit;
        }

        // Asignar los permisos al nuevo usuario si existen
        $permissionsString = isset($_POST['permissions']) ? $_POST['permissions'] : '';
        $permissionsArray = explode(',', $permissionsString);

        foreach ($permissionsArray as $permission) {
            if (!empty($permission)) {
                $sql_row = "INSERT INTO u_permission_assignment (user_id, permission_id) VALUES (?, ?);";
                $sql = $conn->prepare($sql_row);
                $sql->bind_param("ii", $user_inserted_id, $permission);
                if (!$sql->execute()) {
                    $success = false;
                    echo json_encode(["success" => false, "message" => "Error al asignar permisos. Contacta al administrador"]);
                    exit;
                }
            }
        }
        echo json_encode(["success" => true, "message" => "Usuario añadido correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al añadir el usuario."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error en la consulta. Contacta al administrador"]);
}

$conn->close();
?>
