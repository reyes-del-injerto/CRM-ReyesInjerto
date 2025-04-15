<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require '../common/connection_db.php';

$formData = isset($_POST['form_data']) ? $_POST['form_data'] : '';
$permissionsString = isset($_POST['permissions']) ? $_POST['permissions'] : '';

parse_str($formData, $formArray);
$permissionsArray = explode(',', $permissionsString);

if (empty($formArray['contrasena']) && empty($formArray['verif_contrasena'])) {
    $change_password = false;
} else if ($formArray['contrasena'] !== $formArray['verif_contrasena']) {
    echo json_encode(["success" => false, "error" => "Las contraseñas no coinciden."]);
    exit;
} else {
    $change_password = true;
}
$user_id = $formArray['user_id'];
$nombre = trim($formArray['nombre']);
$usuario = trim($formArray['usuario']);
$hashed_contrasena = ($change_password) ? password_hash($formArray['contrasena'], PASSWORD_BCRYPT) : false;

if ($change_password) {

    $sql_row = "UPDATE usuarios SET nombre = ?, usuario = ?, contrasena = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("sssi", $nombre, $usuario, $hashed_contrasena, $user_id);
} else {

    $sql_row = "UPDATE usuarios SET nombre = ?, usuario = ? WHERE id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("ssi", $nombre, $usuario, $user_id);
}

if ($sql->execute()) {

    $sql_row = "DELETE FROM u_permission_assignment WHERE user_id = ?;";
    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $user_id);
    if ($sql->execute()) {
        $permissions_changed = true;

        if (count($permissionsArray) > 0) {
            foreach ($permissionsArray as $permission) {
                $sql_row = "INSERT INTO u_permission_assignment (user_id, permission_id) VALUES (?, ?);";
                $sql = $conn->prepare($sql_row);

                $sql->bind_param("ii", $user_id, $permission);
                if (!$sql->execute()) {
                    $permissions_changed = false;
                }
            }
        }

        if ($permissions_changed) {
            echo json_encode(["success" => true, "message" => "Datos actualizados correctamente"]);
        } else {
            echo json_encode(["success" => false, "message" => "Query Fail. Contact Admin"]);
        }
    }

    // Cierra la conexión
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Query Fail. Contact Admin"]);
}
