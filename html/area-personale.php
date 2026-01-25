<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.html"); 
    exit;
}

$ruolo = $_SESSION['user_tipo'] ?? '';

switch ($ruolo) {
    case 'admin':
        require 'internal/utente-admin.php';
        break;

    case 'pt':
        require 'internal/utente-pt.php';
        break;

    case 'cliente':
        require 'internal/utente-semplice.php';
        break;

    default:
        session_destroy();
        echo "Errore: Ruolo utente non riconosciuto. Contattare l'assistenza.";
        echo "<br><a href='home.html'>Torna alla Home</a>";
        exit;
}
?>