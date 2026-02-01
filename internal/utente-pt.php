<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'pt') {
    header("Location: login.php");
    exit;
}

require __DIR__ . "/../internal/header.php";

$id_utente = $_SESSION['user_id'];
$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['telefono']);
    $new_pass = !empty($_POST['password']) ? $_POST['password'] : null;

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $messaggio = "Formato email non valido.";
    } else {
        try {
            if ($new_pass) {
                $password_hash = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
                $stmt->bind_param("ssi", $new_email, $password_hash, $id_utente);
            } else {
                $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
                $stmt->bind_param("si", $new_email, $id_utente);
            }
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE Istruttore SET telefono = ? WHERE id_utente = ?");
            $stmt->bind_param("si", $new_phone, $id_utente);
            
            if ($stmt->execute()) {
                $messaggio = "Dati aggiornati con successo!";
            } else {
                $messaggio = "Errore nell'aggiornamento dei dati.";
            }
            $stmt->close();
            
        } catch (Exception $e) {
            $messaggio = "Errore tecnico: " . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT u.*, i.telefono, i.specializzazione, i.qualifica FROM Utente u JOIN Istruttore i ON u.id_utente = i.id_utente WHERE u.id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
    die("Errore: Impossibile recuperare il profilo istruttore.");
}

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/utente-pt/body.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Area PT - " . htmlspecialchars($userData['nome']), $top);
$breadcrumb = "Ti trovi in: <a href='./home.php'>Home</a> >> Area Personale";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[NomeCompleto]", htmlspecialchars($userData['nome'] . " " . $userData['cognome']), $body);
$messaggioHtml = $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "";
$body = str_replace("[MessaggioFeedback]", $messaggioHtml, $body);

$body = str_replace("[Nome]", htmlspecialchars($userData['nome']), $body);
$body = str_replace("[Cognome]", htmlspecialchars($userData['cognome']), $body);
$body = str_replace("[Qualifica]", htmlspecialchars($userData['qualifica'] ?? ''), $body);
$body = str_replace("[Specializzazione]", htmlspecialchars($userData['specializzazione'] ?? ''), $body);
$body = str_replace("[Email]", htmlspecialchars($userData['email']), $body);
$body = str_replace("[Telefono]", htmlspecialchars($userData['telefono'] ?? ''), $body);

renderFromHtml($top . $body . $bottom);

$conn->close();
?>