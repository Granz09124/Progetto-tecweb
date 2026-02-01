<?php
session_start();
require_once __DIR__ . '/../../internal/header.php';

// tutte le pagine proprie dell'amministratore restituiscono un 404
// tutte le pagine utente restituiscono un 403 se non autenticato
http_response_code(301);

renderPage("error/403.html");

?>