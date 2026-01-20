<?php
$host = "db";
$user = "root";
$pass = "example";
$db   = "palestra_db"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
