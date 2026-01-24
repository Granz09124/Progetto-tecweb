<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require "internal/contattaci/upload-messaggio.php";
    exit();
}

include "internal/contattaci/contattaci.html";
