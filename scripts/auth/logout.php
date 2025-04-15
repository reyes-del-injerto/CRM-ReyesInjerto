<?php
session_start(); // Inicia la sesión

// Destruye todas las variables de sesión
session_unset();
session_destroy();

// Elimina la cookie 'recordar_token'
setcookie('recordar_token', '', time() - 3600, '/', '', true, true);

// Redirecciona a la página de inicio de sesión u otra página si lo deseas
header("Location: ../../login.php"); // Cambia 'login.php' por la página a la que quieras redirigir

exit(); // Asegura que el script se detenga después de redireccionar
