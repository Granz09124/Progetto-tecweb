<?php
require_once 'config.php';

// Test Marco Colombo
$_SESSION['user_id'] = 9; 

$id_utente = $_SESSION['user_id'] ?? 0;
$messaggio = "";

if ($id_utente == 0) { header("Location: home.html"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $new_email = $_POST['email']; 
    $new_phone = $_POST['telefono'];
    $new_pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    try {
        if ($new_pass) {
            $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssi", $new_email, $new_pass, $id_utente);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
            $stmt->bind_param("si", $new_email, $id_utente);
        }
        $stmt->execute(); $stmt->close();
        $stmt = $conn->prepare("UPDATE Istruttore SET telefono = ? WHERE id_utente = ?");
        $stmt->bind_param("si", $new_phone, $id_utente);
        $stmt->execute(); $stmt->close();
        $messaggio = "Dati aggiornati!";
    } catch (Exception $e) { $messaggio = "Errore: " . $e->getMessage(); }
}

$stmt = $conn->prepare("SELECT u.*, i.telefono, i.specializzazione, i.qualifica FROM Utente u JOIN Istruttore i ON u.id_utente = i.id_utente WHERE u.id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$top = file_get_contents("internal/utente/top.html");
$body = file_get_contents("internal/utente/utente-pt/body.html");
$bottom = file_get_contents("internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Area PT - " . $userData['nome'], $top);
$top = str_replace("[Breadcrumb]", "Ti trovi in: <a href='./home.html'>Home</a> >> Area Personale", $top);

$body = str_replace("[NomeCompleto]", htmlspecialchars($userData['nome'] . " " . $userData['cognome']), $body);
$body = str_replace("[MessaggioFeedback]", $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "", $body);
$body = str_replace("[Nome]", htmlspecialchars($userData['nome']), $body);
$body = str_replace("[Cognome]", htmlspecialchars($userData['cognome']), $body);
$body = str_replace("[Qualifica]", htmlspecialchars($userData['qualifica'] ?? ''), $body);
$body = str_replace("[Specializzazione]", htmlspecialchars($userData['specializzazione'] ?? ''), $body);
$body = str_replace("[Email]", htmlspecialchars($userData['email']), $body);
$body = str_replace("[Telefono]", htmlspecialchars($userData['telefono'] ?? ''), $body);

echo $top . $body . $bottom;
$conn->close();
?>