<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: registrati.html');
    exit();
}

require_once 'db_connection.php';

$nome = trim($_POST['nome'] ?? '');
$cognome = trim($_POST['cognome'] ?? '');
$codice_fiscale = strtoupper(trim($_POST['codice-fiscale'] ?? ''));
$email = trim($_POST['email'] ?? '');
$password = $_POST['new-password'] ?? '';
$confirm_password = $_POST['confirm-password'] ?? '';

$errori = [];

if (empty($nome) || strlen($nome) < 2) {
    $errori[] = "Il nome deve contenere almeno 2 caratteri.";
} elseif (!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $nome)) {
    $errori[] = "Il nome contiene caratteri non validi. Sono ammessi solo lettere, spazi, apostrofi e trattini.";
} elseif (strlen($nome) > 50) {
    $errori[] = "Il nome è troppo lungo (massimo 50 caratteri).";
}

if (empty($cognome) || strlen($cognome) < 2) {
    $errori[] = "Il cognome deve contenere almeno 2 caratteri.";
} elseif (!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $cognome)) {
    $errori[] = "Il cognome contiene caratteri non validi. Sono ammessi solo lettere, spazi, apostrofi e trattini.";
} elseif (strlen($cognome) > 50) {
    $errori[] = "Il cognome è troppo lungo (massimo 50 caratteri).";
}

if (empty($email)) {
    $errori[] = "L'email è obbligatoria.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errori[] = "Formato email non valido.";
} elseif (strlen($email) > 100) {
    $errori[] = "L'email è troppo lunga (massimo 100 caratteri).";
}

if (empty($codice_fiscale)) {
    $errori[] = "Il codice fiscale è obbligatorio.";
} elseif (!preg_match("/^[A-Z0-9]{16}$/", $codice_fiscale)) {
    $errori[] = "Il codice fiscale deve essere di esattamente 16 caratteri alfanumerici.";
}

if (empty($password)) {
    $errori[] = "La password è obbligatoria.";
} elseif (strlen($password) < 8) {
    $errori[] = "La password deve essere di almeno 8 caratteri.";
} elseif (!preg_match("/^[\x20-\x7E]+$/", $password)) {
    $errori[] = "La password contiene caratteri non validi.";
}

if ($password !== $confirm_password) {
    $errori[] = "Le password non coincidono.";
}

if (!empty($errori)) {
    http_response_code(400);
    echo $errori[0];
    exit();
}

// Controlla se l'email esiste già
$checkEmail = $conn->prepare("SELECT email FROM Utente WHERE email = ?");
if (!$checkEmail) {
    http_response_code(500);
    echo "Si è verificato un errore tecnico. Riprova più tardi.";
    exit();
}

$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$resultEmail = $checkEmail->get_result();

if ($resultEmail->num_rows > 0) {
    http_response_code(400);
    echo "Questa email è già registrata.";
    $checkEmail->close();
    exit();
}
$checkEmail->close();

// Controlla se il codice fiscale esiste già
$checkCF = $conn->prepare("SELECT codice_fiscale FROM Cliente WHERE codice_fiscale = ?");
if (!$checkCF) {
    http_response_code(500);
    echo "Si è verificato un errore tecnico. Riprova più tardi.";
    exit();
}

$checkCF->bind_param("s", $codice_fiscale);
$checkCF->execute();
$resultCF = $checkCF->get_result();

if ($resultCF->num_rows > 0) {
    http_response_code(400);
    echo "Questo codice fiscale è già registrato.";
    $checkCF->close();
    exit();
}
$checkCF->close();

// Hash della password
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);


$conn->begin_transaction();

try {
    // Inserisce nella tabella Utente
    $stmt = $conn->prepare("INSERT INTO Utente (nome, cognome, email, password_hash) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Errore preparazione query utente: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $nome, $cognome, $email, $password_hash);
    
    if (!$stmt->execute()) {
        throw new Exception("Errore inserimento utente: " . $stmt->error);
    }
    
    $id_utente = $stmt->insert_id;
    $stmt->close();
    
    // Inserisce nella tabella Cliente
    $stmt_cliente = $conn->prepare("INSERT INTO Cliente (id_utente, codice_fiscale) VALUES (?, ?)");
    if (!$stmt_cliente) {
        throw new Exception("Errore preparazione query cliente: " . $conn->error);
    }
    
    $stmt_cliente->bind_param("is", $id_utente, $codice_fiscale);
    
    if (!$stmt_cliente->execute()) {
        throw new Exception("Errore creazione profilo cliente: " . $stmt_cliente->error);
    }
    
    $stmt_cliente->close();
    

    $conn->commit();
    
    http_response_code(200);
    echo "Success";
    exit();
    
} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    
    http_response_code(500);

    echo "Si è verificato un errore tecnico. Riprova più tardi.";
    exit();
}
?>