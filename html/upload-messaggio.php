<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$messageFormData = [
    'nome' => '',
    'email' => '',
    'telefono' => '',
    'messaggio' => ''
];

$getFilteredName = 'getFilteredName';
$getFilteredEmail = 'getFilteredEmail';
$getFilteredPhoneNumber = 'getFilteredPhoneNumber';
$getFilteredMessage = 'getFilteredMessage';

validateInput('nome', $getFilteredName, 'Come ti chiami?', 'Per piacere, inserisci un nome valido.');
validateInput('email', $getFilteredEmail, 'Qual è il tuo indirizzo di posta elettronica?', 'Per piacere, inserisci un indirizzo di posta elettronica valido.');
validateInput('messaggio', $getFilteredMessage, 'Per piacere, inserisci un messaggio più corto di 255 caratteri.', 'Per piacere, inserisci un messaggio più corto di 255 caratteri.');

if (isset($_POST['telefono']) && !empty($_POST['telefono']) && preg_match('/\S/', $_POST['telefono'])) {
    validateInput('telefono', $getFilteredPhoneNumber, 'Per piacere, inserisci un numero di telefono valido.', 'Per piacere, inserisci un numero di telefono valido.');
}

if (http_response_code() == 400) {
    exit;
}

if (empty($messageFormData['telefono'])) {
    $messageFormData['telefono'] = null;
}

require 'db_connection.php';
$stmt = $conn->prepare("INSERT INTO Messaggio_Contattaci (nome, email, telefono, messaggio) VALUES (?, ?, ?, ?)");
    
$stmt->bind_param("ssss", $messageFormData['nome'], $messageFormData['email'], $messageFormData['telefono'], $messageFormData['messaggio']);

if($stmt->execute()) {
    $stmt->close();
} else {
    http_response_code(500);
    echo "Non siamo riusciti a inviare il messaggio. Prova di nuovo tra 15 secondi. Se continua a non funzionare, prova a chiamarci in sede.";
    exit;
}


function validateInput($key, $validator, $notDefinedMsg, $badMsg) {
    global $messageFormData;
    if (!isset($_POST[$key]) || empty($_POST[$key])) {
        setInvalidInput($notDefinedMsg);
    } else {
        $messageFormData[$key] = $validator($_POST[$key]);
        if (empty($messageFormData[$key])) {
            setInvalidInput($badMsg);
        }
    }
}

function setInvalidInput($error) {
    if (http_response_code() != 400) {
        http_response_code(400);
    }
    echo $error;
}

function getFilteredName($name) {
    $name = trim($name);
    $name = strip_tags($name);
    if (strlen($name) == 0 || strlen($name) > 50)
        return '';

    if(preg_match("/\d/", $name))
        return '';

    return $name;
}

function getFilteredEmail($email) {
    $email = trim($email);
    $email = strip_tags($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '';
    }
    if (strlen($email) > 100) {
        return '';
    }
    return $email;
}

function getFilteredPhoneNumber($phoneNumber) {
    if (preg_match('/[^\d+\s]/', $phoneNumber)) {
        return '';
    }
    $phoneNumber = preg_replace('/[\s-]/', '', $phoneNumber);
    
    if (strlen($phoneNumber) > 20) {
        return '';
    }
    return $phoneNumber;
}

function getFilteredMessage($msg) {
    $msg = trim($msg);
    $msg = strip_tags($msg);
    if (strlen($msg) > 255) { // non necessario
        return '';
    }
    return $msg;
}