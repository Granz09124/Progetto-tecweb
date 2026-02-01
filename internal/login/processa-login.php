<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: /login.php');
    exit();
}

if (isset($_SESSION['user_id'])) {
    http_response_code(200);
    header('Location: /area-personale.php');  
    exit();
}
    
require __DIR__ . '/../db_connection.php';


$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errori = [];

if (empty($email)) {
    $errori[] = "Inserisci un'email valida.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email !== 'admin' && $email !== 'pt' && $email !== 'user') {
    $errori[] = "Formato email non valido.";
}

if (empty($password)) {
    $errori[] = "Inserisci la password.";
}

if (!empty($errori)) {
    http_response_code(400);
    echo $errori[0];
    exit();
}

$stmt = $conn->prepare("SELECT id_utente, password_hash, nome, cognome FROM Utente WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        
        $id_utente = $user['id_utente'];

        // Default
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
        
        // Per sicurezza
        session_regenerate_id(true);

        // Salva in sessione
        $_SESSION['user_id'] = $user['id_utente'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_cognome'] = $user['cognome'];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_tipo'] = $tipo_utente;

        
        http_response_code(200);
        echo "Success";
        exit();
    }
}

http_response_code(400);
echo "Email o password non corretti.";
exit();
?>