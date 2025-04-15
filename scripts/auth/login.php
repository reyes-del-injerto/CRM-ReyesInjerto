<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
      throw new Exception("Faltan parámetros");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql_select_user = $conn->prepare("SELECT id, nombre, contrasena, department,clinica FROM usuarios WHERE usuario = ?");
    $sql_select_user->bind_param("s", $username);
    $sql_select_user->execute();

    $sql_select_user->store_result();

    if ($sql_select_user->num_rows === 0) {
      throw new Exception("Usuario no encontrado");
    }

    $sql_select_user->bind_result($id, $name, $hashed_password, $department ,$clinica);
    $sql_select_user->fetch();

    if (!password_verify($password, $hashed_password)) {
      throw new Exception("Contraseña incorrecta");
    }

    $sql_permissions = $conn->prepare("SELECT permission_id FROM u_permission_assignment WHERE user_id = ?");
    $sql_permissions->bind_param("i", $id);
    $sql_permissions->execute();

    $result_permissions = $sql_permissions->get_result();

    $user_permissions = [];
    while ($row = $result_permissions->fetch_assoc()) {
      $user_permissions[] = $row['permission_id'];
    }

    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_permissions'] = $user_permissions;
    $_SESSION['department'] = $department;

    if (isset($_POST['rememberme']) && $_POST['rememberme'] === 'rememberme') {
      $token = bin2hex(random_bytes(64));

      $_SESSION['recordar_token'] = $token;
      setcookie('recordar_token', $token, time() + (86400 * 30), '/');

      $sql_insert_token = $conn->prepare("INSERT INTO u_tokens (user_id, user_name, user_department, token) VALUES (?, ?, ?, ?)");
      $sql_insert_token->bind_param("isss", $id, $name, $department, $token);
      $sql_insert_token->execute();
    }

    echo json_encode([
      "success" => true,
      "user_id" => $id,
      "user_name" => $name,
      "user_department" => $department,
      "user_department" => $department,
      "clinica" => $clinica,
      "token" => isset($token) ? $token : null
    ]);
  } catch (Exception $e) {
    // Manejo de errores y retorno de respuesta JSON
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
  }
}


$conn->close();
