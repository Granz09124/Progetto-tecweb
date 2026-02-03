<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: area-personale.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/../internal/registrati/processa-registrazione.php";
    exit();
}

require __DIR__ . '/../internal/header.php';

renderPage(__DIR__ . "/../internal/registrati/registrati.html");

?>