<?php
session_start();
require __DIR__ . '/../internal/header.php';

renderPage(__DIR__ . '/../internal/abbonamenti/top.html');
renderPage(__DIR__ . '/../internal/abbonamenti/body.html');
renderPage(__DIR__ . '/../internal/abbonamenti/bottom.html');
?>