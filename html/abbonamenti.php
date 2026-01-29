<?php
session_start();
require 'header.php';

renderPage('internal/abbonamenti/top.html');
renderPage('internal/abbonamenti/body.html');
renderPage('internal/abbonamenti/bottom.html');

?>