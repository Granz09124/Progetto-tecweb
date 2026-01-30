<?php
session_start();
require __DIR__ . '/../internal/header.php';

renderPage(__DIR__ . '/../internal/palestra1/top.html');
renderPage(__DIR__ . '/../internal/palestra1/body.html');
renderPage(__DIR__ . '/../internal/palestra1/bottom.html');
?>