<?php
require_once 'config.php';

// Test Mario Rossi
$_SESSION['user_id'] = 3; 

$id_utente = $_SESSION['user_id'] ?? 0;
$messaggio = "";

if ($id_utente == 0) { header("Location: home.html"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $new_email = $_POST['email']; $new_phone = $_POST['telefono'];
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
        $stmt = $conn->prepare("UPDATE Cliente SET telefono = ? WHERE id_utente = ?");
        $stmt->bind_param("si", $new_phone, $id_utente);
        $stmt->execute(); $stmt->close();
        $messaggio = "Dati aggiornati!";
    } catch (Exception $e) { $messaggio = "Errore: " . $e->getMessage(); }
}

$stmt = $conn->prepare("SELECT u.*, c.telefono, c.codice_fiscale FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente WHERE u.id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmtSchede = $conn->prepare("SELECT s.*, u.nome as nome_pt FROM Scheda_Allenamento s LEFT JOIN Utente u ON s.id_pt = u.id_utente WHERE s.id_cliente = ? ORDER BY s.data_caricamento DESC");
$stmtSchede->bind_param("i", $id_utente);
$stmtSchede->execute();
$resultSchede = $stmtSchede->get_result();

$top = file_get_contents("internal/utente/top.html");
$body = file_get_contents("internal/utente/utente-semplice/body.html");
$itemTemplate = file_get_contents("internal/utente/utente-semplice/scheda-item.html");
$bottom = file_get_contents("internal/utente/bottom.html");

$lista = "";
if ($resultSchede->num_rows > 0) {
    while($scheda = $resultSchede->fetch_assoc()) {
        $item = $itemTemplate;
        $item = str_replace("[NomeFile]", htmlspecialchars($scheda['nome_file']), $item);
        $item = str_replace("[NomePT]", htmlspecialchars($scheda['nome_pt']), $item);
        $item = str_replace("[PercorsoFile]", htmlspecialchars($scheda['percorso_file']), $item);
        $lista .= $item;
    }
} else {
    $lista = "<li><span>Nessuna scheda presente.</span></li>";
}

$top = str_replace("[PageTitle]", "Area Utente", $top);
$breadcrumb = "Ti trovi in: <a href='./home.html'>Home</a> >> Area Personale";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[Nome]", htmlspecialchars($userData['nome']), $body);
$body = str_replace("[Cognome]", htmlspecialchars($userData['cognome']), $body);
$body = str_replace("[MessaggioFeedback]", $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "", $body);
$body = str_replace("[ListaSchede]", $lista, $body);
$body = str_replace("[Email]", htmlspecialchars($userData['email']), $body);
$body = str_replace("[Telefono]", htmlspecialchars($userData['telefono']), $body);

echo $top . $body . $bottom;
$stmtSchede->close(); $conn->close();
?>