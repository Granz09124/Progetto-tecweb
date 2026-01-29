<?php
session_start();
require '../header.php';

// tutte le pagine proprie dell'amministratore restituiscono un 404
// tutte le pagine utente restituiscono un 403 se non autenticato
http_response_code(404);

renderPage("internal/error/404.html");

?>