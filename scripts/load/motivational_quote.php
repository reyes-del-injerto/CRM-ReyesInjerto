<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // TS

require_once __DIR__ . '/../connection_db.php';

$quote = "";
$author = "";

$sql = "SELECT quote,author FROM u_motivational_quotes WHERE day = CURDATE();";
$query = $conn->query($sql);

if ($query) {
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $quote = $row['quote'];
        $author = $row['author'];
    }
}
