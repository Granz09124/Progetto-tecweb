<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $data_nascita = $_POST['data-nascita'];
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];
    
    $errori = [];


    // Validazioni varie
    if (empty($nome) || strlen($nome) < 2) {
        $errori[] = "Il nome deve contenere almeno 2 caratteri.";
    }
    
    if (empty($cognome) || strlen($cognome) < 2) {
        $errori[] = "Il cognome deve contenere almeno 2 caratteri.";
    }
    
    if (!$email) {
        $errori[] = "Formato email non valido.";
    }
    
    if (strlen($password) < 8) {
        $errori[] = "La password deve essere di almeno 8 caratteri.";
    }
    
    if ($password !== $confirm_password) {
        $errori[] = "Le password non coincidono.";
    }
    
    // Validazione data di nascita
    if (!empty($data_nascita)) {
        $oggi = new DateTime();
        $nascita = new DateTime($data_nascita);
        $eta = $nascita->diff($oggi)->y;
        
        if ($eta < 16) {
            $errori[] = "Devi avere almeno 16 anni per registrarti.";
        }
        if ($eta > 100) {
            $errori[] = "Data di nascita non valida.";
        }
    }

    if (!empty($errori)) {
        $_SESSION['errori_registrazione'] = $errori;
        $_SESSION['dati_form'] = [
            'nome' => $nome,
            'cognome' => $cognome,
            'data_nascita' => $data_nascita,
            'email' => $_POST['email']
        ];
        header("Location: registrati.html");
        exit();
    }

    // Controlla se l'email esiste già
    $checkEmail = $conn->prepare("SELECT email FROM Utente WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    if ($checkEmail->get_result()->num_rows > 0) {
        $_SESSION['errori_registrazione'] = ["Questa email è già registrata."];
        $_SESSION['dati_form'] = ['email' => $_POST['email']];
        header("Location: registrati.html");
        exit();
    }

    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    
    $conn->begin_transaction();

    try {
        //Aggiunge alla tabella utente
        $stmt = $conn->prepare("INSERT INTO Utente (nome, cognome, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $cognome, $email, $password_hash);
        
        if (!$stmt->execute()) {
            throw new Exception("Errore inserimento utente: " . $stmt->error);
        }
        
        // Prende l'ID appena creato
        $id_utente = $stmt->insert_id;
        
        //Aggiunge alla tabelli cliente
        $stmt_cliente = $conn->prepare("INSERT INTO Cliente (id_utente) VALUES (?)");
        $stmt_cliente->bind_param("i", $id_utente);
        
        if (!$stmt_cliente->execute()) {
            throw new Exception("Errore creazione profilo cliente: " . $stmt_cliente->error);
        }
        
        $conn->commit();
        
        $_SESSION['successo'] = "Registrazione completata! Ora puoi effettuare il login come cliente.";
        header("Location: login.html");
        exit();
        
    } catch (Exception $e) {
        // Se c'è un errore annulla tutto (Fa anche check per input troppo lunghi)
        $conn->rollback();
        
        error_log("Errore registrazione: " . $e->getMessage());
        echo $e->getMessage();
        $_SESSION['errori_registrazione'] = ["Si è verificato un errore tecnico. Riprova più tardi."];
        header("Location: registrati.html");
        exit();
    }
}
?>