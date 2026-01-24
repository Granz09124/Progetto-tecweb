<?php
require_once 'config.php';

// Controllo accesso
/*
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: error403.html");
    exit;
}
*/

// TEST
$id_admin = 1; 

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_credenziali') {
    $new_email = $_POST['email'];
    $new_pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    
    try {
        if ($new_pass) {
            $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssi", $new_email, $new_pass, $id_admin);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
            $stmt->bind_param("si", $new_email, $id_admin);
        }
        
        if ($stmt->execute()) {
            $messaggio = "Credenziali aggiornate con successo!";
        } else {
            $messaggio = "Errore durante l'aggiornamento: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $messaggio = "Errore: " . $e->getMessage();
    }
}

$stmt = $conn->prepare("SELECT email FROM Utente WHERE id_utente = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();
$currentEmail = $adminData['email'] ?? 'admin@email.it';
$stmt->close();

$top = file_get_contents("internal/utente/top.html");
$body = file_get_contents("internal/utente/utente-admin/body.html");
$bottom = file_get_contents("internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Palestra - Area Utente Admin", $top);
$breadcrumb = "Ti trovi in: <a href='./home.html'>Home</a> >> Area Amministrazione";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$messaggioHtml = $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "";
$body = str_replace("[MessaggioFeedback]", $messaggioHtml, $body);

$body = str_replace("[Email]", htmlspecialchars($currentEmail), $body);

echo $top . $body . $bottom;
$conn->close();
?>