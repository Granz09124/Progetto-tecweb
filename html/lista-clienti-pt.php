<?php
require_once 'db_connection.php';

// if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'pt') {
//     header('Location: /login.html');
//     exit;
// }


// Test Marco Colombo
$_SESSION['user_id'] = 9;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require "internal/utente/lista-clienti-pt/upload-scheda.php";
    exit();
}

$id_pt = $_SESSION['user_id'] ?? 0;
$messaggio = "";
if ($id_pt == 0) { header("Location: home.html"); exit; }

$stmtClienti = $conn->prepare("SELECT u.id_utente, u.nome, u.cognome, c.codice_fiscale, c.telefono FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente JOIN Assegnazione_PT apt ON c.id_utente = apt.id_cliente WHERE apt.id_pt = ?");
$stmtClienti->bind_param("i", $id_pt);
$stmtClienti->execute();
$resultClienti = $stmtClienti->get_result();

$top = file_get_contents("internal/utente/top.html");
$body = file_get_contents("internal/utente/lista-clienti-pt/body.html");
$tableTemplate = file_get_contents("internal/utente/lista-clienti-pt/table-template.html");
$rowTemplate = file_get_contents("internal/utente/lista-clienti-pt/client-row.html");
$bottom = file_get_contents("internal/utente/bottom.html");


if ($resultClienti->num_rows > 0) {
    $righeHTML = $tableTemplate;
    while($cliente = $resultClienti->fetch_assoc()) {
        $row = $rowTemplate;
        $row = str_replace("[NomeCliente]", htmlspecialchars($cliente['nome'] . " " . $cliente['cognome']), $row);
        $row = str_replace("[CF]", htmlspecialchars($cliente['codice_fiscale']), $row);
        $row = str_replace("[Telefono]", htmlspecialchars($cliente['telefono']), $row);
        $row = str_replace("[IDCliente]", $cliente['id_utente'], $row);
        $righeHTML = str_replace('[RigheTabella]', $row . '[RigheTabella]', $righeHTML);
    }
    $righeHTML = str_replace('[RigheTabella]', '', $righeHTML);
} else {
    $righeHTML = "<tr><td colspan='4' class='empty-message'>Non hai clienti assegnati al momento.</td></tr>";
}

$body = str_replace("[ListaClienti]", $righeHTML, $body);

echo $top . $body . $bottom;
$stmtClienti->close(); $conn->close();
?>