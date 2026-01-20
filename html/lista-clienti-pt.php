<?php
require_once 'config.php';

// Test Marco Colombo
$_SESSION['user_id'] = 9; 

$id_pt = $_SESSION['user_id'] ?? 0;
$messaggio = "";
if ($id_pt == 0) { header("Location: home.html"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['scheda_file'])) {
    $id_cliente_target = $_POST['id_cliente'];
    $messaggio = uploadScheda($_FILES['scheda_file'], $id_cliente_target, $id_pt, $conn);
}

$stmtClienti = $conn->prepare("SELECT u.id_utente, u.nome, u.cognome, c.codice_fiscale, c.telefono FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente JOIN Assegnazione_PT apt ON c.id_utente = apt.id_cliente WHERE apt.id_pt = ?");
$stmtClienti->bind_param("i", $id_pt);
$stmtClienti->execute();
$resultClienti = $stmtClienti->get_result();

$top = file_get_contents("internal/utente/top.html");
$body = file_get_contents("internal/utente/lista-clienti-pt/body.html");
$rowTemplate = file_get_contents("internal/utente/lista-clienti-pt/client-row.html");
$bottom = file_get_contents("internal/utente/bottom.html");

$righeHTML = "";
if ($resultClienti->num_rows > 0) {
    while($cliente = $resultClienti->fetch_assoc()) {
        $row = $rowTemplate;
        $row = str_replace("[NomeCliente]", htmlspecialchars($cliente['nome'] . " " . $cliente['cognome']), $row);
        $row = str_replace("[CF]", htmlspecialchars($cliente['codice_fiscale']), $row);
        $row = str_replace("[Telefono]", htmlspecialchars($cliente['telefono']), $row);
        $row = str_replace("[IDCliente]", $cliente['id_utente'], $row);
        $righeHTML .= $row;
    }
} else {
    $righeHTML = "<tr><td colspan='4' class='empty-message'>Non hai clienti assegnati al momento.</td></tr>";
}

$top = str_replace("[PageTitle]", "Lista Clienti PT", $top);
$top = str_replace("[Breadcrumb]", "Ti trovi in: Home >> <a href='utente-pt.php'>Area Personale</a> >> Lista Clienti", $top);
$body = str_replace("[MessaggioFeedback]", $messaggio ? "<div class='feedback-message'>$messaggio</div>" : "", $body);
$body = str_replace("[RigheTabella]", $righeHTML, $body);

echo $top . $body . $bottom;
$stmtClienti->close(); $conn->close();
?>