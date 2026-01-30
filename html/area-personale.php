<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php"); 
    exit;
}

$ruolo = $_SESSION['user_tipo'] ?? '';

switch ($ruolo) {
    case 'admin':
        require __DIR__ . '/../internal/utente-admin.php';
        break;

    case 'pt':
        require __DIR__ . '/../internal/utente-pt.php';
        break;

    case 'cliente':
        require __DIR__ . '/../internal/utente-semplice.php';
        break;

    default:
        session_destroy();
        echo "Errore: Ruolo utente non riconosciuto. Contattare l'assistenza.";
        echo "<br><a href='home.php'>Torna alla Home</a>";
        exit;
}
?>