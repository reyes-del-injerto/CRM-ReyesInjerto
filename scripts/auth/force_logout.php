<?php

session_start(); // Inicia la sesión

// Destruye todas las variables de sesión
session_unset();
session_destroy();

// Elimina la cookie 'recordar_token'
setcookie('recordar_token', '', time() - 3600, '/', '', true, true);

header("Location: https://lrdi.online/cdmx/force_logout.php"); // Cambia 'login.php' por la página a la que quieras redirigir


?>