<?php
session_start();

$_SESSION = array();

session_destroy();

echo 'home.html';
exit();
?>