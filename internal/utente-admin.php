<?php
session_start();
require_once __DIR__ . '/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: error403.php");
    exit;
}

require __DIR__ . "/../internal/header.php";

$id_admin = $_SESSION['user_id']; 

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_credenziali') {
    $new_email = trim($_POST['email']);
    $new_pass = !empty($_POST['password']) ? $_POST['password'] : null;
    
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $messaggio = "Email non valida.";
    } else {
        try {
            if ($new_pass) {
                $password_hash = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
                $stmt->bind_param("ssi", $new_email, $password_hash, $id_admin);
            } else {
                $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
                $stmt->bind_param("si", $new_email, $id_admin);
            }
            
            if ($stmt->execute()) {
                $messaggio = "Credenziali aggiornate con successo!";
            } else {
                $messaggio = "Errore durante l'aggiornamento (Email già in uso?).";
            }
            $stmt->close();
        } catch (Exception $e) {
            $messaggio = "Errore tecnico: " . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT email FROM Utente WHERE id_utente = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();
$currentEmail = $adminData['email'] ?? '';
$stmt->close();

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/utente-admin/body.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Palestra - Area Utente Admin", $top);
$breadcrumb = '<li><a lang="en" href="./home.php">Home</a></li>';
$breadcrumb .= '<li aria-current="page">Area Personale</li>';
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$messaggioHtml = $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "";
$body = str_replace("[MessaggioFeedback]", $messaggioHtml, $body);
$body = str_replace("[Email]", htmlspecialchars($currentEmail), $body);

renderFromHtml($top . $body . $bottom);

$conn->close();
?>