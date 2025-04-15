<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

//PRUEBAS LOCAL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "injertoprueba";

//Produccion
/* $servername = "localhost";
$username = "root";
$password = "developers2024";
$dbname = "cdmx3"; */

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
