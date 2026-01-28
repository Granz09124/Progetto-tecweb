<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require "internal/contattaci/upload-messaggio.php";
    exit();
}
include "header.php";

renderPage("internal/contattaci/contattaci.html");

?>