<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}


function redirect_based_on_type() {
    if (!isset($_SESSION['user_tipo'])) {
        header("Location: login.php");
        exit();
    }

    switch ($_SESSION['user_tipo']) {
        case 'admin':
            header("Location: utente-admin.php");
            break;
        case 'pt':
            header("Location: utente-pt.php");
            break;
        default:
            header("Location: utente-semplice.php");
            break;
    }
    exit();
}

if (isset($_SESSION['user_id'])) {
    redirect_based_on_type(); // Se già loggato, reindirizza
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

if (!$email) {
    $_SESSION['errori_login'] = ["Inserisci un'email valida."];
    header("Location: login.html");
    exit();
}


$stmt = $conn->prepare("SELECT id_utente, password_hash, nome, cognome FROM Utente WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        
        $id_utente = $user['id_utente'];

        // Default (Non faccio check istruttore perchè viene trattato allo stesso modo per semplicità)
        $tipo_utente = 'cliente'; 
        
        // Controlla se è Admin
        $stmt_admin = $conn->prepare("SELECT 1 FROM Admin WHERE id_utente = ?");
        $stmt_admin->bind_param("i", $id_utente);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();
        
        if ($result_admin->num_rows > 0) {
            $tipo_utente = 'admin';
        } else {
            // Controlla se è Pt
            $stmt_pt = $conn->prepare("SELECT 1 FROM Personal_Trainer WHERE id_istruttore = ?");
            $stmt_pt->bind_param("i", $id_utente);
            $stmt_pt->execute();
            $result_pt = $stmt_pt->get_result();
            
            if ($result_pt->num_rows > 0) {
                $tipo_utente = 'pt';
            } 
        }
        
        // Rigenera il session id per sicurezza
        session_regenerate_id(true);

        // Salva in sessione
        $_SESSION['user_id'] = $user['id_utente'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_cognome'] = $user['cognome'];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_tipo'] = $tipo_utente; // 'admin', 'pt' o 'cliente' usato per capire se può accedere a certe pagine

        // Reindirizza in base al tipo di utente
        redirect_based_on_type();
}

$_SESSION['errori_login'] = ["Email o password non corretti."];
echo $_SESSION['errori_login'][0];
header("Location: login.html");
exit();
?>
