<?php
session_start();
require_once __DIR__ . '/../internal/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'pt') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require __DIR__ . "/../internal/utente/lista-clienti-pt/upload-scheda.php";
    exit();
}

$id_pt = $_SESSION['user_id'] ?? 0;
if ($id_pt == 0) { header("Location: home.php"); exit; }

$stmtClienti = $conn->prepare("SELECT u.id_utente, u.nome, u.cognome, c.codice_fiscale, c.telefono FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente JOIN Assegnazione_PT apt ON c.id_utente = apt.id_cliente WHERE apt.id_pt = ?");
$stmtClienti->bind_param("i", $id_pt);
$stmtClienti->execute();
$resultClienti = $stmtClienti->get_result();

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/lista-clienti-pt/body.html");
$tableTemplate = file_get_contents(__DIR__ . "/../internal/utente/lista-clienti-pt/table-template.html");
$rowTemplate = file_get_contents(__DIR__ . "/../internal/utente/lista-clienti-pt/client-row.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

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
$top = str_replace("[PageTitle]", "I Miei Clienti - Area PT", $top);

$breadcrumb = "Ti trovi in: <a href='./home.php'>Home</a> >> <a href='area-personale.php'>Area Personale</a> >> I Miei Clienti";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

require_once __DIR__ . '/../internal/header.php';
renderFromHtml($top . $body . $bottom);

$stmtClienti->close(); $conn->close();
?>