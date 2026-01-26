<?php
require_once 'config.php';

// =============================================================
// TEST DA TOGLIERE DOPO
// =============================================================
// $ruolo_test = 'admin'; 

// if ($ruolo_test === 'admin') {
//     $_SESSION['user_id'] = 1;
//     $_SESSION['user_tipo'] = 'admin';
//     $_SESSION['user_nome'] = 'Admin';
// } 
// elseif ($ruolo_test === 'pt') {
//     $_SESSION['user_id'] = 9;
//     $_SESSION['user_tipo'] = 'pt';
//     $_SESSION['user_nome'] = 'Marco';
//     $_SESSION['user_cognome'] = 'Colombo';
// } 
// elseif ($ruolo_test === 'cliente') {
//     $_SESSION['user_id'] = 3;
//     $_SESSION['user_tipo'] = 'cliente';
//     $_SESSION['user_nome'] = 'Mario';
//     $_SESSION['user_cognome'] = 'Rossi';
// }
// =============================================================

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