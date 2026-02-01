<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'cliente') {
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

            $stmt = $conn->prepare("UPDATE Cliente SET telefono = ? WHERE id_utente = ?");
            $stmt->bind_param("si", $new_phone, $id_utente);
            
            if ($stmt->execute()) {
                $messaggio = "Dati aggiornati con successo!";
            } else {
                $messaggio = "Errore durante l'aggiornamento.";
            }
            $stmt->close();
            
        } catch (Exception $e) {
            $messaggio = "Errore tecnico: " . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT u.*, c.telefono, c.codice_fiscale FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente WHERE u.id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$userData) {
    die("Errore: Impossibile recuperare il profilo utente.");
}

$stmtSchede = $conn->prepare("SELECT s.*, u.nome as nome_pt FROM Scheda_Allenamento s LEFT JOIN Utente u ON s.id_pt = u.id_utente WHERE s.id_cliente = ? ORDER BY s.data_caricamento DESC");
$stmtSchede->bind_param("i", $id_utente);
$stmtSchede->execute();
$resultSchede = $stmtSchede->get_result();

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/utente-semplice/body.html");
$itemTemplate = file_get_contents(__DIR__ . "/../internal/utente/utente-semplice/scheda-item.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

$lista = "";
if ($resultSchede->num_rows > 0) {
    while($scheda = $resultSchede->fetch_assoc()) {
        $item = $itemTemplate;
        $item = str_replace("[NomeFile]", htmlspecialchars($scheda['nome_file']), $item);

        $nomePT = $scheda['nome_pt'] ? $scheda['nome_pt'] : "Sistema"; 
        $item = str_replace("[NomePT]", htmlspecialchars($nomePT), $item);
        $item = str_replace("[PercorsoFile]", htmlspecialchars($scheda['percorso_file']), $item);
        $lista .= $item;
    }
} else {
    $lista = "<p class='empty-message'>Non hai ancora schede di allenamento caricate.</p>";
}

$top = str_replace("[PageTitle]", "Area Utente - " . htmlspecialchars($userData['nome']), $top);
$breadcrumb = "Ti trovi in: <a href='./home.php'>Home</a> >> Area Personale";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[Nome]", htmlspecialchars($userData['nome']), $body);
$body = str_replace("[Cognome]", htmlspecialchars($userData['cognome']), $body);
$messaggioHtml = $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "";
$body = str_replace("[MessaggioFeedback]", $messaggioHtml, $body);

$body = str_replace("[ListaSchede]", $lista, $body);
$body = str_replace("[Email]", htmlspecialchars($userData['email']), $body);
$body = str_replace("[Telefono]", htmlspecialchars($userData['telefono']), $body);

renderFromHtml($top . $body . $bottom);

$stmtSchede->close();
$conn->close();
?>