<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo 'b';
    require __DIR__ . "/../internal/contattaci/upload-messaggio.php";
    echo 'a';
    exit();
}
require __DIR__ . "/../internal/header.php";

renderPage(__DIR__ . "/../internal/contattaci/contattaci.html");

?>