<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['db'])) {
    exit;
}

$host = "db";
$user = "root";
$pass = "example";
$db   = "palestra_db"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$_SESSION['db'] = $conn;
