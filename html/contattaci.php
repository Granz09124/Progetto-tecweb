<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require "internal/contattaci/upload-messaggio.php";
    exit();
}
require "header.php";

renderPage("internal/contattaci/contattaci.html");

?>