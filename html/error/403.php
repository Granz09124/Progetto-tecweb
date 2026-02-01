<?php
session_start();
require_once __DIR__ . '/../../internal/header.php';

$basePath = dirname($_SERVER['SCRIPT_NAME']);
$basePath = '/' . trim($basePath, '/') . '/';

$errorPage = file_get_contents(__DIR__ . "/../../internal/error/403.html");
$errorPage = str_replace('[Base]', $basePath, $errorPage);

http_response_code(301);

renderFromHtml($errorPage);

?>