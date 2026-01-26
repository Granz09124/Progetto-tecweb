<?php
session_start();

$content = file_get_contents("internal/home/template.html");

$menuFile = isset($_SESSION['user_id'])
    ? "internal/home/menu-user.html"
    : "internal/home/menu-guest.html";
$menuContent = file_get_contents($menuFile);

$content = str_replace('[Menu]', $menuContent, $content);

echo $content;